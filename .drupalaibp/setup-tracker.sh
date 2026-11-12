#!/usr/bin/env bash
set -euo pipefail
# OLI can continue after a failed hook. Publish the tracker even if DNS setup fails,
# so the landing page explains recovery instead of implying that sites are usable.
network_result=0
python3 scripts/register-runtime-hostnames.py || network_result=$?
python3 scripts/tracker.py --install
exit "$network_result"
