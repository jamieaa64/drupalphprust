# 07. Sessions and HTTP serving

Status: patched

Destination: PHP Rust

## What this does

A session cookie connects consecutive browser requests to the same Drupal login.
When Drupal regenerates a session ID, the browser must receive its replacement.
Browsers can also open an idle connection before sending a request; that must not
freeze the server while other requests are waiting.

## What is implemented

The session patch emits the replacement cookie on ID regeneration; recorded
browser login persists. The preconnect patch handles idle speculative sockets.
Both fixes are implemented and have focused passing evidence.

## How it is tested

`tests/test-session-regenerate.py` exercises a disposable HTTP fixture on both
runtimes: start and double rotation, an existing cookie, flags, unrelated cookies
and cookies disabled. `tests/test-server-preconnect.py` checks serving a real asset
while an idle connection is open. These recorded passes are not a fresh test run.

## Separate remaining defect

A connection that sends only part of an HTTP request can still block the sequential
server. Idle preconnect handling does not fix that case. It now has its own
[partial-request handling task](15-http-partial-requests.md), so the two completed
patches need not be labelled unfinished. Production hardening remains unclaimed.

## Work

- Prepare separate server and session contributions.
- Keep partial-request handling separate from these two fixes.

## Test / finish

- Run fixtures and check session persistence across requests.
- Verify Drupal login/logout and requests with early-disconnected clients.

## Upstream

Upstream status: Not submitted.

Prepare discrete fixes; submit only after end-to-end acceptance and user approval.

Patches: server-preconnect.patch, session-regenerate-cookie.patch

[Overall plan](README.md) · [Previous](06-rendering-twig.md) · [Next](08-optional-cms-gaps.md) · [Evidence / guidance](../../../docs/UPSTREAM.md)
