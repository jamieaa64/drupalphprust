# Tracker landing page and installation verification — 12 November 2026

## Installation observed

Root: `/Users/jamesabrahams/code/ai/phprust/phprusttest1`; source HEAD
`cf8c634c95f5f1a6f3026c5d00150fdc1093f9f4`, branch main, with preserved installer
changes. DDEV project: `phprusttest1`, DDEV v1.25.0, Docker Desktop.
The existing terminal reached installation completion and returned to a shell.
The dev-release build completed; image `drupalphprust:dev-release`, image config
`sha256:dccd91734db4e9be1c7ae23f8e5f9881975e8b46f661dba734279b3b7b20b1c4`.
`ddev describe -j` reported web, db and phpr running. Only allowlisted service
status and timestamp are stored by the tracker; full output is not persisted.
No installer rerun, DB reset, module changes or credential collection was performed.

## OLI contract inspected

Downloaded the public generated script from
https://project.pages.drupalcode.org/one_line_installer/drupalaibp on this date,
for inspection only. Source sections: `src/lib/steps/launch_site.sh`,
`print_welcome.sh`, `_welcome_text_substitute.sh` and `src/lib/core/pipeline.sh`.

- `launch_path` is passed to `ddev launch`; a persisted `LAUNCH_PATH` answer can override it.
- `replace_welcome_text` replaces the whole generic ready/login message.
- `{SITE_URL}` is guessed from the project directory in that message, so it is
  unsuitable for custom DDEV hostnames/ports. Hooks run as child shells and
  cannot export a replacement value into the parent's welcome variables.
- Existing `before_launch_site` and `before_agent_handoff` events solve this in
  repository configuration: publish the tracker, then print actual URLs from
  DDEV and `.ddev/.runtime-urls` immediately before the replacement welcome text.
- No upstream installer change or submission is needed.

## Changes and verification

Both local and Coder configs now launch `/project-tracker/`. The new host hooks
invoke `python3 scripts/tracker.py --install` and `--message`. The installed HTML
lives in ignored `web/project-tracker/`; an ownership marker prevents replacing
an existing unrelated directory. Committed `tracker.html` remains portable and
contains no installation metadata. Normal regeneration also refreshes the managed
installed copy. Markdown task navigation remains intact; the loopback server
still refreshes Markdown and supports relative Coder proxy paths.

Executed:

- `python3 scripts/tracker.py --install`: published landing page, stored timestamped
  web/phpr service status separately from explicit unverified compatibility labels.
- `python3 scripts/tracker.py --message`: printed Project tracker, Drupal on
  standard PHP, Drupal on PHP Rust with actual installation URLs.
- Extracted OLI's unchanged launch function into a temporary harness, set
  `LAUNCH_PATH=/project-tracker/`, ran `step_launch_site`: Chrome opened
  `https://phprusttest1.ddev.site/project-tracker/`; page title, runtime cards and
  Markdown tasks verified in the browser. HTTPS fetch returned 200.
- Extracted OLI's unchanged welcome functions and supplied the configured
  replacement array: the hook's three exact URLs appeared followed by the
  compatibility caveat; no generic Drupal-ready or credential message appeared.
- `ddev utility compose-config`: php hostname belongs to web; rust hostname
  belongs only to phpr.
- Temporary unique PHP identity endpoint, removed in `finally`, requested with
  `curl --resolve HOST:443:127.0.0.1`: php host returned `8.4.17|fpm-fcgi`;
  rust host returned `8.5.7|cli-server`. TLS verification stayed enabled.
- Ordinary requests to both named hostnames failed DNS resolution on this Mac.
  User-run `ddev hostname HOST 127.0.0.1` requested for each; authentication stays
  with the user. Distinct service routing passes, ordinary link accessibility
  remains pending DNS repair. Browser follow-up on a blank new tab was blocked
  by URL policy; no bypass attempted.
- `python3 scripts/tracker.py --serve`: port 8765 already occupied; preserved its
  owner and started this project's server with `--port 8766` instead.
- Seven tracker tests pass, including credential-bearing URL rejection and
  metadata allowlisting; `python3 scripts/check-repo.py` passes.

Homepage checks with the same explicit local DNS resolution returned standard PHP
HTTP 200 (10,180 bytes, no detected fatal/warning markers) and PHP Rust HTTP 500
(2,984 bytes): `Class "XMLWriter" not found` in ContainerBuilder.php:416.
The current installation has not had optional-module exclusions applied; do not
assume the prior scoped Fiber/Generator blocker is the first failure here.

Limits: this verifies the launch and welcome stages on the existing installation,
not a second clean install with the amended config. Coder private access and
stop/start persistence remain untested. Running services are not proof of Drupal
compatibility; no end-to-end or performance claim. `phpr --version` is unsupported
by this build; the identity endpoint provides the runtime version instead.

## Follow-up: user completed hostname registration

After the user ran both `ddev hostname` commands, Python `socket.gethostbyname`
returned `127.0.0.1` for both hostnames. Normal `urllib.request.urlopen` requests
(no explicit DNS override; TLS verification enabled) reached both sites:
standard PHP HTTP 200, 10,180 bytes, no detected fatal/warning markers;
PHP Rust HTTP 500, 2,984 bytes, the same missing XMLWriter error. DNS repair
is verified; runtime compatibility remains failing. Automatic installation-time
hostname registration is still outstanding, separate from this manual repair.
