# 06. Rendering and Twig semantics

Status: partial

Destination: PHP Rust

## What this does

Twig turns Drupal templates and render values into HTML. Its compiler inspects
PHP functions through reflection, tokenizes text using regex offsets, and expects
objects representing markup to convert to strings correctly.

## What is implemented

Local fixes cover reproduced Stringable conversion, object snapshots, Unicode
regex byte offsets and chained magic-property argument reads. Their focused
fixtures pass in the recorded combined run.

## Why this task is still partial

Builtin reflection currently describes only six named functions rather than a
general solution. The dashboard and published Utility-page rendering are verified;
broader reflection and rendering coverage remain open.
This is a specific limitation, not merely waiting for upstream submission.

## How it is tested

`tests/test-oob-runtime.sh` compares strtr-stringable, markup-string, field-concat,
twig-markup, builtin-reflection, object-vars-copy, preg-byte-offset and
chained-magic-argument probes with Zend. Exact output and clean stderr are required.
The real dashboard, page form and published content have been inspected. Retain
those checks when extending coverage; a successful HTTP status alone is insufficient.

## Work

- Patched dynamic callable unpacking references; the Mercury font prefix is
  correct after rebuilding Drupal caches. Array-copy isolation is tested.
  Follow-up namespace, token-scanner and Fiber fixes are implemented separately.
  See [receipt](../../../docs/receipts/2026-11-12-callable-unpack-reference.md).
- Patched extended regex comments so token scanning resolves `[site:name]`.
- Patched string/natural multisort to invoke Stringable conversion. Pure Drupal
  labels pass; side-effectful conversion order remains different from Zend.
  [Receipt](../../../docs/receipts/2026-11-12-continuations-and-rendering.md).
- Audit reflection coverage needed by Drupal; implement missing descriptors with fixtures.
- Resolve remaining rendering defects revealed after the Fiber fix in separate patches.

- Binary preg replacement preserves Drupal date markers. Current-time DateTime
  constructors clear stale parse errors, allowing revision timestamp validation.
  Multibyte case conversion handles translated-label Stringable objects.
  [Receipt](../../../docs/receipts/2026-11-13-authenticated-runtime.md).

## Test / finish

- Match Zend fixtures, including byte offsets and magic-call ordering.
- Verify rendered Drupal content contains no fatal errors or placeholder class names.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: strtr-stringable-map.patch, concat-stringable.patch, field-concat-stringable.patch, builtin-reflection.patch, object-vars-snapshot.patch, preg-byte-offset.patch, chained-magic-argument.patch, callable-unpack-reference.patch, preg-extended-comments.patch, multisort-stringable.patch, preg-binary-replacement.patch, date-current-errors.patch, mb-stringable.patch

[Overall plan](README.md) · [Previous](05-callables-references.md) · [Next](07-sessions-http.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
