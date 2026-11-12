#!/usr/bin/env python3
"""Prepare runtime navigation and project-scoped MCP configuration without login."""
import argparse
import json
import os
from pathlib import Path
import re
import subprocess
import tomllib

ROOT = Path(__file__).resolve().parent.parent
NAME = 'drupalphprust-serena'
START = '# drupalphprust-serena:start'
END = '# drupalphprust-serena:end'


def answer(root, key, choices, default):
    value = os.environ.get(key)
    if value is None and (root/'.ddev/.env').is_file():
        matches = re.findall(r'^'+key+r'="?([a-z-]+)"?$', (root/'.ddev/.env').read_text(), re.M)
        value = matches[-1] if matches else None
    value = value or default
    if value not in choices:
        raise ValueError('Invalid '+key)
    return value


def configure(root, client, location):
    """Validate every selected destination before changing any client config."""
    if client == 'manual':
        return
    state_path = root/'.ddev/.serena-client.json'
    if state_path.is_symlink():
        raise ValueError('Refusing symlink: '+str(state_path))
    state = json.loads(state_path.read_text()) if state_path.exists() else {}
    writes = {}
    command = str(root/'scripts/serena-mcp.sh')
    if client in ('codex', 'both'):
        path = root/'.codex/config.toml'
        if path.is_symlink():
            raise ValueError('Refusing symlink: '+str(path))
        old = path.read_text() if path.exists() else ''
        parsed = tomllib.loads(old)
        has_block = START in old and END in old
        if NAME in parsed.get('mcp_servers', {}) and not has_block:
            raise ValueError('Existing unowned Codex Serena entry; preserve and configure manually')
        if (START in old) != (END in old):
            raise ValueError('Incomplete managed Codex block')
        if has_block:
            old = re.sub(re.escape(START)+r'.*?'+re.escape(END)+r'\n?', '', old, flags=re.S)
        body = ('url = "http://serena:9121/mcp"\n' if location == 'ddev' else
                'command = "bash"\nargs = '+json.dumps([command])+'\n')
        new = old.rstrip()+'\n\n'+START+'\n[mcp_servers.'+NAME+']\n'+body+'startup_timeout_sec = 180\ntool_timeout_sec = 180\n'+END+'\n'
        tomllib.loads(new)
        writes[path] = new
    if client in ('claude', 'both'):
        path = root/'.mcp.json'
        if path.is_symlink():
            raise ValueError('Refusing symlink: '+str(path))
        data = json.loads(path.read_text()) if path.exists() else {}
        servers = data.setdefault('mcpServers', {})
        entry = ({'type': 'http', 'url': 'http://serena:9121/mcp'} if location == 'ddev' else
                 {'type': 'stdio', 'command': 'bash', 'args': [command, '--context', 'claude-code']})
        if NAME in servers and servers[NAME] != state.get('claude_entry') and servers[NAME] != entry:
            raise ValueError('Existing unowned Claude Serena entry; preserve and configure manually')
        servers[NAME] = entry
        state['claude_entry'] = entry
        writes[path] = json.dumps(data, indent=2)+'\n'
    for path in writes:
        if path.parent.is_symlink():
            raise ValueError('Refusing symlink directory: '+str(path.parent))
    for path, content in writes.items():
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(content)
    state.update(client=client, location=location)
    state_path.write_text(json.dumps(state, indent=2)+'\n')


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--client', choices=['codex', 'claude', 'both', 'manual'])
    parser.add_argument('--location', choices=['host', 'ddev'])
    parser.add_argument('--install-agents', action='store_true', help='Install selected DDEV agent add-ons; never authenticate')
    parser.add_argument('--status', action='store_true', help='Print credential-free completion status')
    args = parser.parse_args()
    ready = ROOT/'.ddev/.serena-setup-complete'
    if args.status:
        if ready.exists():
            state = json.loads(ready.read_text())
            print('Serena setup: '+state['client']+' / '+state['location']+'; reconnect/trust the client and verify MCP tools.')
        else:
            print('Serena client setup incomplete or manual. See docs/SERENA.md and rerun scripts/setup-serena.py.')
        return
    client = args.client or answer(ROOT, 'DRUPALPHPRUST_AGENT', ['codex', 'claude', 'both', 'manual'], 'manual')
    location = args.location or answer(ROOT, 'DRUPALPHPRUST_AGENT_LOCATION', ['host', 'ddev'], 'host')
    ready.unlink(missing_ok=True)
    if client == 'manual':
        print('Serena container installed. Configure clients later: python3 scripts/setup-serena.py --client codex --location host')
        return
    runtime = ROOT/'.work/php-rust'
    if not runtime.exists():
        subprocess.run(['python3', str(ROOT/'scripts/prepare-runtime.py')], cwd=ROOT, check=True)
    if not (runtime/'php-rust/Cargo.toml').is_file():
        raise ValueError('Existing runtime checkout is incomplete; preserve it and inspect before continuing')
    config = runtime/'.serena/project.yml'
    template = (ROOT/'runtime/serena/project.yml').read_text()
    if config.is_symlink() or config.parent.is_symlink():
        raise ValueError('Refusing symlink Serena configuration')
    if config.exists():
        # Serena expands its YAML on activation; never replace an existing config.
        print('Preserved existing runtime Serena configuration.')
    else:
        config.parent.mkdir(exist_ok=True)
        config.write_text(template)
    configure(ROOT, client, location)
    if location == 'ddev' and args.install_agents:
        selected = ['codex', 'claude'] if client == 'both' else [client]
        for agent in selected:
            subprocess.run(['ddev', 'add-on', 'get', 'e0ipso/ddev-assistant-'+agent], cwd=ROOT, check=True)
            command = ROOT/'.ddev/commands/web'/agent
            body = '#!/usr/bin/env bash\n# Managed by drupalphprust Serena setup\nexec '+agent+' "$@"\n'
            if command.exists() and 'Managed by drupalphprust Serena setup' not in command.read_text():
                print('Preserved existing agent command:', command.name)
            else:
                command.parent.mkdir(parents=True, exist_ok=True)
                command.write_text(body)
                command.chmod(0o755)
        subprocess.run(['ddev', 'restart'], cwd=ROOT, check=True)
    ready.write_text(json.dumps({'client': client, 'location': location})+'\n')
    print(f'Configured {client} for {location}. Open/trust this project in the client and reconnect MCP.')
    print('Agent sign-in remains a user action. No credentials or global agent settings changed.')


if __name__ == '__main__':
    main()
