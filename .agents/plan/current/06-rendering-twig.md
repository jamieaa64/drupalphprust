# 06. Rendering and Twig semantics

Status: partial

Destination: PHP Rust

Specific rendering fixes exist. Builtin reflection descriptors cover only six named functions; broader reflection and the complete dashboard remain unverified.

## Work

- Audit reflection coverage needed by Drupal; implement missing descriptors with fixtures.
- Resolve remaining rendering defects revealed after the Fiber fix in separate patches.

## Test / finish

- Match Zend fixtures, including byte offsets and magic-call ordering.
- Verify rendered Drupal content contains no fatal errors or placeholder class names.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: strtr-stringable-map.patch, concat-stringable.patch, field-concat-stringable.patch, builtin-reflection.patch, object-vars-snapshot.patch, preg-byte-offset.patch, chained-magic-argument.patch

[Overall plan](README.md) · [Previous](05-callables-references.md) · [Next](07-sessions-http.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
