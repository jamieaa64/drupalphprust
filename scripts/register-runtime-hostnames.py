#!/usr/bin/env python3
"""Register local runtime aliases through DDEV without handling credentials."""
import json
import re
import socket
import subprocess
from pathlib import Path
from urllib.parse import urlsplit

ROOT = Path(__file__).resolve().parent.parent

def register(root=ROOT, run=subprocess.run, resolve=socket.gethostbyname):
    receipt = root/'.ddev/.runtime-network.json'
    ownership = root/'.ddev/.runtime-owned-hostnames.json'
    owned = json.loads(ownership.read_text()) if ownership.exists() else {'root': str(root.resolve()), 'hosts': []}
    if owned.get('root') != str(root.resolve()):
        raise ValueError('Hostname ownership belongs to another checkout')
    target_file = root/'.ddev/.runtime-target'
    target = target_file.read_text().strip() if target_file.exists() else 'local'
    def finish(status):
        receipt.write_text(json.dumps({'status': status})+'\n')
        return 0 if status in ('ready', 'private-routing-required') else 1
    if target == 'coder':
        print('Coder: use private workspace routes or tunnels; host registration is not laptop DNS.')
        return finish('private-routing-required')
    if target != 'local':
        return finish('failed')
    # Clear stale success before any operation that could fail or be interrupted.
    finish('pending')
    try:
        lines = (root/'.ddev/.runtime-urls').read_text().splitlines()
        hosts = []
        for prefix in ('Standard PHP (Zend): ', 'PHP RUST: ', 'Project tracker: '):
            matches = [line[len(prefix):] for line in lines if line.startswith(prefix)]
            if len(matches) != 1:
                raise ValueError('Missing or ambiguous runtime URL')
            url = urlsplit(matches[0])
            host = url.hostname
            if (url.scheme not in ('http', 'https') or url.username or url.password
                    or url.query or url.fragment or url.path not in ('', '/')
                    or not host or not re.fullmatch(r'[a-zA-Z0-9][a-zA-Z0-9.-]*', host)):
                raise ValueError('Invalid runtime URL')
            hosts.append(host)
        dashboard = [line.split(': ', 1)[1] for line in lines if line.startswith('Serena dashboard: ')]
        if dashboard:
            if len(dashboard) != 1: raise ValueError('Ambiguous dashboard URL')
            url = urlsplit(dashboard[0])
            if (url.scheme not in ('http','https') or url.username or url.password or url.query or url.fragment
                    or url.path not in ('','/') or not url.hostname or not re.fullmatch(r'[a-zA-Z0-9][a-zA-Z0-9.-]*',url.hostname)):
                raise ValueError('Invalid dashboard URL')
            hosts.append(url.hostname)
        for host in dict.fromkeys(hosts):
            try:
                if resolve(host) == '127.0.0.1':
                    print(f'Hostname ready: {host}')
                    continue
            except OSError:
                pass
            print(f'Registering {host} through DDEV. Enter any administrator password directly in your terminal.', flush=True)
            # Inherit the installer terminal; never capture, store or supply a password.
            # Record intent before mutation, so interruption cannot lose cleanup ownership.
            # Already resolving aliases are never adopted from another owner.
            if host not in owned['hosts']: owned['hosts'].append(host)
            ownership.write_text(json.dumps(owned)+'\n')
            run(['ddev', 'hostname', host, '127.0.0.1'], check=True)
            if resolve(host) != '127.0.0.1':
                raise ValueError('Hostname did not resolve to local DDEV after registration')
        return finish('ready')
    except (OSError, ValueError, subprocess.CalledProcessError):
        print('Hostname setup incomplete. Run python3 scripts/register-runtime-hostnames.py in your terminal to retry.', flush=True)
        return finish('failed')

if __name__ == '__main__':
    raise SystemExit(register())
