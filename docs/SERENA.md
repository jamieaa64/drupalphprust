# Serena for PHP Rust development

The bundled DDEV add-on builds a `serena` service containing Serena 2.1.0,
Rust 1.98.1, rust-analyzer and rust-src. It is development tooling, separate from
the `phpr` serving image. The same service runs locally and in a Coder DDEV
workspace. No host Serena, Python package manager or Rust toolchain is required;
the setup helper requires host Python 3.11+.

## One Line Installer choices

The normal Local/Coder configuration picker is followed by two project questions:

1. Connect Serena to **codex**, **claude**, **both**, or **manual**.
2. Run the agent on **host** (your computer or Coder VM), or **ddev** (web container).

Local defaults to Codex on the host. Coder defaults to Codex inside DDEV.
Unattended installation uses these defaults. `manual` installs the tooling service
without preparing the runtime checkout or changing client configuration.

After the runtime service starts, the hook reconstructs `.work/php-rust` if absent,
creates its explicit Rust Serena configuration, and generates the selected
project-scoped client connection. Existing runtime checkouts are preserved.
For `ddev`, it installs the selected `e0ipso/ddev-assistant-codex` and/or
`e0ipso/ddev-assistant-claude` add-ons, supplies missing command wrappers and
restarts DDEV. This uses the same agent add-on repositories as OLI. No automatic
approval-bypass flags, agent logins or global host client installations are added.
For `host`, bring your existing Codex or Claude Code application/CLI.

Open and trust the installed project in the selected client, then reconnect MCP
(restart the client if necessary). Client trust and account authentication remain
user actions. Config files alone do not prove the client has connected.

## Existing installations

From the selected project root:

```bash
ddev add-on get ./ddev-addon
ddev restart
python3 scripts/setup-serena.py --client codex --location host
```

For container agents, use instead:

```bash
python3 scripts/setup-serena.py --client both --location ddev --install-agents
ddev codex
# Or: ddev claude
```

The user signs in directly when the agent requests it. `--install-agents` is
unnecessary when using already installed container clients. Rerunning setup
preserves unrelated MCP settings and refuses conflicting unowned entries.
Codex configuration is `.codex/config.toml`; Claude Code uses `.mcp.json`.
Generated machine-specific configuration and ownership receipts are ignored by Git.
One installation selects one client location at a time; rerun setup to switch.

## Connection details for agents and other clients

| Agent location | Transport | Connection |
| --- | --- | --- |
| Host or Coder VM host | STDIO | `bash /absolute/project/path/scripts/serena-mcp.sh` |
| DDEV web container | Streamable HTTP | `http://serena:9121/mcp` |

The host launcher changes to the project directory and executes Serena inside
DDEV; its stdout is reserved for MCP. Use `--context claude-code` as an additional
launcher argument for Claude Code. Codex is the default STDIO context. The HTTP
service uses the generic `ide` context and is shared only for this runtime project.
Do not activate unrelated projects through that shared endpoint. Host STDIO
connections each start their own Serena process.

The MCP HTTP port (9121) is accessible within the DDEV Docker network only.
The separate dashboard has a DDEV hostname and a workspace-loopback port; see below. It grants source-editing tools
to clients that can reach that network. A desktop agent on your laptop is not
the same as an agent running in a remote Coder VM; use the agent in that workspace
or separately configure an authenticated private connection, never a public MCP URL.

Call `initial_instructions` before using the tools. The project root seen by Serena
is `/var/www/html/.work/php-rust`; a typical tool path is
`php-rust/crates/php-types/src/zval.rs`. Host shell paths differ. The Cargo workspace
is `/var/www/html/.work/php-rust/php-rust`. Source edits persist in the host bind
mount. Analysis build outputs and Serena's home are in container `/tmp` and can be
recreated after restart. Rust and Python dependencies are baked into the image.

## Verification and recovery

```bash
ddev exec -s serena rustc --version
ddev exec -s serena rust-analyzer --version
python3 tests/test-serena-mcp.py
python3 tests/test-serena-http.py
```

The smoke tests initialise MCP, list tools and find the real `Zval` Rust symbol;
the HTTP test runs requests from the web container. They do not edit runtime code.
If the checkout is missing, run the setup helper. If an existing Serena project
config lacks Rust support, merge `runtime/serena/project.yml` into it; setup does
not overwrite it. Check `ddev logs -s serena` for HTTP startup errors. DDEV must be
running before a host MCP client connects.

The image pins its base digest, Rust toolchain and Python package versions in
`runtime/serena/requirements.txt`. Debian package repositories are not snapshot
pinned, so this is not a bit-for-bit build claim. Keep toolchain changes separate
from runtime parity/performance experiments. Full fresh OLI/Coder provisioning
and authenticated agent sessions require their own acceptance receipts.

References: [Serena client configuration](https://oraios.github.io/serena/02-usage/030_clients.html),
[Codex MCP](https://learn.chatgpt.com/docs/extend/mcp),
[DDEV custom services](https://docs.ddev.com/en/stable/users/extend/custom-compose-files/).

## Shared dashboard and hostname lifecycle

Open `ddev serena-dashboard`, or use the Serena dashboard card in the tracker.
The stable route is `https://serena.<project-host>/dashboard/index.html` (scheme
and port follow DDEV). The service dashboard displays the shared HTTP server's
configuration, tool usage and logs. It does not aggregate separate host STDIO
processes or the Mac-installed Serena dashboard at port 24282.

OLI's normal pre-completion hostname hook registers the Serena alias through
DDEV along with the site/tracker aliases. Any administrator authentication stays
in the installer's terminal. Normal AIBP/DDEV deletion removes only aliases
recorded as created by this installation; existing aliases are not adopted.
Older installations can register the new alias and refresh the tracker with:

```bash
python3 scripts/register-runtime-hostnames.py
python3 scripts/tracker.py --install
```

DDEV routes only dashboard port 24282, never MCP port 9121. The dashboard can
change Serena configuration and shut down its server, so Coder access is
owner-only. See [DDEV Freeform button](CODER-SERENA.md). Normal local installs
allocate a free loopback dashboard port; `SERENA_DASHBOARD_PORT=24283` pins it
for the Coder app. The saved port survives rebuilds.
