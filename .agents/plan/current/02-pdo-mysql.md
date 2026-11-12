# 02. PDO MySQL driver

Status: patched

Destination: PHP Rust

## What this does

PDO is the PHP database interface Drupal uses to send queries, bind values and
read results. This patch connects that interface to MySQL/MariaDB so Drupal can
store and retrieve its content and configuration. Without it, the existing
MariaDB-based site could not boot.

## What is implemented

The local driver passes the recorded MariaDB fixtures and Drupal database boot
checks. “Patched” describes this implemented driver scope; it does not promise
every PDO mode or compatibility with every MySQL configuration.

## How it is tested

`tests/test-database.sh` runs pdo-mysql, pdo-mysql-edge, database-regressions and
drupal-database probes against standard PHP and PHP Rust on a disposable DDEV
MariaDB project. It requires identical output and no unexpected stderr. A separate
restrictions probe checks deliberately rejected charset/comment/SQL-mode cases.
These tests were previously recorded as passing; they have not been rerun during
this documentation update. Full Drupal create/edit/view remains a separate gate.

## Work

- Audit transactions, bindings, fetch modes and error behavior against Zend.
- Split any discovered gaps into driver fixes with independent fixtures.

## Test / finish

- Run the MariaDB-backed driver suite and Drupal database operations.
- Prepare the driver contribution with its supported surface and explicit limitations.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: pdo-mysql.patch

[Overall plan](README.md) · [Previous](01-fiber-generators.md) · [Next](03-iterators-filesystem.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
