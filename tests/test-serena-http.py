#!/usr/bin/env python3
"""Live MCP smoke from the DDEV web container; never print session identifiers."""
import json
import subprocess

session = None
counter = 0


def request(method, params=None, notification=False):
    global session, counter
    message = {'jsonrpc': '2.0', 'method': method}
    if not notification:
        counter += 1
        message['id'] = counter
    if params is not None:
        message['params'] = params
    args = ['ddev', 'exec', 'curl', '--silent', '--show-error', '--fail-with-body',
            '--max-time', '180', '--include', 'http://serena:9121/mcp',
            '-H', 'Content-Type: application/json', '-H', 'Accept: application/json, text/event-stream']
    if session:
        args += ['-H', 'Mcp-Session-Id: '+session]
    result = subprocess.run(args+['--data', json.dumps(message)], capture_output=True, text=True)
    if result.returncode:
        raise RuntimeError('HTTP MCP request failed: '+method)
    for line in result.stdout.splitlines():
        if line.lower().startswith('mcp-session-id:'):
            session = line.split(':', 1)[1].strip()
        if line.startswith('data: '):
            reply = json.loads(line[6:])
            assert 'error' not in reply, 'MCP protocol error: '+method
            data = reply['result']
            assert not data.get('isError'), 'MCP tool failed: '+method
            return data
    assert notification, 'Missing MCP response: '+method


try:
    request('initialize', {'protocolVersion': '2024-11-05', 'capabilities': {},
                          'clientInfo': {'name': 'drupalphprust-web-smoke', 'version': '1'}})
    request('notifications/initialized', notification=True)
    request('tools/call', {'name': 'initial_instructions', 'arguments': {}})
    names = {tool['name'] for tool in request('tools/list', {})['tools']}
    assert 'get_symbols_overview' in names
    result = request('tools/call', {'name': 'get_symbols_overview', 'arguments': {
        'relative_path': 'php-rust/crates/php-types/src/zval.rs', 'depth': 0}})
    assert 'Zval' in json.dumps(result), 'Missing expected Rust symbol'
    print('PASS: web-container HTTP MCP initialise, tools/list and Rust symbol lookup (Zval)')
finally:
    if session:
        subprocess.run(['ddev', 'exec', 'curl', '--silent', '--max-time', '10', '-X', 'DELETE',
                        'http://serena:9121/mcp', '-H', 'Mcp-Session-Id: '+session],
                       stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
