#!/usr/bin/env bash
# STDIO is reserved for MCP JSON-RPC. Do not print banners or auto-start DDEV here.
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
cd "$root"
if [[ ! -f .work/php-rust/php-rust/Cargo.toml ]]; then
  echo 'Prepare the runtime first: python3 scripts/prepare-runtime.py' >&2
  exit 1
fi
exec ddev exec -s serena -d /var/www/html/.work/php-rust/php-rust \
  serena start-mcp-server --context codex \
  --project /var/www/html/.work/php-rust \
  --transport stdio --agent-interface tools \
  --enable-web-dashboard false --open-web-dashboard false "$@"
