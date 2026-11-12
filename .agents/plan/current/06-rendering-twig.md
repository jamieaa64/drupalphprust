# 06. Rendering and Twig semantics

Status: patched

Destination: PHP Rust

Stringable, reflection, object snapshots, regex offsets and magic-call fixes are patched locally.

## Work

- Prepare one contribution per behavior with a minimal PHP reproducer.
- Resolve remaining rendering defects revealed after the Fiber fix in separate patches.

## Test / finish

- Match Zend fixtures, including byte offsets and magic-call ordering.
- Verify rendered Drupal content contains no fatal errors or placeholder class names.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: strtr-stringable-map.patch, concat-stringable.patch, field-concat-stringable.patch, builtin-reflection.patch, object-vars-snapshot.patch, preg-byte-offset.patch, chained-magic-argument.patch

[Overall plan](README.md) · [Previous](05-callables-references.md) · [Next](07-sessions-http.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
