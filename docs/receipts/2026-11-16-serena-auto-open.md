# Serena dashboard auto-open — 16 November 2026

The reported loopback dashboard repeatedly opened and took browser focus.
The project launchers already pass `--open-web-dashboard false`; STDIO also
passes `--enable-web-dashboard false`. The independent host Codex Serena entry
starts `serena start-mcp-server --context=codex --project-from-cwd` without an
auto-open override. Its host Serena configuration had
`web_dashboard_open_on_launch: true`.

Changed only that host preference to `false`, retaining manual dashboard access
and all other configuration. This is a local preference change, not a runtime
patch or installer change. The host preference file is outside this repository.
The tracker launch remains unchanged.

Verification: inspected the launcher flags and read back the saved host setting.
The project Serena MCP `initial_instructions` call succeeded. No listener on the
reported host port was visible to the initial `lsof` check, so that exact tab's
process was not identified. No forced client restart or browser focus test was
performed; the preference applies on the host server's next start/reconnect.

## Follow-up after continued tab openings

A later process check identified native Serena PID 60234 listening on loopback
24282 and a separate DDEV STDIO client with both dashboard flags disabled.
Nothing listened on 24283 at that check. Installed Serena source chooses the
first free port starting at 24282, so simultaneous native instances can use
24283; a tab alone cannot identify which process created it.

The earlier disk edit did not update the running instance. Applied its supported
`POST /save_web_dashboard_open_on_launch` with boolean false; response was
`status: success`. Read back the live `get_config_overview` preference as false.
Also appended `--open-web-dashboard false` to the existing native Serena MCP
arguments in the host Codex configuration, preserving all other configuration.
This explicit override applies on subsequent starts. No processes were killed.

The container launchers already use the override. Coder still needs its actual
workspace acceptance check; this local fix does not certify Coder browser focus
behaviour. No newly launched client was used for a focus test.
