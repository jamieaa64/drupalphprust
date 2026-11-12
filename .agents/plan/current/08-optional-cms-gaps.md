# 08. Deferred CMS compatibility

Status: deferred

Destination: PHP Rust

Limited DOM/stream patches exist; XMLWriter, XPath, stream writes and cron gaps remain outside the agreed baseline.

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
