# 09. Portable one-line installation

Status: active

Destination: This repository

Local installation completed. Tracker landing and completion text are implemented locally; manual named-host DNS repair is verified; automatic hostname setup and Coder acceptance remain open.

## Work

- Finish the canonical repository-based install and fix failures in the owning component.
- Add installation-time hostname registration through DDEV, with authentication handled by the user; the manual repair is not yet automated.
- Provide labelled php. and rust. hostnames and print exact URLs; preserve settings and secret-free portable configuration.

- Use OLI launch_path, replacement welcome text and existing hooks; no upstream installer change needed.
- Preserve Markdown navigation and live preview; installed URLs/status remain credential-free.

## Test / finish

- [x] Observe completed installation and running web/phpr services.
- [x] Verify actual OLI launch opens the tracker; final message lists all three URLs.
- [x] Verify distinct runtime identities with explicit local DNS resolution.
- [x] Verify ordinary HTTPS URLs after user repairs DNS: PHP 200; Rust 500 (missing XMLWriter), not a routing failure.
- [ ] Fresh-install amended configuration; verify Coder and restart persistence.
- Seven tracker tests and repository checks pass. [Receipt](../../../docs/receipts/2026-11-12-tracker-landing.md).
- Verify named routes reach distinct services, TLS, Drupal trusted hosts and restart persistence; record remaining limitations.

## Upstream

Project work belongs here; send only proven generic defects to their owning upstream.

[Overall plan](README.md) · [Previous](08-optional-cms-gaps.md) · [Next](10-coder-tracker.md) · [Evidence / guidance](../../../docs/USAGE.md)
