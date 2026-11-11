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
python3 - "$profile" <<'PY'
from pathlib import Path
import sys
source=Path('runtime/docker-compose.phpr.yaml').read_text()
Path('.ddev/docker-compose.phpr.yaml').write_text('# Managed by drupalphprust\n'+source.replace('drupalphprust:dev-release','drupalphprust:'+sys.argv[1]))
PY
ddev restart
printf '%s\n' "drupalphprust:$profile" > .ddev/.runtime-installed-image
printf '%s\n' 'PHP Rust configured. Check http://127.0.0.1:18090/ (or PHPR_HTTP_PORT).' 'This does not certify Drupal compatibility. Zend remains the normal DDEV URL.'
