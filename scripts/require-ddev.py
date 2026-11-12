#!/usr/bin/env python3
"""Fail before enabling native deletion hooks on unsupported DDEV versions."""
import re,subprocess
result=subprocess.run(['ddev','--version'],check=True,capture_output=True,text=True)
match=re.search(r'v?(\d+)\.(\d+)\.(\d+)',result.stdout)
if not match or tuple(map(int,match.groups())) < (1,25,5):
    raise SystemExit('DDEV 1.25.5 or newer is required for automatic deletion cleanup. Upgrade DDEV, then rerun setup; no custom delete command is needed.')
