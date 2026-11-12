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

- Bundled `php-rust` add-on uses native pre-delete on DDEV 1.25.5+: ordinary AIBP deletion releases owned aliases and previews. DDEV builds a project-scoped image. No bespoke delete command or upstream patch is needed.
- Dedicated tracker service and hostname; dynamic absolute OLI launch URL. Runtime and hostname setup run before completion.
- Add-on installation/removal, Compose rendering and native deletion failure/success verified with isolated DDEV 1.25.5 fixture. Full fresh installation/browser checks remain open. See [receipt](../../../docs/receipts/2026-11-12-native-ddev-addon.md).

- Added early DDEV version check with user-run upgrade/trust commands. Native DDEV version constraint enforces the stop before Drupal installation, because OLI hooks are best-effort. Five isolated prerequisite tests pass; see [receipt](../../../docs/receipts/2026-11-12-ddev-prerequisite.md).

## Test / finish

- Serena service and OLI choices implemented: Codex, Claude Code, both or manual;
  host/Coder VM or DDEV. STDIO and internal HTTP Rust symbol tests pass locally.
  Existing config is preserved; users handle trust and sign-in. Fresh OLI/Coder and
  authenticated client sessions remain unverified. See [Serena receipt](../../../docs/receipts/2026-11-12-serena-ddev.md).

- [x] Observe completed installation and running web/phpr services.
- [x] Verify actual OLI launch opens the tracker; final message lists all three URLs.
- [x] Verify distinct runtime identities with explicit local DNS resolution.
- [x] Verify ordinary HTTPS URLs after user repairs DNS: PHP 200; Rust 500 (missing XMLWriter), not a routing failure.
- [ ] Fresh-install amended configuration; verify Coder and restart persistence.
- Five hostname tests, seven tracker tests, eight installer tests and repository checks pass. Existing-site setup hook reports both names ready and publishes the tracker. [Receipt](../../../docs/receipts/2026-11-12-tracker-landing.md).
- Verify named routes reach distinct services, TLS, Drupal trusted hosts and restart persistence; record remaining limitations.

## Upstream

No upstream change is needed for the requested manual-upgrade guidance and native version constraint. OLI support for aborting before any scaffold is created remains optional; automatic upgrades are outside the requested behavior. Nothing submitted.

[Overall plan](README.md) · [Previous](08-optional-cms-gaps.md) · [Next](10-coder-tracker.md) · [Evidence / guidance](../../../docs/USAGE.md)
