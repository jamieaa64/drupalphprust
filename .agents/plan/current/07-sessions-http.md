# 07. Sessions and HTTP serving

Status: partial

Destination: PHP Rust

Preconnect and session-cookie fixes exist, but partial HTTP requests can still block the sequential server. This is not a production-ready serving stack.

## Work

- Prepare separate server and session contributions.
- Reproduce partial-request blocking with a bounded test; design timeout/connection handling separately.

## Test / finish

- Run fixtures and check session persistence across requests.
- Verify Drupal login/logout and requests with early-disconnected clients.

## Upstream

Not submitted; prepare discrete fixes. Submit only after end-to-end acceptance and user approval.

Patches: server-preconnect.patch, session-regenerate-cookie.patch

[Overall plan](README.md) · [Previous](06-rendering-twig.md) · [Next](08-optional-cms-gaps.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
