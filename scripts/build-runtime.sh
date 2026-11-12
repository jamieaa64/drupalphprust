#!/usr/bin/env bash
# DDEV owns the build and project-scoped image; no shared custom image tag.
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
python3 "$root/scripts/check-repo.py"
bash "$root/scripts/enable-runtime.sh" "${1:-dev-release}"
