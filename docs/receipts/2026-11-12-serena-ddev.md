# Containerised Serena and installer choices — 2026-11-12

## Delivered

The bundled DDEV add-on supplies a separate `serena` development service with
Serena 2.1.0, Rust/rust-analyzer 1.98.1 and rust-src. Python dependencies are
version-pinned in `runtime/serena/requirements.txt`; base image digest is pinned.
Debian package repositories are not snapshot-pinned.

Local and Coder OLI configs ask for Codex, Claude Code, both or manual, followed
by host versus DDEV execution. The late setup hook prepares a missing runtime
checkout, supplies explicit Rust language/workspace settings and writes selected
project-scoped client configuration. DDEV mode installs the selected official
OLI-used agent add-ons and supplies missing wrappers without auto-approval flags.
Host mode does not install a host agent. No agent authentication is performed.

OLI's public entry script was inspected on this date: custom
`single-select-options` questions are supported, their answers are persisted, and
the built-in agent picker is skipped in config mode. The project therefore uses
explicit custom questions and a late hook, without modifying the installer.

## Executed evidence

Selected project: `phprusttest1`, DDEV 1.25.5, Docker Desktop on arm64 macOS.

- `ddev add-on get ./ddev-addon && ddev restart`: service installed and built.
- `ddev exec -s serena rustc --version`: `rustc 1.98.1 (48a229cea 2026-09-01)`.
- `ddev exec -s serena rust-analyzer --version`: `rust-analyzer 1.98.1 (48a229c 2026-09-01)`.
- Recorded all 74 installed Python package versions, rebuilt using that file,
  then restarted the project successfully.
- Final service image: `sha256:0915e7a185e4c5e7d02329c294ac1adef0d601c7aa7e9b7b56f9334b4b671e08`.
  Docker inspection: host port bindings `{}`, execution user `501:20`.
  Container Cargo cache is writable by that user.
- `python3 scripts/setup-serena.py --client codex --location host`: project Codex
  configuration generated; repeated invocation preserved Serena's expanded YAML.
- `codex mcp get drupalphprust-serena`: enabled STDIO entry recognised with the
  project launcher and 180-second startup/tool timeouts. This is configuration
  recognition, not proof this existing desktop chat has loaded the tools.
- `ddev serena-mcp --context claude-code --help`: installed command resolves and
  accepts the documented Claude context override.
- `python3 tests/test-serena-mcp.py`: PASS after rebuild/restart. Initialisation,
  initial instructions, tool discovery and actual `Zval` Rust symbol lookup over
  host STDIO through `ddev exec`.
- `python3 tests/test-serena-http.py`: PASS after rebuild/restart. The same
  protocol and symbol checks, with HTTP requests originating in the web container
  to `http://serena:9121/mcp`. Session identifiers are not printed or saved.
- `python3 tests/test-serena-setup.py`: 9 PASS. Host/container settings, repeated
  setup, preserving unrelated configuration, refusing conflicts and symlinks,
  manual mode, installer choices, mocked selected-add-on installation and no
  completion marker after a simulated add-on failure.
- `python3 tests/test-runtime-config.py`: 3 PASS.
- `python3 tests/test-installer-launcher.py`: 8 PASS.
- `python3 tests/test-tracker.py`: 7 PASS.
- `python3 scripts/check-repo.py`: PASS, all 31 runtime patches unchanged.
- Diagnostic HTTPS requests after restart: Zend 200, Rust 500 with the same
  missing XMLWriter error. These requests skipped certificate validation and do
  not establish browser or TLS acceptance.

## Corrections discovered during testing

Serena's initial default exposed its REPL interface and detected no Rust language
server in the nested checkout. Explicit `--agent-interface tools` and the bundled
Rust project configuration fixed both failures. Serena expands its project YAML
on activation, so setup now preserves existing YAML instead of comparing its
bytes with the short template. The tracker task-length check caught an overlong
task summary; it was shortened and all seven tracker tests passed on rerun.

## Limits and next work

No Rust source fix, Drupal change, database reset, login or push was performed.
XMLWriter and the earlier Fiber/Generator blocker remain. The new connections
were tested with protocol clients, not authenticated Codex/Claude sessions.
Current Codex chat tools may require a client reconnect to load the project entry.

A fresh OLI run, remote Coder run, and real installation of both agent add-ons
through the new hook have not been executed; installation sequencing has mock
coverage only. One existing local site and both real transports were tested.
The host and DDEV client configurations select one execution location per project;
run setup again to switch. The internal HTTP service is shared for this runtime
project and is not published through a host port or DDEV/Coder router.

See [setup and recovery](../SERENA.md) and [installer task](../../.agents/plan/current/09-installer.md).
