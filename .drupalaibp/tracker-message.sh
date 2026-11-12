#!/usr/bin/env bash
set -euo pipefail
python3 scripts/tracker.py --message
python3 scripts/setup-serena.py --status
