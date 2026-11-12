# 03. Runtime compatibility

Status: active

Next blocker: Fiber suspension inside a Generator.

## Work

- Preserve both continuations when a Fiber suspends during generator execution.
- Avoid replaying side effects; keep each runtime fix separate.

## Test / finish

- Compare fiber-generator-suspend.php with Zend; it currently panics on PHP Rust.
- Cover nested generators, repeated suspension, exceptions and finally; obtain review.

[Overall plan](README.md) · [Previous](02-static-references.md) · [Next](04-browser.md) · [Evidence / guidance](../../../docs/receipts/2026-11-12-fiber-generator-blocker.md)
