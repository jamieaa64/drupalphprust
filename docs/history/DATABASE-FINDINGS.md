> Historical investigation record imported on 2026-11-11. Paths and status below may be stale. Read ../../.agents/plan/current/README.md first.

# Experimental PDO MySQL driver

The pinned PHP Rust source already implements mysqli using Rust's `mysql` crate.
Drupal uses PDO instead. This patch adds `Pdo\Mysql` and PDO dispatch over the
existing wire client, preserving the Drupal CMS installation and MariaDB database.

Source: `francescotinti/php-rust`, commit
`72f688c3183b79858c01d250ce3999d86e143544`, development branch
`codex/drupal-pdo-mysql` in the temporary source checkout. The durable source change
is `patches/pdo-mysql.patch`; no upstream changes have been published.

## Scope

Implemented paths include DSN connection, the PHP 8.4 PDO factory/subclass,
Drupal's PDO options, positional/named parameters, native and client-emulated
binding, buffered fetching, numeric/string/null conversions, matched-row counts,
last insert IDs, transactions, savepoints and PDO error reporting.

This candidate targets DDEV's MariaDB 11.8. `inTransaction()` queries MariaDB's
actual session state. It is **not a general MySQL-compatible production driver**:
MySQL proper and older MariaDB servers have not been validated. It rejects
unsupported TLS options, persistent connections, unbuffered queries, executable
SQL comments, column metadata and unsafe session charsets. Advanced PDO features,
large results and stored procedures are outside the validated subset.

Single-statement enforcement uses MySQL's prepared-statement protocol even for
client-emulated queries. The PHP scanner is not the security boundary. This also
means additional round trips and that commands must be preparable (MariaDB
10.6.2+ supports the relevant commands). The original mysqli behavior is unchanged.
Both quoting and placeholder scanning consult current connection settings;
unsupported multibyte charsets fail closed after `SET NAMES` as well as at connect.
This correctness-first implementation has not been optimized or benchmarked.

## Reproduce in the disposable Coder workspace

From `/home/coder/php-rust-lab`, with this experiment's files uploaded:

```sh
docker build -f Dockerfile.pdo-mysql -t canvas-phpr:pdo-mysql .
bash test-database.sh /home/coder/php-rust-migration php-rust-migration
bash install-coder-runtime.sh /home/coder/php-rust-migration canvas-phpr:pdo-mysql
```

The tests use the DDEV-only `db` database/user/password defaults. Writes are to
connection-local temporary tables. The Drupal test reads `core.extension` from
configuration, uses Drupal's real connection/query classes, and does not require
a service container. Outputs and diagnostics go under ignored `results/database`.
The runner requires successful processes, empty unexpected stderr, and exact
Zend/Rust output matches. The restrictions probe separately checks deliberate
fail-closed behavior rather than claiming parity for unsupported features.

Tests cover binary values, Unicode, injection-shaped bound values, numeric and
null values, native and emulated statements, parameter errors, SQLSTATEs, silent
errors, transactions/savepoints, changed versus matched rows, repeated parameters,
references, autocommit, cursor reuse, and existing SQLite/mysqli behavior.

## Full Drupal remains a separate gate

With the initial PDO patch, Drupal bootstrap advanced beyond the missing
`Pdo\Mysql` class and failed at `RecursiveCallbackFilterIterator` in module
discovery. That missing SPL class is unrelated to database connectivity. The
main migration application remains on Zend until full bootstrap and authenticated
OAuth/MCP checks succeed. Passing database tests is not a working-agent or speed
claim. The separate SQLite `createCollation()` gap is unchanged.

## Validation result — 10 November 2026

The final hardened image passed all four byte-for-byte fixture comparisons against
DDEV PHP 8.4 and the separate charset/comment/ANSI_QUOTES fail-closed probe:

- `pdo-mysql`: core PDO operations, emulated and native.
- `pdo-mysql-edge`: numeric boundaries, autocommit, cursor reuse and error cases.
- `database-regressions`: existing SQLite scalar functions/rollback and mysqli.
- `drupal-database`: Drupal 11.4.12's unmodified connection and query wrappers.
- `pdo-mysql-restrictions`: unsafe session charsets, executable comments and
  multiple statements under ANSI_QUOTES.

This is a focused integration suite, not the upstream project's full PHP corpus,
release test battery or performance certification. Those broader gates have not
been run, and this patch is not an upstream runtime release.

The candidate was installed into `php-rust-migration` and its installed `ddev phpr`
passed the Drupal database fixture against the Zend reference. Standard PHP still
bootstraps Drupal 11.4.12 with all six required Canvas/OAuth/MCP modules. Installed
PHP Rust reports both `mysql` and `sqlite` PDO drivers; its full bootstrap exits
255 at the missing iterator class noted above.

Verified build receipt:

- Patch SHA256: `0e4993ff5684b35d8f7effc113bb8281f4146350c975989c18fd845280b700a6`.
- Runtime image ID: `sha256:034a8d9417bc6c9146270d4e2a86af9581816ca802888eaf9c0777f0ca1020a5`.
- Original pinned runtime image remains available; only the isolated test
  workspace's experimental PHP Rust binaries were replaced.
