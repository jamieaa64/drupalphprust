# 10. Coder tracker and Serena dashboard integration

Status: active

Destination: Coder DDEV + this repository

The existing freeform template can gain a private app button. Integration is designed, not deployed.

Target confirmed: **DDEV Freeform** (`freeform`), not canvas-migration. Read-only
access to its active staging template is verified. The optional owner-only
Serena app is prepared in `coder/freeform/serena.tf`. Terraform validation and
two mocked plans passed against the downloaded active template. Publication,
workspace upgrade and actual Coder proxy acceptance remain pending.

Local DDEV dashboard routing and HTTP/STDIO Rust symbol lookup pass. OLI hostname
registration includes Serena; isolated tests verify cleanup removes only owned
aliases. Existing Mac hostname registration still needs the user's terminal.
See [receipt](../../../docs/receipts/2026-11-12-serena-dashboard.md) and
[Freeform instructions](../../../docs/CODER-SERENA.md).

## Work

- Add optional tracker startup/restart handling and an app resource to existing freeform.
- Keep installation independent of Coder credentials and template ownership.

## Test / finish

- Check private button access, Markdown refresh and workspace restart.
- Check local mode remains unchanged and a missing checkout reports not ready.

## Upstream

Proposed freeform change; no template update or upstream submission yet.

[Overall plan](README.md) · [Previous](09-installer.md) · [Next](11-fast-seed.md) · [Evidence / guidance](../../../docs/CODER-TRACKER.md)
