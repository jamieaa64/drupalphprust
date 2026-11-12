# 08. Deferred CMS compatibility

Status: deferred

Destination: PHP Rust

Limited DOM/stream patches exist; XPath, stream writes and cron gaps remain.
XMLWriter memory support is now owned and patched in task 16; full XMLWriter
API coverage remains outside that focused contribution.

## Work

- Keep optional sitemap/klaro, aggregation and request cron exclusions explicit.
- After baseline acceptance, choose individual compatibility gaps to implement.

## Test / finish

- For each chosen gap, add a Zend parity fixture before changing the runtime.
- Re-enable only the affected feature and prove it works; no blanket CMS parity claim.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: dom-doctype.patch, stream-constants.patch

[Overall plan](README.md) · [Previous](07-sessions-http.md) · [Next](09-installer.md) · [Evidence / guidance](../../../docs/TESTING.md)
