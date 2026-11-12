# 15. Partial HTTP request handling

Status: pending

Destination: PHP Rust

## What this does

An HTTP client can connect, send only part of its headers or body, then stall.
The recorded sequential-server limitation is that this can hold up unrelated
requests. Fixing idle browser preconnections does not cover this case.

## Work

- Reproduce the recorded blocking behavior with a bounded local fixture.
- Implement appropriate request deadlines or connection scheduling without changing valid request behavior.

## Test / finish

- While one client stalls mid-request, verify a second valid request completes within a declared bound.
- Check timeout cleanup and valid slow/fragmented requests; avoid an unbounded test hang.
- Rerun the existing preconnect and session tests. No new fix or passing regression exists yet for this task.

## Upstream

Upstream status: Not submitted.

Keep this server change independent of session-cookie and idle-preconnect fixes.
Prepare a minimal reproduction and regression for upstream review.

[Overall plan](README.md) · [Previous](14-drupal-opportunities.md) · [Existing HTTP fixes](07-sessions-http.md) · [Recorded limitation](../../../docs/HANDOVER.md)
