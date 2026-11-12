# 05. Callables and references

Status: patched

Destination: PHP Rust

## What this does

A callable is a function or method passed around for later execution. A PHP
reference lets two names address the same stored value instead of copying it.
Drupal uses both heavily: callbacks invoke services, while render arrays and
Views depend on changes reaching the original array or static property.

## What is implemented

Local fixes handle reproduced callable dispatch/autoload errors and preserve
references across calls, returns, ArrayAccess and static properties. The Views
assignment `static::$pageRenderArray = &$element` now compiles; its regression also
checks aliases and finally execution.

## How it is tested

`tests/test-oob-runtime.sh` compares callable-autoload, cross-unit-reference,
dynamic-reference, static-reference, arrayaccess-reference, return-reference and
static-property-reference probes, among others, against Zend. The recorded
29-probe combined run passes. The Views class-load probe also succeeded before
the final finally amendment; full Drupal flow is still outstanding.

## Scope limits

Named/spread/dynamic reference-argument paths need further investigation. Typed
static-property enforcement and additional expression forms are outside the
specific Views fix. Unknown broader coverage does not undo the tested fixes;
confirmed new defects should receive their own reproducer and task.

## Work

- Prepare focused dispatch and reference contributions; document dependencies.
- Probe named/spread/dynamic reference arguments; implement each confirmed gap separately.

## Test / finish

- Run the combined reference/callable probes against Zend.
- Retain the Views/static-reference regression and obtain runtime review.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: builtin-object-dispatch.patch, callable-method-arguments.patch, callable-autoload.patch, cross-unit-reference-call.patch, dynamic-reference-builtin.patch, static-reference.patch, arrayaccess-reference.patch, return-reference.patch, static-property-reference.patch

[Overall plan](README.md) · [Previous](04-class-loading.md) · [Next](06-rendering-twig.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
