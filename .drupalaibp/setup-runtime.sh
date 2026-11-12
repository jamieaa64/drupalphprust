#!/usr/bin/env bash
# Late host hook; One Line Installer copies the source repo after scaffolding.
set -euo pipefail
[[ -f runtime/Dockerfile && -f scripts/enable-runtime.sh && -f .ddev/config.yaml ]] || {
  echo 'Expected the drupalphprust source overlay in the installed project.' >&2; exit 1;
}
target="${DRUPALPHPRUST_TARGET:-}"
if [[ -z "$target" && -f .ddev/.runtime-target ]]; then target="$(cat .ddev/.runtime-target)"; fi
target="${target:-local}"
case "$target" in local|coder) ;; *) echo 'Target must be local or coder' >&2; exit 2;; esac
rm -f .ddev/.runtime-ready
bash scripts/build-runtime.sh dev-release
bash scripts/enable-runtime.sh dev-release
printf '%s\n' 'Runtime setup completed; browser compatibility remains experimental.' > .ddev/.runtime-ready
printf '%s\n' "$target" > .ddev/.runtime-target
