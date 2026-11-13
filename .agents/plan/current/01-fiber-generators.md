# 01. Complete Drupal runtime support

Status: active

Destination: PHP Rust + this repository

The selected installation now completes login, dashboard, Utility-page creation,
editing, publishing and anonymous viewing on PHP Rust. The page survives restart.
This is a scoped CMS journey, not an inventory of every remaining runtime defect.

## Current browser evidence

Explicit Generator continuations cover repeated suspension, changed resume depth,
delegation and finally cleanup. `iterator_to_array` also retains partial collection
across suspension. Unsupported native helper boundaries reject safely. References,
stream wrappers and date fixes were needed to complete the form journey and remain
owned by tasks 05, 06 and 08. See the
[current receipt](../../../docs/receipts/2026-11-13-authenticated-runtime.md).

## Work

- Fix Fiber/Generator continuations, including nested suspension, exceptions and finally.
- Fix further reproduced runtime failures separately; do not patch Drupal to conceal them.

## Test / finish

- [x] Fix the Views static-property reference compiler gap and add its regression.
- [x] Match Zend on the implemented Fiber/Generator fixtures and obtain runtime review.
- [x] Verify the dashboard and page creation form.
- [x] Verify create → edit → publish → anonymous view through PHP Rust.
- [x] Run the combined probes, both PHPT modes and release suite; record the final candidate result in the receipt.
- [ ] Resolve the three release failures through [task 17](17-release-gates.md) before claiming a green release candidate.

## Upstream

- [ ] Finish separate local contribution units with fixtures across tasks 02–08.
- [ ] Review submissions after end-to-end success and user approval.

Then [package a reusable seed](11-fast-seed.md) and [compare startup/page performance](12-benchmark.md).
The superseded checklist came from the old drupal-runtime-comparison/PROGRESS.md
(11 November); its unchecked Views item is now resolved, not a new blocker.

Patches: fiber-static-dispatch.patch, fiber-callback-trampoline.patch, fiber-generator-continuation.patch, fiber-generator-collector.patch

[Overall plan](README.md) · [Next](02-pdo-mysql.md) · [Evidence / guidance](../../../docs/HANDOVER.md) · [Fiber reproducer](../../../docs/receipts/2026-11-12-fiber-generator-blocker.md)
