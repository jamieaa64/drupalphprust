#!/usr/bin/env python3
"""Configure the bundled DDEV add-on from actual project metadata."""
import argparse,json,re,subprocess
from pathlib import Path
from urllib.parse import urlsplit,urlunsplit
ROOT=Path(__file__).resolve().parent.parent


def render(root=ROOT):
    urls=dict(line.split(': ',1) for line in (root/'.ddev/.runtime-urls').read_text().splitlines())
    profile=(root/'.ddev/.runtime-profile').read_text().strip()
    if profile not in ('dev-release','release'): raise ValueError('Invalid runtime profile')
    source=(root/'ddev-addon/docker-compose.phpr.yaml').read_text()
    for label,token in [('Standard PHP (Zend)','__PHP_HOST__'),('PHP RUST','__PHPR_HOST__'),('Project tracker','__TRACKER_HOST__')]:
        host=urlsplit(urls[label]).hostname
        if not host or not re.fullmatch(r'[A-Za-z0-9.-]+',host): raise ValueError('Invalid hostname')
        source=source.replace(token,host)
    port=(root/'.ddev/.runtime-port').read_text().strip()
    if not port.isdigit() or not (int(port)==0 or 1024<=int(port)<=65535):raise ValueError('Invalid port')
    source=source.replace('__RUNTIME_PROFILE__',profile).replace('${PHPR_HTTP_PORT:-18090}','' if port=='0' else port)
    (root/'.ddev/docker-compose.phpr.yaml').write_text(source)


def routes(root=ROOT,profile='dev-release',port='0'):
    result=subprocess.run(['ddev','describe','-j'],cwd=root,check=True,capture_output=True,text=True)
    records=[json.loads(line) for line in result.stdout.splitlines() if line.startswith('{')]
    raw=next(r['raw'] for r in reversed(records) if 'primary_url' in r.get('raw',{}))
    url=urlsplit(raw['primary_url'])
    if url.scheme not in ('http','https') or url.username or url.password or not url.hostname or not re.fullmatch(r'[A-Za-z0-9.-]+',url.hostname):raise ValueError('Invalid DDEV primary URL')
    lines=[]
    for label,prefix in [('Standard PHP (Zend)','php'),('PHP RUST','rust'),('Project tracker','tracker')]:
        host=prefix+'.'+url.hostname+(':'+str(url.port) if url.port else '')
        lines.append(label+': '+urlunsplit((url.scheme,host,'/','','')))
    (root/'.ddev/.runtime-urls').write_text('\n'.join(lines)+'\n')
    (root/'.ddev/.runtime-profile').write_text(profile+'\n')
    (root/'.ddev/.runtime-port').write_text(port+'\n')
    name=raw['name']
    if not re.fullmatch(r'[A-Za-z0-9-]+',name):raise ValueError('Invalid DDEV project name')
    (root/'.ddev/.runtime-installed-image').write_text('debian:bookworm-slim-'+name+'-phpr-built\n')
    tracker=lines[-1].split(': ',1)[1]
    subprocess.run(['ddev','dotenv','set','.ddev/.env','--launch-path='+tracker],cwd=root,check=True)

if __name__=='__main__':
    parser=argparse.ArgumentParser();parser.add_argument('--render',action='store_true');parser.add_argument('--profile',choices=['dev-release','release'],default='dev-release');parser.add_argument('--port',default='0');args=parser.parse_args()
    if args.render:render()
    else:routes(profile=args.profile,port=args.port)
