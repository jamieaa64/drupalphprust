# Serena dashboard and DDEV Freeform — 12 November 2026

## Implementation

The bundled Serena service exposes dashboard port 24282 through its own DDEV
hostname and a dynamically allocated loopback port. MCP 9121 stays internal.
The tracker and `ddev serena-dashboard` link to the dashboard. OLI's existing
hostname hook now records the Serena alias for normal DDEV/AIBP deletion;
pre-existing resolving aliases are preserved. Route changes invalidate stale
network readiness. No real site or database was deleted for these checks.

## Local evidence

Runtime: DDEV 1.25.5, project phprusttest1, pinned Serena 2.1.0 / Rust 1.98.1
image from `runtime/serena/Dockerfile`.

- `python3 tests/test-runtime-hostnames.py`: 7 passed, including Serena ownership
  and cleanup plus preservation of a pre-existing alias.
- `python3 tests/test-project-cleanup.py`: 4 passed.
- `python3 tests/test-runtime-config.py`: 3 passed.
- `python3 tests/test-installer-launcher.py`: 8 passed.
- `python3 tests/test-serena-setup.py`: 9 passed.
- `python3 tests/test-tracker.py`: 7 passed.
- `python3 scripts/check-repo.py`: 31 patch hashes/order and static checks passed.
- `python3 tests/test-serena-http.py` and `python3 tests/test-serena-mcp.py`:
  Rust symbol lookup verified through the container transports.
- HTTPS dashboard returned 200 using curl `--resolve` with normal certificate
  verification; `/get_config_overview` identified the active php-rust project.
  Browser inspection of the loopback dashboard showed Rust enabled and tool use.
- Native Codex MCP initially returned the Zval symbol. Recreating the service
  closed that connection; reconnect MCP in the client after restarting DDEV.

The Mac still requires the user-run hostname registration command in
`docs/SERENA.md`; no administrator password was requested or read by the agent.
A complete fresh OLI install and real deletion remain untested.

## Coder evidence and limits

Read-only `coder templates pull freeform ... --version active --yes` succeeded
against staging-coder.ddev.com, version thankful_west78
(`416847ac-a65f-4303-83e0-e5a3824347a2`). The prepared add-on preserves that
template's existing modules. Using hashicorp/terraform:1.13.5 with its locked
providers, `terraform validate -no-color` passed and
`terraform test -filter=tests/serena.tftest.hcl -no-color` passed both mocked
plans: opt-in disabled and owner-only access when website sharing is public.

No template was pushed, activated or applied to a workspace. Real proxy/Host
header behaviour, owner access and workspace restart acceptance remain pending.
The dashboard monitors the shared HTTP process only; independent host STDIO
connections do not contribute its activity. Runtime compatibility is unchanged:
XMLWriter remains the fresh-installation blocker.
