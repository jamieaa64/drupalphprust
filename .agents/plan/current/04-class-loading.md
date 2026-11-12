# 04. Class loading and serialization

Status: patched

Destination: PHP Rust

Local patches cover trait loading, empty classes, private fields and enums.

## Work

- Separate trait/autoload fixes from serialization fixes for upstream review.
- Audit case handling, inheritance and serialized field identity.

## Test / finish

- Compare each independent fixture with Zend.
- Run upstream tests and Drupal container/cache rebuild before preparing submissions.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: trait-autoload-case.patch, trait-diamond.patch, unserialize-private-fields.patch, enum-serialization.patch, empty-class-autoload.patch

[Overall plan](README.md) · [Previous](03-iterators-filesystem.md) · [Next](05-callables-references.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
