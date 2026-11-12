#!/usr/bin/env python3
"""Release only this checkout's owned host aliases and live tracker previews."""
import json
import re
import subprocess
from pathlib import Path

ROOT=Path(__file__).resolve().parent.parent

def cleanup(root=ROOT, run=subprocess.run):
    root=root.resolve()
    # A file signal cannot target a PID belonging to another project or reused PID.
    (root/'.ddev/.tracker-stop').touch()
    manifest=root/'.ddev/.runtime-owned-hostnames.json'
    if not manifest.exists():
        print('No owned hostname receipt; existing aliases are preserved.')
        return 0
    data=json.loads(manifest.read_text())
    if data.get('root') != str(root):
        raise ValueError('Refusing cleanup: hostname receipt belongs to another checkout')
    hosts=data.get('hosts')
    if not isinstance(hosts,list) or any(not isinstance(h,str) or not re.fullmatch(r'[A-Za-z0-9][A-Za-z0-9.-]*',h) for h in hosts):
        raise ValueError('Invalid owned hostname receipt')
    for host in list(hosts):
        # Only aliases explicitly recorded during registration; no global prune.
        # User handles any DDEV administrator authentication in this terminal.
        run(['ddev','hostname','-r',host,'127.0.0.1'],check=True)
        hosts.remove(host)
        manifest.write_text(json.dumps(data)+'\n')
    print('Project-owned aliases released; shared images and other projects preserved.')
    return 0

if __name__=='__main__':
    raise SystemExit(cleanup())
