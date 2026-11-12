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
  implemented local patches from unfinished implementation and upstream submission; keep every runtime patch assigned to a task.
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

## Coder target

Use the existing **DDEV Freeform** template (`freeform`), whose upstream source is
`https://github.com/ddev/coder-ddev/tree/main/freeform`. The known deployment is
`https://staging-coder.ddev.com`. Do not substitute the `canvas-migration` template,
even if an older PHP Rust workspace uses it. Read `docs/CODER.md` and
`docs/CODER-SERENA.md` for dashboard integration and verified access/version notes.
Recheck the active template before preparing changes. The user handles template
push, authentication and workspace updates; never read or copy credentials.

## Serena for runtime work

The bundled DDEV `serena` service supplies Serena, rust-analyzer and the pinned
Rust toolchain. Host installations of those tools are not required. The One Line
Installer asks which client to configure (Codex, Claude Code, both or manual) and
where it runs (host/Coder VM or inside DDEV). See `docs/SERENA.md`.

- Before editing Rust, connect to the project's `drupalphprust-serena` MCP server
  and call `initial_instructions`. Use its symbol navigation/editing tools and
  continue following the reconstructed upstream AGENTS/process files.
- Host agents use `bash /absolute/project/path/scripts/serena-mcp.sh` (also
  available as `ddev serena-mcp`). Inside-DDEV agents use the internal URL
  `http://serena:9121/mcp`; do not publish this endpoint or add a router hostname.
- `ddev serena-dashboard` opens the separate shared-service dashboard on the
  `serena.<project-host>` route. It is not the Mac's global Serena instance and
  does not show tool calls made through independent STDIO sessions.
- Serena sees `/var/www/html/.work/php-rust` as its project root. Tool-relative
  paths start with `php-rust/`; they are not macOS host paths.
- Run `python3 scripts/setup-serena.py --client codex --location host` to prepare
  the checkout and configure an existing installation. Use `claude` or `both`
  and `--location ddev --install-agents` when appropriate. User handles sign-in.
- Trust/open the project and reconnect MCP in the client after setup. If tools
  are absent, diagnose the connection; do not silently bypass upstream's Serena
  requirement. Never replace global agent configuration or read credentials.
