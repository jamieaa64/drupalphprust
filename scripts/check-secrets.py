#!/usr/bin/env python3
"""Scan reachable Git history and tracked worktree files without printing secret values.
Heuristic check, not proof that arbitrary secrets cannot exist. Uses no service/API.
"""
import re, subprocess
from pathlib import Path
root = Path(__file__).resolve().parent.parent
patterns = [
    re.compile(rb'-----BEGIN (?:RSA |EC |OPENSSH |DSA )?PRIVATE KEY-----'),
    re.compile(rb'gh[pousr]_[A-Za-z0-9]{30,}'),
    re.compile(rb'github_pat_[A-Za-z0-9_]{40,}'),
    re.compile(rb'AKIA[0-9A-Z]{16}'),
    re.compile(rb'sk-(?:proj-)?[A-Za-z0-9_-]{40,}'),
]
def private_path(name):
    parts = Path(name).parts
    base = Path(name).name
    return (any(x in {'.ssh', '.aws', '.coder', '.playwright-cli'} for x in parts)
            or (base == '.env' or base.startswith('.env.')) and base != '.env.example'
            or base.endswith(('.sql', '.sql.gz', '.pem', '.p12', '.key', '.state.json'))
            or base == '.oob-test-credentials.json')
issues = set()
objects = subprocess.check_output(['git', 'rev-list', '--objects', '--all'], cwd=root).splitlines()
blobs = 0
for line in objects:
    fields = line.split(b' ', 1)
    if len(fields) != 2:
        continue
    oid, raw_name = fields
    name = raw_name.decode(errors='replace')
    kind = subprocess.check_output(['git', 'cat-file', '-t', oid], cwd=root).strip()
    if kind != b'blob':
        continue
    blobs += 1
    if private_path(name):
        issues.add('Private artifact path in history: '+name)
    data = subprocess.check_output(['git', 'cat-file', 'blob', oid], cwd=root)
    if any(pattern.search(data) for pattern in patterns):
        issues.add('Potential secret in history: '+name)
for raw in subprocess.check_output(['git', 'ls-files', '-z'], cwd=root).split(b'\0'):
    if not raw:
        continue
    name = raw.decode(); path = root/name
    if private_path(name):
        issues.add('Private artifact tracked: '+name)
    if path.is_file() and any(pattern.search(path.read_bytes()) for pattern in patterns):
        issues.add('Potential secret in tracked file: '+name)
if issues:
    raise SystemExit('\n'.join(sorted(issues)))
print(f'PASS: scanned {blobs} reachable Git blobs and tracked files; no matching secrets/private artifacts. Heuristic coverage only.')
