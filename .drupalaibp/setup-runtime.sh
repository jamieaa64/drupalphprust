#!/usr/bin/env bash
# Late host hook; One Line Installer copies the source repo after scaffolding.
set -euo pipefail
[[ -f runtime/Dockerfile && -f scripts/enable-runtime.sh && -f .ddev/config.yaml ]] || {
  echo 'Expected the drupalphprust source overlay in the installed project.' >&2; exit 1;
}
target="${DRUPALPHPRUST_TARGET:-}"
# Installer config answers are persisted for host hooks, not exported by the CLI.
# Read only this non-secret key; never source the dotenv file as shell code.
if [[ -z "$target" && -f .ddev/.env ]]; then
  target="$(python3 - <<'TARGET'
from pathlib import Path
import re
values=re.findall(r'^DRUPALPHPRUST_TARGET="?(local|coder)"?$',Path('.ddev/.env').read_text(),re.M)
print(values[-1] if values else '')
TARGET
)"
fi
if [[ -z "$target" && -f .ddev/.runtime-target ]]; then target="$(cat .ddev/.runtime-target)"; fi
target="${target:-local}"
case "$target" in local|coder) ;; *) echo 'Target must be local or coder' >&2; exit 2;; esac
rm -f .ddev/.runtime-ready
bash scripts/build-runtime.sh dev-release
bash scripts/enable-runtime.sh dev-release
printf '%s\n' 'Runtime setup completed; browser compatibility remains experimental.' > .ddev/.runtime-ready
printf '%s\n' "$target" > .ddev/.runtime-target
if [[ "$target" == coder ]]; then
  echo 'Coder: use your existing workspace routes or a private coder port-forward tunnel.'
else
  echo 'Local setup: use the labelled php. and rust. URLs printed above; saved in .ddev/.runtime-urls.'
fi
