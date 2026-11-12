#!/usr/bin/env python3
"""Build a standalone plan tracker, or serve its Markdown live on loopback."""
import argparse, json, re, subprocess
from datetime import datetime, timezone
from urllib.parse import urlsplit
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer
from pathlib import Path
ROOT = Path(__file__).resolve().parent.parent
STATUSES = {'done', 'active', 'blocked', 'pending', 'partial', 'patched', 'deferred'}

def snapshot(root=ROOT):
    files = [root/'PROGRESS.md', *sorted((root/'.agents/plan').rglob('*.md')), *sorted((root/'docs').rglob('*.md'))]
    pages = {str(p.relative_to(root)): p.read_text() for p in files if p.is_file() and not p.is_symlink()}
    steps=[]
    for p in sorted((root/'.agents/plan/current').glob('[0-9][0-9]-*.md')):
        path=str(p.relative_to(root)); content=pages[path]
        status=re.search(r'^Status: (\w+)$',content,re.M)
        if not status or status[1] not in STATUSES:
            raise ValueError(f'Invalid or missing status: {path}')
        steps.append({'path':path,'title':content.splitlines()[0].removeprefix('# '),'status':status[1]})
    if not steps:
        raise ValueError('Expected at least one task document')
    return {'pages':pages,'steps':steps}


def safe_url(value):
    url=urlsplit(value)
    if (url.scheme not in ('http','https') or not url.hostname or url.username
            or url.password or url.query or url.fragment or url.path not in ('','/')
            or any(c.isspace() for c in value)):
        raise ValueError('Expected a credential-free runtime base URL')
    return value.rstrip('/')+'/'

def installation(root=ROOT):
    path=root/'.ddev/.runtime-urls'
    urls={}
    if path.is_file():
        for line in path.read_text().splitlines():
            for prefix,key in [('Standard PHP (Zend): ','php'),('PHP RUST: ','rust')]:
                if line.startswith(prefix): urls[key]=safe_url(line[len(prefix):])
    info={'urls':urls,'services':{},'checked':'Not checked'}
    status=root/'.ddev/.tracker-status.json'
    if status.is_file():
        saved=json.loads(status.read_text())
        info['checked']=saved.get('checked','Not checked')
        info['services']={k:v for k,v in saved.get('services',{}).items()
                          if k in ('web','phpr') and v in ('running','stopped','unknown')}
    network=root/'.ddev/.runtime-network.json'
    info['network']='not-checked'
    if network.is_file():
        state=json.loads(network.read_text()).get('status')
        if state in ('ready','failed','pending','private-routing-required'): info['network']=state
    return info

def refresh_status():
    result=subprocess.run(['ddev','describe','-j'],check=True,capture_output=True,text=True)
    records=[json.loads(line) for line in result.stdout.splitlines() if line.startswith('{')]
    raw=next(r['raw'] for r in reversed(records) if 'primary_url' in r.get('raw',{}))
    # Persist only an explicit allowlist, never DDEV's credentials or full response.
    info={'tracker':safe_url(raw['primary_url'])+'project-tracker/',
          'checked':datetime.now(timezone.utc).isoformat(timespec='seconds'),
          'services':{name:raw.get('services',{}).get(name,{}).get('status','unknown')
                      for name in ('web','phpr')}}
    (ROOT/'.ddev/.tracker-status.json').write_text(json.dumps(info)+'\n')
    return info

def installed_snapshot():
    data=snapshot(); data['installation']=installation(); return data

def publish_installed():
    target=ROOT/'web/project-tracker'
    marker=target/'.drupalphprust-tracker'
    if target.exists() and not marker.is_file():
        raise ValueError('Refusing to overwrite unowned web/project-tracker')
    target.mkdir(exist_ok=True); marker.touch()
    (target/'index.html').write_text(html(installed_snapshot()))


def html(data):
    # Keep script-closing markup in documentation inert inside the JSON element.
    payload=json.dumps(data,ensure_ascii=True).replace('<',r'\u003c')
    return (ROOT/'scripts/tracker-template.html').read_text().replace('__TRACKER_DATA__',payload)

def main():
    p=argparse.ArgumentParser(description=__doc__)
    p.add_argument('--install',action='store_true',help='Publish the installed landing page and record service status')
    p.add_argument('--message',action='store_true',help='Print actual installed URLs without credentials')
    p.add_argument('--serve',action='store_true')
    p.add_argument('--port',type=int,default=8765)
    p.add_argument('--check',action='store_true',help='Fail if the committed HTML snapshot is stale')
    args=p.parse_args()
    if args.install:
        refresh_status(); publish_installed(); print('Published web/project-tracker/index.html'); return
    if args.message:
        info=installation(); status=json.loads((ROOT/'.ddev/.tracker-status.json').read_text())
        print('Project tracker: '+status['tracker'])
        print('Drupal on standard PHP: '+info['urls'].get('php','Unavailable: runtime setup did not complete'))
        print('Drupal on PHP Rust: '+info['urls'].get('rust','Unavailable: runtime setup did not complete'))
        print('Hostname setup: '+info['network'])
        return
    if args.serve:
        class Handler(BaseHTTPRequestHandler):
            def do_GET(self):
                route=self.path.split('?',1)[0]
                if route not in ('/','/tracker.html','/snapshot'):
                    self.send_error(404); return
                try:
                    data=installed_snapshot(); data['live']=True
                    body=(json.dumps(data) if route=='/snapshot' else html(data)).encode()
                except (OSError,ValueError) as error:
                    self.send_error(500,str(error)); return
                self.send_response(200)
                self.send_header('Content-Type','application/json; charset=utf-8' if route=='/snapshot' else 'text/html; charset=utf-8')
                self.send_header('Cache-Control','no-store')
                self.send_header('Content-Length',str(len(body)))
                self.end_headers(); self.wfile.write(body)
            def log_message(self,*args): pass
        print(f'Tracker: http://127.0.0.1:{args.port} (Ctrl-C to stop)',flush=True)
        try: ThreadingHTTPServer(('127.0.0.1',args.port),Handler).serve_forever()
        except KeyboardInterrupt: pass
    else:
        output=html(snapshot()); target=ROOT/'tracker.html'
        if args.check:
            if not target.exists() or target.read_text()!=output:
                raise SystemExit('Tracker snapshot is stale: run python3 scripts/tracker.py')
            print('PASS: tracker snapshot matches Markdown')
        else:
            target.write_text(output); print('Updated tracker.html from Markdown')
            if (ROOT/'web/project-tracker/.drupalphprust-tracker').is_file(): publish_installed()

if __name__=='__main__': main()
