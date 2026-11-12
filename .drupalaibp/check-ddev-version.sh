#!/usr/bin/env bash
# Early OLI hook, staged before configure_ddev; also used by runtime recovery.
set -euo pipefail
minimum=1.25.5
if [[ "${1:-}" != --check-only ]]; then
  # OLI hooks warn and continue. DDEV itself must enforce the prerequisite.
  mkdir -p .ddev
  target=.ddev/config.php-rust-version.yaml
  if [[ -e "$target" ]] && ! grep -qx '# Managed by drupalphprust version prerequisite' "$target"; then
    echo "Refusing to replace unowned $target" >&2
    exit 1
  fi
  printf '%s\n' '# Managed by drupalphprust version prerequisite' \
    "ddev_version_constraint: '>= v$minimum'" > "$target"
fi
version="$(ddev --version 2>/dev/null || true)"
if [[ "$version" =~ v?([0-9]+)\.([0-9]+)\.([0-9]+)([^[:space:]]*) ]]; then
  major="${BASH_REMATCH[1]}"; minor="${BASH_REMATCH[2]}"; patch="${BASH_REMATCH[3]}"; suffix="${BASH_REMATCH[4]}"
  if (( major > 1 || (major == 1 && minor > 25) || (major == 1 && minor == 25 && patch > 5) )) ||
    { (( major == 1 && minor == 25 && patch == 5 )) && [[ "$suffix" != -* ]]; }; then
    echo "DDEV prerequisite passed: $version (requires $minimum or newer)."
    exit 0
  fi
fi
cat >&2 <<MESSAGE

PHP Rust setup requires DDEV $minimum or newer.
Detected: ${version:-DDEV missing or version unavailable}
Upgrade DDEV yourself in your terminal; this check does not upgrade software.

For a Homebrew installation:
  brew upgrade ddev/ddev/ddev
If Homebrew reports an untrusted DDEV formula, first run:
  brew trust --formula ddev/ddev/ddev
Then repeat the upgrade command.

For Debian/Ubuntu with the DDEV apt repository already configured:
  sudo apt-get update && sudo apt-get install ddev
Other installations: https://docs.ddev.com/en/stable/users/install/ddev-upgrade/

Verify with:
  ddev --version
Then rerun your original One Line Installer command from the parent directory
using a new empty destination. The incomplete configuration folder is preserved;
do not delete or overwrite an existing site to retry. No Drupal installation or
PHP Rust build should be attempted with this unsupported DDEV version.
MESSAGE
exit 1
