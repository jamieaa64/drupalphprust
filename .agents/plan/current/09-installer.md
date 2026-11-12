# 09. Portable one-line installation

Status: active

Destination: This repository

Local installation completed. Tracker landing and completion text are implemented locally; automatic local hostname setup is implemented and the existing-site hook passes; fresh-install and Coder acceptance remain open.

## Work

- Finish the canonical repository-based install and fix failures in the owning component.
- Register unresolved local runtime hostnames through DDEV before launch; user handles authentication. Skip already resolved names and preserve Coder private routing.
- Publish recovery instructions in the tracker even when registration fails; explain known compatibility gaps prominently.
- Provide labelled php. and rust. hostnames and print exact URLs; preserve settings and secret-free portable configuration.

- Use OLI launch_path, replacement welcome text and existing hooks; no upstream installer change needed.
- Preserve Markdown navigation and live preview; installed URLs/status remain credential-free.

## Test / finish

- [x] Observe completed installation and running web/phpr services.
- [x] Verify actual OLI launch opens the tracker; final message lists all three URLs.
- [x] Verify distinct runtime identities with explicit local DNS resolution.
- [x] Verify ordinary HTTPS URLs after user repairs DNS: PHP 200; Rust 500 (missing XMLWriter), not a routing failure.
- [ ] Fresh-install amended configuration; verify Coder and restart persistence.
- Five hostname tests, seven tracker tests, eight installer tests and repository checks pass. Existing-site setup hook reports both names ready and publishes the tracker. [Receipt](../../../docs/receipts/2026-11-12-tracker-landing.md).
- Verify named routes reach distinct services, TLS, Drupal trusted hosts and restart persistence; record remaining limitations.

## Upstream

Project work belongs here; send only proven generic defects to their owning upstream.

[Overall plan](README.md) · [Previous](08-optional-cms-gaps.md) · [Next](10-coder-tracker.md) · [Evidence / guidance](../../../docs/USAGE.md)
