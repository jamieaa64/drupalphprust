#!/usr/bin/env python3
"""Create a separate Drupal site with One Line Installer; Coder is an optional host."""
import argparse, os, re, shutil, subprocess, tempfile
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
p = argparse.ArgumentParser(description=__doc__)
p.add_argument('--target', choices=['local', 'coder'], default='local')
p.add_argument('--destination', type=Path, required=True, help='New site folder outside the source checkout')
p.add_argument('--runtime-port', type=int, default=18090)
p.add_argument('--installer', default='drupalaibp', help='Installed drupalaibp executable or downloaded script')
p.add_argument('--yes', action='store_true', help='Use One Line Installer --yolo defaults')
p.add_argument('--dry-run', action='store_true', help='Validate and describe without installing')
a = p.parse_args()
dest = a.destination.expanduser().resolve()
if dest == ROOT or ROOT in dest.parents or dest in ROOT.parents:
    p.error('Destination must be separate from the source checkout, not its parent or child.')
if dest.exists():
    p.error('Destination already exists; choose a new folder. No existing site will be changed.')
if not re.fullmatch(r'[a-z][a-z0-9-]*[a-z0-9]|[a-z]', dest.name):
    p.error('Destination folder name must be lowercase letters, digits and hyphens, starting with a letter.')
if not 1024 <= a.runtime_port <= 65535:
    p.error('Runtime port must be between 1024 and 65535.')
subprocess.run(['python3', str(ROOT/'scripts/check-repo.py')], check=True)
subprocess.run(['python3', str(ROOT/'scripts/check-secrets.py')], check=True)
status = subprocess.check_output(['git', 'status', '--porcelain'], cwd=ROOT, text=True)
if status.strip():
    p.error('Commit source changes first: installations use only committed Git content.')
print(f'Target: {a.target}; new site: {dest}; PHP Rust loopback port: {a.runtime_port}', flush=True)
if a.dry_run:
    print('Would clone committed source into a temporary clean checkout, invoke One Line Installer, and verify the runtime hook marker.')
    raise SystemExit(0)
installer = shutil.which(a.installer)
if installer is None and Path(a.installer).is_file():
    installer = str(Path(a.installer).resolve())
if installer is None:
    p.error('One Line Installer not found. Install drupalaibp or pass --installer /path/to/drupalaibp.')
for command in ['git', 'docker', 'ddev', 'bash']:
    if not shutil.which(command):
        p.error(f'{command} is required on this host.')
subprocess.run(['docker', 'info', '--format', '{{.ServerVersion}}'], check=True)
dest.parent.mkdir(parents=True, exist_ok=True)
env = dict(os.environ, DRUPALPHPRUST_TARGET=a.target, PHPR_HTTP_PORT=str(a.runtime_port))
with tempfile.TemporaryDirectory(prefix='drupalphprust-source-') as temporary:
    source = Path(temporary)/'source'
    subprocess.run(['git', 'clone', '--no-hardlinks', '--no-local', str(ROOT), str(source)], check=True)
    subprocess.run(['git', 'remote', 'set-url', 'origin', 'https://github.com/jamieaa64/drupalphprust.git'], cwd=source, check=True)
    command = ['bash', installer, str(source), '--config', ('coder' if a.target == 'coder' else 'drupalphprust'), '--name', dest.name]
    if a.yes:
        command.append('--yolo')
    result = subprocess.run(command, cwd=dest.parent, env=env)
    if result.returncode:
        raise SystemExit(f'Installer failed (exit {result.returncode}). Partial site preserved at {dest}; inspect the error before retrying. No existing files are deleted.')
marker = dest/'.ddev/.runtime-ready'
if not marker.is_file() or not marker.read_text().strip():
    raise SystemExit('Installer finished without runtime readiness. Inspect its hook error; see docs/USAGE.md for recovery.')
subprocess.run(['ddev', 'exec', '-s', 'phpr', 'phpr', '--version'], cwd=dest, check=True)
print(f'Setup completed in {dest}. Drupal compatibility remains experimental.')
if a.target == 'coder':
    print(f'On your laptop: coder port-forward WORKSPACE --tcp 127.0.0.1:{a.runtime_port}:{a.runtime_port}')
else:
    print(f'PHP Rust: http://127.0.0.1:{a.runtime_port}/ ; Zend: ddev describe')
