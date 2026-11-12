# 05. Callables and references

Status: patched

Destination: PHP Rust

Local fixes cover callable dispatch and alias preservation, including static-property references.

## Work

- Prepare focused dispatch and reference contributions; document dependencies.
- Review alias lifetime across calls, returns, ArrayAccess and finally.

## Test / finish

- Run the combined reference/callable probes against Zend.
- Retain the Views/static-reference regression and obtain runtime review.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: builtin-object-dispatch.patch, callable-method-arguments.patch, callable-autoload.patch, cross-unit-reference-call.patch, dynamic-reference-builtin.patch, static-reference.patch, arrayaccess-reference.patch, return-reference.patch, static-property-reference.patch

[Overall plan](README.md) · [Previous](04-class-loading.md) · [Next](06-rendering-twig.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
