# 02. PDO MySQL driver

Status: partial

Destination: PHP Rust

MariaDB fixtures and Drupal boot work with the local driver patch; complete PDO compatibility is not established.

## Work

- Audit transactions, bindings, fetch modes and error behavior against Zend.
- Split any discovered gaps into driver fixes with independent fixtures.

## Test / finish

- Run the MariaDB-backed driver suite and Drupal database operations.
- Prepare the driver contribution with its supported surface and explicit limitations.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: pdo-mysql.patch

[Overall plan](README.md) · [Previous](01-fiber-generators.md) · [Next](03-iterators-filesystem.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
