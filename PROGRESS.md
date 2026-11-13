# Drupal on PHP Rust

Updated: 2026-11-13

**Installer:** Bundled DDEV add-on gives the tracker its own hostname and uses
normal AIBP/DDEV deletion, requiring DDEV 1.25.5+. Add-on installation, rendered
routes and native cleanup are verified in an isolated fixture; full fresh
installation/browser acceptance remains open. An early version check now prints user-run
upgrade commands; DDEV’s version constraint blocks unsupported service starts. [Receipt](docs/receipts/2026-11-12-native-ddev-addon.md).

**Try it and take over:** [What works, known failures and next steps](docs/TRY-IT.md). The environment can be shared while compatibility tasks remain open.

**Agent tooling:** Containerised Serena and pinned Rust analysis tools; installer
choices for Codex/Claude and host/DDEV execution. Both MCP transports pass Rust
symbol lookup locally. Fresh Coder and authenticated clients remain unverified.
[Setup](docs/SERENA.md) · [Receipt](docs/receipts/2026-11-12-serena-ddev.md).

**Serena dashboard:** DDEV route and tracker button implemented; OLI registers
its hostname and deletion cleans up project-owned aliases. DDEV Freeform button
prepared and Terraform-validated, not published. [Receipt](docs/receipts/2026-11-12-serena-dashboard.md).

**Current installation:** Drupal CMS runs on PHP Rust for the verified homepage,
login, dashboard, Utility-page creation, editing and publishing journey. The
published synthetic page is accessible anonymously and survives a service restart.
Rendering is clean after a cache rebuild. All 49 comparison probes and 47 PHPT
cases in both compiler modes pass.
[What changed and evidence](docs/receipts/2026-11-13-authenticated-runtime.md).

**Release suite:** 1,756 passed, 3 failed, 2 ignored. Remaining failures cover
opcode accounting, source-size limits and the reference-allocation guard.

**Next:** Resolve those release failures; test a fresh One Line
Installer build and DDEV Freeform; then prepare discrete upstream contribution
packets. Broader runtime coverage and partial-request server blocking remain open.
No speed or complete Drupal parity claim. Standard PHP remains the comparison oracle.

**Research alongside fixes:** [Upstream submission process](.agents/plan/current/13-upstream-process.md) and [comparisons beyond speed](docs/COMPARISON.md), separating today's behavior from Drupal-change opportunities.
