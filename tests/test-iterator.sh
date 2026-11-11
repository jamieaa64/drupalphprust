#!/usr/bin/env bash
set -euo pipefail
lab_dir="$(cd "$(dirname "$0")" && pwd)"
project_dir="${1:?Usage: test-iterator.sh /absolute/project/path [image]}"
image="${2:-drupalphprust:release}"
mkdir -p "$lab_dir/results/iterator"
cd "$project_dir"
ddev exec php /dev/stdin < "$lab_dir/probes/recursive-callback-iterator.php" > "$lab_dir/results/iterator/zend.txt" 2> "$lab_dir/results/iterator/zend.stderr"
docker run --rm -i "$image" phpr /dev/stdin < "$lab_dir/probes/recursive-callback-iterator.php" > "$lab_dir/results/iterator/rust.txt" 2> "$lab_dir/results/iterator/rust.stderr"
test ! -s "$lab_dir/results/iterator/zend.stderr"
test ! -s "$lab_dir/results/iterator/rust.stderr"
diff -u "$lab_dir/results/iterator/zend.txt" "$lab_dir/results/iterator/rust.txt"
echo 'recursive_callback_iterator: PASS'
