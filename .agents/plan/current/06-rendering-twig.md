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
general solution, and the complete dashboard has not been verified. The rendering
fixes are patched; reflection coverage and full rendering acceptance remain open.
This is a specific limitation, not merely waiting for upstream submission.

## How it is tested

`tests/test-oob-runtime.sh` compares strtr-stringable, markup-string, field-concat,
twig-markup, builtin-reflection, object-vars-copy, preg-byte-offset and
chained-magic-argument probes with Zend. Exact output and clean stderr are required.
After the Fiber blocker is fixed, render the real dashboard/page form and inspect
content and diagnostics; a successful HTTP status alone is insufficient.

## Work

- Audit reflection coverage needed by Drupal; implement missing descriptors with fixtures.
- Resolve remaining rendering defects revealed after the Fiber fix in separate patches.

## Test / finish

- Match Zend fixtures, including byte offsets and magic-call ordering.
- Verify rendered Drupal content contains no fatal errors or placeholder class names.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: strtr-stringable-map.patch, concat-stringable.patch, field-concat-stringable.patch, builtin-reflection.patch, object-vars-snapshot.patch, preg-byte-offset.patch, chained-magic-argument.patch

[Overall plan](README.md) · [Previous](05-callables-references.md) · [Next](07-sessions-http.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
