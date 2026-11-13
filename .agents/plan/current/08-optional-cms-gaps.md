# 08. Deferred CMS compatibility

Status: partial

Destination: PHP Rust

DOM namespace XPath and registered wrapper writes are patched; broader XPath,
default stream-context APIs and cron gaps remain.
XMLWriter memory support is now owned and patched in task 16; full XMLWriter
API coverage remains outside that focused contribution.

## Current implementation

Namespace XPath support is patched and matches independent Zend fixtures for
inheritance, shadowing, predicates and PHP namespace-node properties. The current
registered wrapper write patch fixes asset generation through `temporary://`.
The browser homepage now renders with styles, fonts and JavaScript. Registered
wrapper enumeration also preserves registration order and resets per request,
avoiding duplicate-registration warnings during Drupal subrequests.
The write fixture covers append, arrays, partial writes, contexts, resource input,
exceptions and zero/false/negative/oversized results. See the
[current receipt](../../../docs/receipts/2026-11-12-continuations-and-rendering.md).

## Work

- Keep optional sitemap/klaro, aggregation and request cron exclusions explicit.
- After baseline acceptance, choose individual compatibility gaps to implement.

## Test / finish

- For each chosen gap, add a Zend parity fixture before changing the runtime.
- Re-enable only the affected feature and prove it works; no blanket CMS parity claim.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: dom-doctype.patch, stream-constants.patch, dom-namespace-axis.patch, user-wrapper-write.patch, stream-wrapper-registry.patch

[Overall plan](README.md) · [Previous](07-sessions-http.md) · [Next](09-installer.md) · [Evidence / guidance](../../../docs/TESTING.md)
