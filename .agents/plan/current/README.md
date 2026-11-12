# Current plan: Drupal CMS on PHP Rust

Updated 2026-11-12. Owner: this repository. Read `docs/HANDOVER.md` to resume.

## Objective and finish line

A reproducible DDEV project, created through the One Line Installer, runs Drupal
CMS on PHP Rust and standard PHP, supports a basic browser acceptance journey,
and produces defensible startup/runtime comparisons. The repository must be enough
for another agent to continue without chat history or the old migration workspace.

The browser journey (homepage → login → create → edit → view) is an acceptance
check, not the project's identity or the only goal.

## Work sequence

| Order | Work | Status / exit condition |
| --- | --- | --- |
| 1 | Extract a standalone repository, plans, patches and handover | Prepared locally; portable reconstruction verified (29 patches / 35 source files); published by user to GitHub main |
| 2 | Fix Views static-property reference assignment lowering | Fixed; old image fails fixture, new image matches Zend; alias/rebinding/finally regression and 28-probe suite pass |
| 3 | Continue Drupal compatibility fixes | Each failure gets a minimal Zend/Rust fixture, narrow patch and review |
| 4 | Prove browser acceptance | Homepage/login verified earlier; dashboard/create/edit/view require new evidence |
| 5 | Validate the whole combined runtime | Expanded differential suite, database, SPL and HTTP session/server tests pass |
| 6 | Smoke-test One Line Installer + DDEV | One clean local-source install; hook success checked independently of installer exit |
| 7 | Smoke-test in Coder freeform | Same install path, project lifecycle, internal and authenticated external access |
| 8 | Prepare fast restore/build workflow | Capture disposable seed, verify restore identity, separate build/start/restore timings |
| 9 | Compare standard PHP and PHP Rust | Release build, pinned identical application, raw valid samples and limitations |
| 10 | Prepare discrete upstream submissions | Separate runtime fixes by behavior; no submission until E2E and user decision |

The user explicitly does not need the full One Line Installer test matrix yet.
Repository/static checks are in scope now; full installer portability, reruns,
parallel sites and lifecycle matrix are in `../future/README.md`.

## Verified scope, not blanket success

Earlier runtime work verified database fixtures and recursive iterators; individual
fixes now cover reference calls, rendering/Stringable behavior, ArrayAccess,
Twig builtin reflection, session cookie rotation, regex byte offsets and chained
magic getters. Exact claims and caveats: `docs/HANDOVER.md`.
The expanded 29-probe differential suite now passes on the extracted candidate.
HTTP/database-specific suites and browser acceptance remain separate checks.

## Current limitations

- Views class loading now passes; timestamp gap is fixed; page rendering now fails on Fiber suspension inside a Generator.
- Optional sitemap/consent, asset aggregation and automatic cron were disabled on
  the earlier site. Do not present that as untouched Drupal CMS compatibility.
- PHP Rust uses its development HTTP server; Coder/public routing is a separate check.
- Development images are not performance releases.
- Installer profile resolves packages dynamically; collect a lock before comparison.
- No remote Git push or upstream submission has been performed by this agent.

## Update discipline

Change this plan and `PROGRESS.md` when the state changes. Put commands, exit
codes and redacted evidence in `docs/receipts/` (create as needed); raw outputs stay
in ignored `results/`. Archive this plan once its milestones are complete or the
scope changes, with an explicit outcome and link to its replacement.
