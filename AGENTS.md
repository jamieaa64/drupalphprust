# Agent entry point

This repository gets Drupal CMS running on PHP Rust and compares it with Zend PHP.
It is a **DDEV / One Line Installer project**, usable locally or in Coder freeform.
It is not a migration dashboard, migration agent, OAuth or MCP project.

Read in order:

1. `.agents/plan/current/README.md` — active plan and current status.
2. `docs/HANDOVER.md` — exact blocker, reproduction, evidence and resume commands.
3. `docs/ARCHITECTURE.md` and `docs/USAGE.md` — ownership and setup.
4. `docs/TESTING.md` and `docs/UPSTREAM.md` — acceptance and contribution boundaries.

Working rules:

- Keep `.agents/plan/current`, `archive` and `future`. Archive a completed/superseded
  plan with its result; do not turn historical investigation notes into current facts.
- Keep `PROGRESS.md` short. Update the relevant numbered task in `.agents/plan/current/`
  after meaningful verified progress; each task owns its status, work and tests.
  Track implementation and contribution work, with tests inside each task. Distinguish
  patched locally from upstream submission; keep every runtime patch assigned to a task.
  Regenerate `tracker.html` with `python3 scripts/tracker.py` after Markdown changes
  under PROGRESS.md, docs/ or .agents/plan/. The live preview reads those files directly.
- Update the current plan after meaningful verified progress.
  Record exact command, runtime/image, test result and remaining limits in a receipt.
- A HTTP 200 containing a fatal error, warnings or class-name placeholders is failure.
- Use standard PHP in DDEV for Composer and Drush. Keep runtime compatibility,
  installer correctness, Coder routing and speed measurements as separate checks.
- No full installer test matrix is required during the repository extraction.
  Do not label the prepared installer/DDEV integration as tested until executed.
- Fix runtime bugs in a reconstructed `.work/php-rust` checkout. Read that upstream
  repository's AGENTS.md and mandatory process files before editing Rust. It requires
  one adversarial reviewer; use that process for runtime changes, not parallel churn.
- Export discrete patches, keep `runtime/series`, Dockerfile and manifest aligned,
  and attach independent fixtures. Do not depend on a sibling repository or this chat.
- Existing local runtime branches are historical conveniences; the pinned upstream
  commit plus committed patch series is the portable source of this candidate.
- The user handles authentication and `git push`. Never request/read passwords.
  Prepare local commits and give exact user-run push commands. No upstream PRs yet.
- Use plain `git add && git commit`. Upstream Rust checkout rules additionally use
  `git add -u` for tracked Rust files. Include why, validation and AI-assisted approach
  in detailed bullet-point commit messages.
- Operate only explicitly selected disposable sites. Do not reset a database or
  remove a container by guessed name. Keep credentials, cookies, DB dumps, site
  files and raw browser/session logs out of Git.

This repository supersedes migration-oriented notes from the original parent folder.
Its local parent location is historical only and is not a runtime dependency.
