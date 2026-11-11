#!/usr/bin/env bash
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
profile="${1:-dev-release}"
case "$profile" in dev-release|release) ;; *) echo 'Use dev-release or release' >&2; exit 2;; esac
python3 "$root/scripts/check-repo.py"
docker build --build-arg "RUNTIME_PROFILE=$profile" -f "$root/runtime/Dockerfile" -t "drupalphprust:$profile" "$root/runtime"
