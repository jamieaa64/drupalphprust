#!/usr/bin/env bash
# Run against an explicitly named disposable DDEV project.
# Zend runs in DDEV; candidate shares its project volumes and network.
set -euo pipefail
lab_dir="$(cd "$(dirname "$0")" && pwd)"
project_dir="${1:?Usage: test-database.sh /absolute/project/path ddev-project-name [image]}"
project_name="${2:?Pass the isolated DDEV project name}"
runtime_image="${3:-drupalphprust:release}"
[[ "$project_name" =~ ^[a-zA-Z0-9][a-zA-Z0-9-]*$ ]] || exit 2
mkdir -p "$lab_dir/results/database"
cd "$project_dir"
# Fail if the expected container does not exist; never start another project.
docker inspect "ddev-${project_name}-web" >/dev/null
docker image inspect "$runtime_image" >/dev/null
for probe in pdo-mysql pdo-mysql-edge database-regressions drupal-database; do
  ddev exec php /dev/stdin < "$lab_dir/probes/$probe.php" > "$lab_dir/results/database/$probe-zend.txt" 2> "$lab_dir/results/database/$probe-zend.stderr"
  docker run --rm -i --network "ddev-${project_name}_default" \
    --volumes-from "ddev-${project_name}-web" -w /var/www/html -e IS_DDEV_PROJECT=true \
    "$runtime_image" phpr /dev/stdin < "$lab_dir/probes/$probe.php" > "$lab_dir/results/database/$probe-rust.txt" 2> "$lab_dir/results/database/$probe-rust.stderr"
  # Unexpected diagnostics are failures, not output silently dropped by diff.
  test ! -s "$lab_dir/results/database/$probe-zend.stderr"
  test ! -s "$lab_dir/results/database/$probe-rust.stderr"
  diff -u "$lab_dir/results/database/$probe-zend.txt" "$lab_dir/results/database/$probe-rust.txt"
  printf '%s: PASS\n' "$probe"
done
docker run --rm -i --network "ddev-${project_name}_default" \
  --volumes-from "ddev-${project_name}-web" -w /var/www/html -e IS_DDEV_PROJECT=true \
  "$runtime_image" phpr /dev/stdin < "$lab_dir/probes/pdo-mysql-restrictions.php" > "$lab_dir/results/database/restrictions.txt" 2> "$lab_dir/results/database/restrictions.stderr"
test ! -s "$lab_dir/results/database/restrictions.stderr"
grep -Fx 'unsafe_charset_comments_and_ansi_quotes: PASS' "$lab_dir/results/database/restrictions.txt"
