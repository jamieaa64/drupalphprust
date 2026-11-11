> Historical investigation record imported on 2026-11-11. Paths and status below may be stale. Read ../../.agents/plan/current/README.md first.

# SQLite compatibility investigation — 10 November 2026

## Actual seed identified

Using the existing staging Coder session and read-only `coder ssh` commands:

- Server: `https://staging-coder.ddev.com`
- Workspace: `TestVersion1` (template `canvas-migration`)
- DDEV project and path: `migration-version-1`, `/home/coder/migration-version-1`
- Architecture: `x86_64`
- DDEV: `v1.25.5`; MariaDB image `11.8`; Docker `29.9.2`
- Drupal: `11.4.11`; MySQL driver; Drush bootstrap successful
- Enabled: `canvas`, `canvas_oauth`, `canvas_tools`, `simple_oauth`, `mcp_server`,
  `mcp_server_oauth`, `mcp_server_tool_bridge`, and Drupal CMS modules
- Installed dependency sizes: vendor 176 MB; node_modules 513 MB; public files 9 MB
- The seed repository has no HEAD commit, so provenance cannot be established by
  a Git SHA there. Use the installed Composer lock and installer-config hashes.
- Installed Composer lock SHA-256:
  `4abd0fdc8fd5bd19b694d16383c1fd27fe5f2c4ec6b4710a4ef610113bb3b78d`.
- The installer did not retain `.drupalaibp/drupal-cms-coder.json`. Capture now
  records that config provenance is unverified and packages the restore hooks
  from this reviewed branch, instead of assuming the installed site kept them.
- Confirmed `.mcp.json`, `node_modules/.package-lock.json` and
  `web/.ht.router.php` exist. The fast-restore prerequisites checked so far are
  present; this is not yet an executed capture/restore validation.

No database conversion, module changes, dependency installation or seed export was
performed. The snapshot and end-to-end runtime tests remain unexecuted. The
runtime image tested locally was ARM64, not the staging workspace's architecture.

## Reproduced blocker

Inspected the actual seed's
`web/core/modules/sqlite/src/Driver/Database/sqlite/Connection.php`.
For PHP >= 8.4, Drupal constructs its `SqliteConnection` subclass of `Pdo\Sqlite`,
registers SQL functions, then calls:

```php
$sqlite->createCollation('NOCASE_UTF8', Unicode::strcasecmp(...));
```

The pinned PHP Rust source is `72f688c3183b79858c01d250ce3999d86e143544`.
Its runtime reports PHP 8.5.7, so Drupal takes this PHP >= 8.4 path.
`sqlite-probe.php` exercises the relevant APIs in independent in-memory databases:

| Operation | Zend PHP 8.4.8 | PHP Rust 8.5.7 |
| --- | --- | --- |
| Subclass of Pdo\Sqlite, SELECT 1 | Pass | Pass |
| createFunction with first-class callable | Pass | Pass |
| createCollation and case-insensitive comparison | Pass | **Fail: undefined method** |
| BEGIN IMMEDIATE, prepared insert, commit, rollback | Pass | Pass |

Exact runtime failure:

```text
Error: Call to undefined method LabSqliteConnection::createCollation()
```

The probe uses `strcasecmp` for a minimal ASCII example; it does not establish
full Unicode collation compatibility. Failure occurs at method availability,
before the callback can run. The legacy `PDO::sqliteCreateCollation` also reports
absent, so forcing Drupal onto its older PHP path would not solve it.

## Reproduce

From the repository root, with the image already built:

```bash
php experiments/php-rust/sqlite-probe.php
```

```bash
python3 experiments/php-rust/lab.py check-runtime --driver sqlite
```

The first command passes on the tested Zend installation; the second exits 1
with the per-check report. No project files or persistent database are mounted
into the test container. `check-runtime --driver mysql` separately fails because
the image exposes only PDO SQLite, not PDO MySQL.

## Consequence and next implementation step

A SQLite seed cannot currently run this Drupal version on the pinned runtime.
Do not skip collation registration or substitute different comparison rules:
that would change database behavior and invalidate the comparison.

The next PHP Rust work is to implement correct SQLite collation callbacks (or
implement PDO MySQL to preserve the original database stack), test them against
Zend, then rerun Drupal bootstrap, OAuth, MCP and Canvas write tests. There may
be further compatibility gaps after this first blocker is fixed.

If SQLite becomes viable, build a fresh Drupal CMS SQLite seed using the same
locked packages and recipes. A MySQL SQL dump cannot simply be imported into
SQLite. Compare Zend/SQLite against PHP Rust/SQLite to isolate runtime effects;
retain Zend/MySQL as the original deployment baseline. The current snapshot tool
still intentionally supports MySQL/MariaDB only. No SQLite restoration support
is claimed or enabled.
