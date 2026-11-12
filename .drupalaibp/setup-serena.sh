#!/usr/bin/env bash
# Late host hook: answers were persisted by OLI into .ddev/.env.
set -euo pipefail
python3 scripts/setup-serena.py --install-agents
