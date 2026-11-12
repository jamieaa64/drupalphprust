# 04. Class loading and serialization

Status: patched

Destination: PHP Rust

## What this does

PHP autoloading locates a class or trait when code first refers to it. Traits
share methods between classes. Serialization saves object state so it can later
be reconstructed, including private inherited fields and enum values. Drupal's
services and cached objects rely on these language behaviors.

## What is implemented

The patches preserve trait names during autoload, handle shared traits reached
through multiple inheritance paths, avoid autoloading an empty class name, and
correct private-field and enum restoration. These are discrete reproduced bugs,
not an unfinished rewrite of PHP's class system.

## How it is tested

The combined runtime runner compares Zend and PHP Rust using trait-autoload,
trait-diamond, serialization-inheritance and enum-serialization probes, alongside
the remaining runtime fixtures. It rejects output differences and unexpected
stderr. The recorded combined run passes. Upstream corpus testing and the final
Drupal container/cache/browser checks remain submission gates, rather than proof
that these specific patches are unimplemented.

## Work

- Separate trait/autoload fixes from serialization fixes for upstream review.
- Audit case handling, inheritance and serialized field identity.

## Test / finish

- Compare each independent fixture with Zend.
- Run upstream tests and Drupal container/cache rebuild before preparing submissions.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: trait-autoload-case.patch, trait-diamond.patch, unserialize-private-fields.patch, enum-serialization.patch, empty-class-autoload.patch

[Overall plan](README.md) · [Previous](03-iterators-filesystem.md) · [Next](05-callables-references.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
