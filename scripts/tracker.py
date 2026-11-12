#!/usr/bin/env python3
"""Build a standalone plan tracker, or serve its Markdown live on loopback."""
import argparse, json, re
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

def html(data):
    # Keep script-closing markup in documentation inert inside the JSON element.
    payload=json.dumps(data,ensure_ascii=True).replace('<',r'\u003c')
    return (ROOT/'scripts/tracker-template.html').read_text().replace('__TRACKER_DATA__',payload)

def main():
    p=argparse.ArgumentParser(description=__doc__)
    p.add_argument('--serve',action='store_true')
    p.add_argument('--port',type=int,default=8765)
    p.add_argument('--check',action='store_true',help='Fail if the committed HTML snapshot is stale')
    args=p.parse_args()
    if args.serve:
        class Handler(BaseHTTPRequestHandler):
            def do_GET(self):
                route=self.path.split('?',1)[0]
                if route not in ('/','/tracker.html','/snapshot'):
                    self.send_error(404); return
                try:
                    data=snapshot()
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

if __name__=='__main__': main()
