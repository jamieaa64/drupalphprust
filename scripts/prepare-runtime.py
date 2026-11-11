#!/usr/bin/env python3
"""Reconstruct the patched runtime in a fresh checkout; never edit an existing tree."""
import argparse,json,subprocess
from pathlib import Path
p=argparse.ArgumentParser(description=__doc__)
p.add_argument('--source',help='Optional existing PHP Rust clone to copy without network')
p.add_argument('--destination',type=Path,default=Path('.work/php-rust'))
a=p.parse_args();root=Path(__file__).resolve().parent.parent
m=json.loads((root/'runtime/manifest.json').read_text())
dest=a.destination.resolve()
if dest.exists(): p.error('Destination already exists; select a fresh path. No reset or cleanup is performed.')
subprocess.run(['python3',str(root/'scripts/check-repo.py')],check=True)
dest.parent.mkdir(parents=True,exist_ok=True)
source=str(Path(a.source).resolve()) if a.source else m['repository']
subprocess.run(['git','clone','--no-hardlinks','--no-checkout',source,str(dest)],check=True)
subprocess.run(['git','checkout','-b','codex/drupal-compatibility',m['commit']],cwd=dest,check=True)
for entry in m['patches']:
    patch=str(root/'runtime/patches'/entry['file'])
    subprocess.run(['git','apply','--check',patch],cwd=dest,check=True)
    subprocess.run(['git','apply',patch],cwd=dest,check=True)
print('Prepared:',dest)
print('Patches remain uncommitted for review. Read the upstream AGENTS.md before runtime edits.')
