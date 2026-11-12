# 01. Fiber / Generator support

Status: active

Destination: PHP Rust

Current blocker: suspending a Fiber inside a Generator panics. Earlier Fiber dispatch fixes are already patched.

## Work

- Preserve both continuations without replaying side effects.
- Handle nested suspension, exceptions and finally; prepare a focused continuation patch.

## Test / finish

- Match Zend on fiber-generator-suspend.php and nested/repeated suspension fixtures.
- Obtain runtime review; rerun Drupal rendering and the combined probes.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: fiber-static-dispatch.patch, fiber-callback-trampoline.patch

[Overall plan](README.md) · [Next](02-pdo-mysql.md) · [Evidence / guidance](../../../docs/receipts/2026-11-12-fiber-generator-blocker.md)
