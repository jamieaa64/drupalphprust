#!/usr/bin/env python3
"""Use the same version prerequisite for late setup/recovery as fresh installs."""
import subprocess
from pathlib import Path
root=Path(__file__).resolve().parent.parent
raise SystemExit(subprocess.run(['bash',str(root/'.drupalaibp/check-ddev-version.sh'),'--check-only']).returncode)
