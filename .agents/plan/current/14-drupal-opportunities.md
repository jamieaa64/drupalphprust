# 14. Benefits now versus Drupal changes

Status: pending

Destination: This repository; possible Drupal / PHP Rust proposals

Explore where the runtimes differ today and which opportunities need application
changes. No benefit from async, Rust or persistent execution is assumed.

## Work

- Build an opportunity register: unchanged Drupal today, runtime-only changes, Drupal changes, or combined changes.
- Explore async external I/O, concurrent independent work, background jobs and persistent workers with request-state isolation.
- Specify the smallest prototype, likely benefit, compatibility cost and owning project for each promising idea.

## Test / finish

- Compare unchanged Drupal first, then one controlled change at a time on both runtimes where supported.
- Check output/permissions, transactions, cancellation, ordering and cross-request state isolation as well as resource use.
- Retain negative results; recommend changes only with measured evidence and maintenance costs.

## Upstream

Draft an architectural proposal separately from language-parity fixes. Verify
Drupal's current processes before proposing changes; submit only with user approval.

[Overall plan](README.md) · [Previous](13-upstream-process.md) · [Exploration design](../../../docs/COMPARISON.md) · [Contribution process](13-upstream-process.md)
