#!/usr/bin/env python3
"""Live, read-only MCP smoke test against this checkout's running DDEV service."""
import json
import os
from pathlib import Path
import selectors
import signal
import subprocess
import tempfile
import time

ROOT = Path(__file__).resolve().parent.parent


def main():
    with tempfile.TemporaryFile() as errors:
        process = subprocess.Popen(['bash', str(ROOT/'scripts/serena-mcp.sh')],
                                   stdin=subprocess.PIPE, stdout=subprocess.PIPE,
                                   stderr=errors, start_new_session=True)
        selector = selectors.DefaultSelector()
        selector.register(process.stdout, selectors.EVENT_READ)
        buffer = b''
        counter = 0

        def send(message):
            process.stdin.write(json.dumps(message).encode()+b'\n')
            process.stdin.flush()

        def request(method, params):
            nonlocal counter, buffer
            counter += 1
            send({'jsonrpc': '2.0', 'id': counter, 'method': method, 'params': params})
            deadline = time.monotonic()+180
            while time.monotonic() < deadline:
                if b'\n' not in buffer:
                    if not selector.select(timeout=1):
                        continue
                    chunk = os.read(process.stdout.fileno(), 65536)
                    if not chunk:
                        raise RuntimeError('Serena closed stdout before replying')
                    buffer += chunk
                    continue
                line, buffer = buffer.split(b'\n', 1)
                reply = json.loads(line)  # Banners on stdout are protocol failures.
                if reply.get('id') == counter:
                    if 'error' in reply:
                        raise RuntimeError(str(reply['error']))
                    result = reply['result']
                    if result.get('isError'):
                        raise RuntimeError(str(result))
                    return result
            raise TimeoutError('MCP response exceeded 180 seconds')

        try:
            request('initialize', {'protocolVersion': '2024-11-05', 'capabilities': {},
                                  'clientInfo': {'name': 'drupalphprust-smoke', 'version': '1'}})
            send({'jsonrpc': '2.0', 'method': 'notifications/initialized'})
            request('tools/call', {'name': 'initial_instructions', 'arguments': {}})
            tools = request('tools/list', {})['tools']
            names = {tool['name'] for tool in tools}
            assert 'get_symbols_overview' in names, names
            result = request('tools/call', {'name': 'get_symbols_overview', 'arguments': {
                'relative_path': 'php-rust/crates/php-types/src/zval.rs', 'depth': 0}})
            assert 'Zval' in json.dumps(result), result
            print('PASS: MCP initialise, tools/list and Rust symbol lookup (Zval)')
        finally:
            process.stdin.close()
            try:
                process.wait(timeout=15)
            except subprocess.TimeoutExpired:
                os.killpg(process.pid, signal.SIGTERM)
                process.wait(timeout=15)
            selector.close()


if __name__ == '__main__':
    main()
