# 03. Iterators and filesystem

Status: partial

Destination: PHP Rust

Recursive callback iteration and filesystem corrections exist locally.

## Work

- Prepare separate iterator, touch and SplFileInfo contributions.
- Review callback filtering and missing-file error-handler behavior for parity.

## Test / finish

- Run each patch fixture under Zend and PHP Rust.
- Run the upstream gates and preserve the custom-error-handler regression.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: recursive-callback-iterator.patch, touch-directory.patch, spl-fileinfo-ctime.patch

[Overall plan](README.md) · [Previous](02-pdo-mysql.md) · [Next](04-class-loading.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
