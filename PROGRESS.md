# Drupal on PHP Rust

Updated: 2026-11-12

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

**Current installation:** XMLWriter homepage blocker fixed locally. The unchanged PHP Rust homepage returns HTTP 200 and renders the expected hero; standard PHP still responds successfully. OO memory XMLWriter passes three Zend parity fixtures. [Task 16](.agents/plan/current/16-xmlwriter.md) · [Receipt](docs/receipts/2026-11-12-xmlwriter-memory.md). Earlier scoped-site Fiber / Generator suspension remains open.

**Current browser blockers:** The font-prefix bug is fixed by dynamic callable-reference preservation, verified after clearing render caches. Missing-route Fiber stalling, XPath namespace warnings and unresolved tokens remain; full browser acceptance fails. [Receipt](docs/receipts/2026-11-12-callable-unpack-reference.md).

**Still open:** Final Drupal flow and combined regressions; reference/reflection coverage; partial-request server blocking; separate upstream contributions. See [runtime completion](.agents/plan/current/01-fiber-generators.md).

**Test evidence:** 33 local patches; all 33 combined runtime probes pass on the XMLWriter candidate. No end-to-end success or upstream submissions. Local installation and tracker launch are verified; named runtime links resolve after user-managed DNS repair; automatic local hostname setup is now implemented; a fresh install of the amended hooks remains untested. Coder integration, clean seed and performance remain; optional CMS gaps are deferred.

**Research alongside fixes:** [Upstream submission process](.agents/plan/current/13-upstream-process.md) and [comparisons beyond speed](docs/COMPARISON.md), separating today's behavior from Drupal-change opportunities.
