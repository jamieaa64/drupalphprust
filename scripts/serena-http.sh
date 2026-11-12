#!/usr/bin/env bash
# MCP stays internal; only the separate dashboard is routed by DDEV.
set -euo pipefail
while [[ ! -f /var/www/html/.work/php-rust/.serena/project.yml ]]; do sleep 2; done
cd /var/www/html/.work/php-rust/php-rust
python3 - <<'PY'
import json, os
from pathlib import Path
config = Path.home()/'.serena/serena_config.yml'
config.parent.mkdir(parents=True, exist_ok=True)
config.write_text(json.dumps({
    'projects': [],
    'web_dashboard_listen_address': '0.0.0.0',
    'web_dashboard_trusted_hosts': ['127.0.0.1', 'localhost', os.environ['SERENA_DASHBOARD_HOST']]
        + [h.strip() for h in os.environ.get('SERENA_DASHBOARD_EXTRA_HOSTS', '').split(',') if h.strip()],
    'web_dashboard_open_on_launch': False,
}))
PY
exec serena start-mcp-server --context ide --agent-interface tools \
  --project /var/www/html/.work/php-rust --transport streamable-http \
  --host 0.0.0.0 --port 9121 --enable-web-dashboard true --open-web-dashboard false
