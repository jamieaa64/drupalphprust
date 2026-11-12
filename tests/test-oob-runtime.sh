#!/usr/bin/env bash
set -euo pipefail
lab_dir="$(cd "$(dirname "$0")" && pwd)"
project_dir="${1:?Pass the disposable DDEV project path}"
image="${2:-drupalphprust:release}"
project_name="${3:-drupalphprust}"
[[ "$project_name" =~ ^[a-zA-Z0-9-]+$ ]] || exit 2
mkdir -p "$lab_dir/results/runtime"
cd "$project_dir"
for probe in trait-autoload trait-diamond serialization-inheritance countable closure-property callable-autoload fiber-callable cross-unit-reference fiber-static fiber-callback dom-doctype strtr-stringable enum-serialization dynamic-reference markup-string static-reference field-concat twig-markup arrayaccess-reference builtin-reflection return-reference touch-directory property-stack object-vars-copy stream-constants preg-byte-offset chained-magic-argument static-property-reference spl-fileinfo-ctime; do
  ddev exec php /dev/stdin < "$lab_dir/probes/$probe.php" > "$lab_dir/results/runtime/$probe-zend.txt" 2> "$lab_dir/results/runtime/$probe-zend.stderr"
  docker run --rm -i --volumes-from "ddev-${project_name}-web" -w /var/www/html "$image" phpr /dev/stdin < "$lab_dir/probes/$probe.php" > "$lab_dir/results/runtime/$probe-rust.txt" 2> "$lab_dir/results/runtime/$probe-rust.stderr"
  test ! -s "$lab_dir/results/runtime/$probe-zend.stderr"
  test ! -s "$lab_dir/results/runtime/$probe-rust.stderr"
  diff -u "$lab_dir/results/runtime/$probe-zend.txt" "$lab_dir/results/runtime/$probe-rust.txt"
  printf '%s: PASS\n' "$probe"
done
