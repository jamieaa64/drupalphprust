> Historical investigation record imported on 2026-11-11. Paths and status below may be stale. Read ../../.agents/plan/current/README.md first.

# Recursive callback iterator support

`patches/recursive-callback-iterator.patch` adds PHP's
`RecursiveCallbackFilterIterator` and repairs two existing SPL behaviors needed
by Drupal's extension discovery:

- Recursive children retain the callback and subclass identity.
- `RecursiveDirectoryIterator::current()` honors `CURRENT_AS_SELF`.
- `RecursiveIteratorIterator` walks live iterators lazily. Its previous eager
  collection retained multiple references to the same mutable directory iterator,
  causing different entries to report the final filename.

The traversal handles the three ordering modes, depth limits and
`CATCH_GET_CHILD`, including exceptions from child detection and retrieval.
These are focused compatibility fixes, not a claim of complete SPL conformance.

## Build and reproduce

The iterator image includes the previous PDO MySQL patch. From the uploaded lab
in the disposable Coder workspace:

```sh
docker build -f Dockerfile.iterator -t canvas-phpr:iterator .
bash test-iterator.sh /home/coder/php-rust-migration
bash test-database.sh /home/coder/php-rust-migration php-rust-migration canvas-phpr:iterator
bash install-coder-runtime.sh /home/coder/php-rust-migration canvas-phpr:iterator
```

The iterator runner compares stdout exactly against standard PHP in DDEV and
rejects unexpected stderr. Fixtures cover arrays, filesystem pruning, multiple
files using Drupal's `CURRENT_AS_SELF` pattern, traversal ordering, depth limits,
subclass propagation, invalid arguments, callback failures and inaccessible
children. Temporary filesystem fixtures are removed in `finally`.

The patch is separate from `pdo-mysql.patch`; the original baseline and the
PDO-only image remain available. No upstream publication or full upstream PHP
conformance/performance suite is included in this experiment.

## Verified result — 10 November 2026

The compiled image passes the complete iterator fixture byte-for-byte against
DDEV's standard PHP. All four PDO/database comparison suites and the database
fail-closed security probe also pass.

Full Drupal bootstrap now advances beyond extension discovery into Symfony's
service-container compilation. It exits 255 because PHP Rust cannot compile
`web/core/lib/Drupal/Core/DependencyInjection/AutowireTrait.php`, reported by
`ContainerBuilder::getReflectionClass()`. This is a separate compiler gap;
full Drupal and OAuth/MCP readiness have not yet passed. The normal Zend control
remains the main application runtime.

The final candidate was installed in `php-rust-migration`. The installed
`ddev phpr` matches the complete Zend iterator fixture; the installed bootstrap
reproduces the AutowireTrait failure. Zend still boots Drupal 11.4.12 with the
six required Canvas/OAuth/MCP modules and a working MySQL connection.

Build receipt:

- Iterator patch SHA256: `7f9bdbdece57813c636276481ed275a81fa5c5a27763da7585c55d61c4e7c5aa`.
- Image ID: `sha256:786b7c1081774f3a2425f862d6a14bd92199c7c2847fdd1820a7b48d59c2286d`.
