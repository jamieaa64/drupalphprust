#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"
profile="${1:-dev-release}"
case "$profile" in dev-release|release) ;; *) exit 2;; esac
python3 scripts/require-ddev.py
[[ -f .ddev/config.yaml && -f web/.ht.router.php ]] || { echo 'Install Drupal CMS first.' >&2; exit 1; }
# Preserve an unowned service instead of allowing add-on installation to replace it.
if [[ -f .ddev/docker-compose.phpr.yaml ]] && ! grep -Eq '^#(ddev-generated| Managed by drupalphprust)$' .ddev/docker-compose.phpr.yaml; then
  echo 'Refusing to overwrite unowned PHP Rust service.' >&2; exit 1
fi
port="${PHPR_HTTP_PORT:-}"
if [[ -z "$port" && -f .ddev/.runtime-port ]]; then port="$(cat .ddev/.runtime-port)"; fi
if [[ -z "$port" && -f .ddev/docker-compose.phpr.yaml ]]; then
  port="$(python3 - <<'PORT'
from pathlib import Path
import re
match=re.search(r'127\.0\.0\.1:([0-9]+):8090',Path('.ddev/docker-compose.phpr.yaml').read_text())
print(match.group(1) if match else '')
PORT
)"
fi
port="${port:-0}"
[[ "$port" =~ ^[0-9]+$ ]] && (( port == 0 || (port >= 1024 && port <= 65535) )) || { echo 'Invalid PHPR_HTTP_PORT' >&2; exit 2; }
# Older project-owned files predate DDEV's marker; migrate only that known owner.
if [[ -f .ddev/docker-compose.phpr.yaml ]] && grep -q '^# Managed by drupalphprust$' .ddev/docker-compose.phpr.yaml; then
  python3 -c 'from pathlib import Path; p=Path(".ddev/docker-compose.phpr.yaml"); p.write_text("#ddev-generated\n"+p.read_text())'
fi
python3 scripts/configure-runtime.py --profile "$profile" --port "$port"
ddev add-on get ./ddev-addon
# Publish static tracker before its service starts; it is outside Drupal's docroot.
python3 scripts/tracker.py --publish
# DDEV builds the project-scoped runtime image via Compose, then starts both services.
ddev restart
python3 scripts/tracker.py --install
