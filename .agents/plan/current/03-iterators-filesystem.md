# 03. Iterators and filesystem

Status: patched

Destination: PHP Rust

## What this does

Iterators let PHP walk collections without loading everything at once.
RecursiveCallbackFilterIterator walks a directory tree while a callback decides
which entries to keep. Drupal and its libraries use filesystem traversal and
metadata when discovering project files.

## What is implemented

The patches supply recursive callback filtering, make touch work on an existing
directory, and add SplFileInfo::getCTime(). The timestamp fix unblocked Drupal's
ProjectInfo code. A missing file must raise the expected exception without leaking
an internal warning into Drupal's custom error handler.

## How it is tested

`tests/test-iterator.sh` compares recursive-callback-iterator.php on Zend and PHP
Rust. `tests/test-oob-runtime.sh` includes touch-directory and spl-fileinfo-ctime;
output must match exactly with no unexpected stderr. The latter covers the custom
error-handler case. The recorded combined 29-probe run passes; that is focused
behavioral evidence, not a claim that every SPL class is implemented.

## Work

- Prepare separate iterator, touch and SplFileInfo contributions.
- Review callback filtering and missing-file error-handler behavior for parity.

## Test / finish

- Run each patch fixture under Zend and PHP Rust.
- Run the upstream gates and preserve the custom-error-handler regression.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: recursive-callback-iterator.patch, touch-directory.patch, spl-fileinfo-ctime.patch

[Overall plan](README.md) · [Previous](02-pdo-mysql.md) · [Next](04-class-loading.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
