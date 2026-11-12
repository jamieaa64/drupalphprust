# 01. Complete Drupal runtime support

Status: active

Destination: PHP Rust + this repository

Fiber/Generator suspension was reproduced on the earlier reduced-feature site,
not a complete inventory of remaining defects. The fresh installation now serves
its homepage and anonymous login form after task 16's XMLWriter fix; authenticated
create/edit/view still needs validation. Continue through Drupal and minimise each
new failure. See the [XMLWriter receipt](../../../docs/receipts/2026-11-12-xmlwriter-memory.md).

## Current browser evidence

The missing-font URL enters a Canvas 404 render loop whose child Fiber reports
running after control returns to its caller. A bounded nested-render fixture also
panics on Generator/Fiber suspension; the exact connection is not yet established.
See [current receipt](../../../docs/receipts/2026-11-12-callable-unpack-reference.md).

## Work

- Fix Fiber/Generator continuations, including nested suspension, exceptions and finally.
- Fix further reproduced runtime failures separately; do not patch Drupal to conceal them.

## Test / finish

- [x] Fix the Views static-property reference compiler gap and add its regression.
- [ ] Match Zend on Fiber/Generator fixtures and obtain runtime review.
- [ ] Verify the dashboard and page creation form.
- [ ] Verify create → edit → view through PHP Rust.
- [ ] Run all regressions against the final combined build; the earlier 29-probe pass is not this final gate.

## Upstream

- [ ] Finish separate local contribution units with fixtures across tasks 02–08.
- [ ] Review submissions after end-to-end success and user approval.

Then [package a reusable seed](11-fast-seed.md) and [compare startup/page performance](12-benchmark.md).
The superseded checklist came from the old drupal-runtime-comparison/PROGRESS.md
(11 November); its unchecked Views item is now resolved, not a new blocker.

Patches: fiber-static-dispatch.patch, fiber-callback-trampoline.patch

[Overall plan](README.md) · [Next](02-pdo-mysql.md) · [Evidence / guidance](../../../docs/HANDOVER.md) · [Fiber reproducer](../../../docs/receipts/2026-11-12-fiber-generator-blocker.md)
