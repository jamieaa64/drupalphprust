# 07. Sessions and HTTP serving

Status: patched

Destination: PHP Rust

Local patches address preconnect handling and session-cookie regeneration.

## Work

- Prepare separate server and session contributions.
- Review cookie regeneration and connection handling beyond the happy path.

## Test / finish

- Run fixtures and check session persistence across requests.
- Verify Drupal login/logout and requests with early-disconnected clients.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: server-preconnect.patch, session-regenerate-cookie.patch

[Overall plan](README.md) · [Previous](06-rendering-twig.md) · [Next](08-optional-cms-gaps.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
