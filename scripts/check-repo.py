#!/usr/bin/env python3
"""Offline repository contract checks. Does not provision DDEV or run the installer."""
import hashlib,json,re,subprocess
from pathlib import Path
root=Path(__file__).resolve().parent.parent
m=json.loads((root/'runtime/manifest.json').read_text())
series=(root/'runtime/series').read_text().splitlines()
assert series==[p['file'] for p in m['patches']], 'manifest and series order differ'
assert len(set(series))==len(series), 'duplicate patches'
docker=(root/'runtime/Dockerfile').read_text()
assert re.findall(r'^COPY patches/(\S+) ',docker,re.M)==series, 'Docker patch order differs'
assert 'ARG PHPR_REF='+m['commit'] in docker, 'Docker upstream pin differs'
for entry in m['patches']:
    file=root/'runtime/patches'/entry['file']
    assert hashlib.sha256(file.read_bytes()).hexdigest()==entry['sha256'], 'Refresh manifest hash after reviewing '+str(file)
for folder in ['runtime', '.drupalaibp']:
    for file in (root/folder).rglob('*.json'): json.loads(file.read_text())
c=json.loads((root/'.drupalaibp/drupalphprust.json').read_text())
assert c['profile']=='template' and c['site_template']=='drupal_cms_starter'
assert c['ddev_project_type']=='drupal11' and c['is_drupal'] is True
coder=json.loads((root/'.drupalaibp/coder.json').read_text())
assert c['weight'] < coder['weight'], 'Local must be first in the installer picker'
assert c['questions'][0]['value']=='local' and coder['questions'][0]['value']=='coder'
assert {k:v for k,v in c.items() if k not in {'label','description','weight','questions'}} == {k:v for k,v in coder.items() if k not in {'label','description','weight','questions'}}, 'Host configs must share the same build'

for events in c['hooks'].values():
    for hook in events:
        assert hook['where']=='host'
        assert (root/'.drupalaibp'/hook['script']).is_file()
for folder in ['scripts','tests','.drupalaibp']:
    for file in (root/folder).rglob('*.sh'): subprocess.run(['bash','-n',str(file)],check=True)
for file in (root/'ddev-addon/commands/host').iterdir():
    subprocess.run(['bash','-n',str(file)],check=True)
for name in ['current','archive','future']:
    assert (root/'.agents/plan'/name/'README.md').is_file()
print(f'PASS: {len(series)} patch hashes/order, installer structure, shell syntax and plan folders.')
print('Installer execution, DDEV services, Coder routing and Drupal E2E are NOT checked here.')
