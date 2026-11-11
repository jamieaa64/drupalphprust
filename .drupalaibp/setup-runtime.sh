#!/usr/bin/env bash
# Late host hook; One Line Installer copies the source repo after scaffolding.
set -euo pipefail
[[ -f runtime/Dockerfile && -f scripts/enable-runtime.sh && -f .ddev/config.yaml ]] || {
  echo 'Expected the drupalphprust source overlay in the installed project.' >&2; exit 1;
}
rm -f .ddev/.runtime-ready
bash scripts/build-runtime.sh dev-release
bash scripts/enable-runtime.sh dev-release
printf '%s\n' 'Runtime setup completed; browser compatibility remains experimental.' > .ddev/.runtime-ready
