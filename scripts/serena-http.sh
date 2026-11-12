#!/usr/bin/env bash
# Internal DDEV network only; no router alias or published host port.
set -euo pipefail
while [[ ! -f /var/www/html/.work/php-rust/.serena/project.yml ]]; do sleep 2; done
cd /var/www/html/.work/php-rust/php-rust
exec serena start-mcp-server --context ide --agent-interface tools \
  --project /var/www/html/.work/php-rust --transport streamable-http \
  --host 0.0.0.0 --port 9121 --enable-web-dashboard false --open-web-dashboard false
