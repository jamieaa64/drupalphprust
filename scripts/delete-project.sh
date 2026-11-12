#!/usr/bin/env bash
# Compatibility entry point until upstream OLI ships before-delete.sh support.
set -euo pipefail
cd "$(dirname "$0")/.."
[[ -f .ddev/config.yaml ]] || { echo 'Run only in an installed project.' >&2; exit 1; }
printf 'Clean up and permanently delete this project: %s\nType DELETE to continue: ' "$PWD"
read -r answer
[[ "$answer" == DELETE ]] || exit 1
python3 scripts/cleanup-project.py
ddev delete -Oy
# Leave source removal to the user/OLI; do not recursively remove the live script's checkout.
echo 'DDEV project deleted. You may now remove this project directory.'
