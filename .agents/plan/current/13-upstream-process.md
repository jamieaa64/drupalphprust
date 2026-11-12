# 13. Work out upstream submission process

Status: pending

Destination: This repository + each owning upstream

Existing patch exports are not yet maintainer-ready submissions. Research and
local preparation can proceed before Drupal end-to-end acceptance.

## Work

- Verify each upstream's current contribution, AI-disclosure, licensing, issue/PR and test requirements; record source links and check dates.
- Check existing issues/changes for duplicates; choose submission order and explicit dependencies.
- Prepare independent branches, regression evidence, draft descriptions and exact user-run push/submission steps.

## Test / finish

- Dry-run one small contribution locally from clean upstream: reproduce before, pass after, run required gates.
- Confirm the package contains only its intended fix and prerequisites; no secrets or unrelated Drupal setup.
- Produce a reusable submission checklist and identify unanswered maintainer questions without posting them.

## Upstream

Publish only after end-to-end success and user approval. The user handles
authentication and pushes; plan how to handle review, revisions and patch retirement.

[Overall plan](README.md) · [Previous](12-benchmark.md) · [Next](14-drupal-opportunities.md) · [Submission preparation](../../../docs/UPSTREAM.md)
