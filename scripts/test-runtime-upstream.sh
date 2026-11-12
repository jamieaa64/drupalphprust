#!/usr/bin/env bash
# Run upstream's real Cargo suite with its pinned-version PHP oracle.
set -euo pipefail
root="$(cd "$(dirname "$0")/.." && pwd)"
if [[ "${1:-}" != --inside ]]; then
  cd "$root"
  exec ddev exec -s serena bash /var/www/html/scripts/test-runtime-upstream.sh --inside "$@"
fi
shift
export PATH="/usr/local/cargo/bin:$PATH"
export PHP_ORACLE="${PHP_ORACLE:-/opt/php-oracle/php}"
export CARGO_TARGET_DIR="${CARGO_TARGET_DIR:-/tmp/serena-target}"
export CARGO_BUILD_JOBS="${CARGO_BUILD_JOBS:-1}"
# Never let an inherited escape hatch silently bypass the differential tests.
unset PHPR_ALLOW_MISSING_ORACLE
"$PHP_ORACLE" -r 'if (PHP_VERSION !== "8.5.7" || ini_get("opcache.enable_cli")) { fwrite(STDERR, "Expected PHP 8.5.7 oracle with CLI OPcache disabled\n"); exit(1); } echo "Upstream oracle: PHP ", PHP_VERSION, "\n";'
cd "$root/.work/php-rust/php-rust"
exec cargo test --locked --release --no-fail-fast "$@"
