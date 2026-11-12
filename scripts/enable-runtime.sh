#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"
profile="${1:-dev-release}"
case "$profile" in dev-release|release) ;; *) exit 2;; esac
[[ -f .ddev/config.yaml && -f web/.ht.router.php ]] || { echo 'Install Drupal CMS with the One Line Installer first.' >&2; exit 1; }
docker image inspect "drupalphprust:$profile" >/dev/null
# Only manage the file carrying this project's ownership marker.
target=.ddev/docker-compose.phpr.yaml
if [[ -f "$target" ]] && ! grep -q '^# Managed by drupalphprust$' "$target"; then
  echo "Refusing to overwrite unowned $target" >&2; exit 1
fi
port="${PHPR_HTTP_PORT:-}"
if [[ -z "$port" ]]; then
  port="$(python3 - "$target" <<'PORT'
from pathlib import Path
import re,sys
path=Path(sys.argv[1])
match=re.search(r'127\.0\.0\.1:([0-9]+):8090',path.read_text()) if path.exists() else None
print(match.group(1) if match else '18090')
PORT
)"
fi
[[ "$port" =~ ^[0-9]+$ ]] && (( port >= 1024 && port <= 65535 )) || { echo 'Invalid PHPR_HTTP_PORT' >&2; exit 2; }
python3 - "$profile" "$port" <<'PY'
from pathlib import Path
import sys,json,re,subprocess
from urllib.parse import urlsplit, urlunsplit
# DDEV owns the base hostname and router port; never guess from the folder name.
result=subprocess.run(['ddev','describe','-j'],check=True,capture_output=True,text=True)
records=[json.loads(line) for line in result.stdout.splitlines() if line.strip().startswith('{')]
info=next(record['raw'] for record in reversed(records) if isinstance(record.get('raw'),dict) and 'primary_url' in record['raw'])
url=urlsplit(info['primary_url'])
host=url.hostname
if not host or not re.fullmatch(r'[A-Za-z0-9.-]+',host):
    raise SystemExit('Cannot determine a safe DDEV hostname')
php_host='php.'+host
rust_host='rust.'+host
port_suffix=(':'+str(url.port)) if url.port else ''
urls='Standard PHP (Zend): '+urlunsplit((url.scheme,php_host+port_suffix,'/','',''))+'\nPHP RUST: '+urlunsplit((url.scheme,rust_host+port_suffix,'/','',''))+'\n'
source=Path('runtime/docker-compose.phpr.yaml').read_text()
Path('.ddev/docker-compose.phpr.yaml').write_text('# Managed by drupalphprust\n'+source.replace('drupalphprust:dev-release','drupalphprust:'+sys.argv[1]).replace('${PHPR_HTTP_PORT:-18090}',sys.argv[2]).replace('__PHP_HOST__',php_host).replace('__PHPR_HOST__',rust_host))
Path('.ddev/.runtime-urls').write_text(urls)
PY
ddev restart
printf '%s\n' "drupalphprust:$profile" > .ddev/.runtime-installed-image
cat .ddev/.runtime-urls
printf '%s\n' "PHP RUST direct/tunnel fallback: http://127.0.0.1:$port/" 'Both URLs use the same Drupal files and database. Named routes do not certify runtime compatibility.'
