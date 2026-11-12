# Drupal PHP Rust

Get **Drupal CMS running on PHP Rust**, make setup repeatable with **DDEV and the
[One Line Installer](https://www.drupal.org/project/one_line_installer)**, and compare
it with normal Drupal CMS on standard PHP (Zend).

This is an experimental runtime compatibility project. It does not provide a
Drupal migration agent, migration dashboard, OAuth integration or MCP tooling.

**Current status:** the earlier disposable site renders the homepage and preserves
login sessions. Page rendering is blocked by Fiber suspension inside a Generator. The
new installer hook and DDEV service are prepared but not yet exercised end-to-end.
There is no proven speed advantage and no claim that untouched Drupal CMS works.

## Start here

- [HTML progress tracker](tracker.html) · [live preview instructions](docs/TRACKER.md)
- [Short progress summary](PROGRESS.md)
- [Overall and current plan](.agents/plan/current/README.md)
- [New-agent handover](docs/HANDOVER.md)
- [Install and use](docs/USAGE.md)
- [Architecture and ownership](docs/ARCHITECTURE.md)
- [DDEV / Coder contract](docs/CODER.md)
- [Comparison study: performance, cost, security and agent workflows](docs/COMPARISON.md)
- [Testing and acceptance](docs/TESTING.md)
- [Discrete upstream submissions](docs/UPSTREAM.md)
- [GitHub handoff](docs/GITHUB.md)

## Install

This setup is experimental and has not yet passed a complete fresh-install smoke
test. You need working DDEV/Docker, Git, Bash and Python 3; see
[prerequisites](docs/USAGE.md).

From the parent directory where you want the project created:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust
```

The One Line Installer offers **Local (default)** first and **Coder** second.
Press Enter for local installation, then answer the project-name and DDEV prompts.
It fetches this repo's default branch and uses its `.drupalaibp/` configuration.
No pre-clone, Python launcher, branch or project-name argument is required.

Inside a Coder DDEV freeform workspace, select Coder at the prompt or skip that
choice with the existing installer argument:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust --config coder
```

Both choices install the same Drupal CMS and PHP Rust service. Coder is only the
host; no new template or API key is required in this repository. Follow
[usage](docs/USAGE.md) and [Coder access instructions](docs/CODER.md).

The default command reads `main`; the installer configuration and tracker changes
are now published there. A branch-specific `--ref` is only for pre-release testing,
not the normal setup command.

## Two clearly named runtime URLs

For a project whose base hostname is `example.ddev.site`, runtime setup creates:

- **Standard PHP (Zend):** `https://php.example.ddev.site/`
- **PHP RUST:** `https://rust.example.ddev.site/`

The installer prints your actual URLs and saves them in `.ddev/.runtime-urls`.
The original DDEV URL still uses standard PHP. The two services share Drupal files
and the database; a working standard-PHP page does not prove PHP Rust works.
Coder browser access still needs its workspace routes or a private tunnel.

For an existing installed checkout, bring in the committed changes, then run
`bash scripts/enable-runtime.sh dev-release` to regenerate routing and restart
DDEV. This requires the previously built runtime image; it does not reinstall Drupal.

## Open the tracker

GitHub displays `tracker.html` as source rather than running the page. In your
local source checkout, open that file in a browser for a standalone snapshot, or
run this from the repository root for live Markdown updates:

```bash
python3 scripts/tracker.py --serve
```

Open `http://127.0.0.1:8765` in your browser or site window. The live tracker reads
the plan documents every five seconds. Agents update those documents and regenerate
the standalone snapshot; see [tracker instructions](docs/TRACKER.md).

A [Coder tracker button](docs/CODER-TRACKER.md) is planned separately and is not
created automatically by installation yet.

## Portable runtime work

The repository contains ordered runtime patches, focused PHP/PHPT fixtures,
and the pinned upstream revision. It does **not** need the old workspace or a
sibling PHP Rust checkout:

```bash
python3 scripts/check-repo.py
python3 scripts/prepare-runtime.py
bash scripts/build-runtime.sh dev-release
```

The first command is an offline repository check. The next commands clone source
and build an experimental image; they do not prove Drupal compatibility.
The runtime source is reconstructed under ignored `.work/php-rust`.
See [upstream workflow](docs/UPSTREAM.md) before changing it.

## Scope and exclusions

The earlier comparison used Drupal CMS 2.2.8, core 11.4.12, PHP 8.4.27 and MariaDB
11.8. A fresh installer run may resolve newer packages: save the Composer lock and
use **the same lock/database/files/settings** for both comparison arms.

To get through core runtime work, the disposable test site currently disables
`simple_sitemap` (XMLWriter), `klaro` (DOM XPath), CSS/JS aggregation (stream-wrapper
writes), and automatic request-triggered cron. The new installer does not silently
make those changes. Apply them explicitly only for the scoped experiment and
report them with every result. Full Starter compatibility remains a future gate.

## What is kept where

| Path | Purpose |
| --- | --- |
| `.drupalaibp/` | Installer config and late host hook |
| `.ddev/` | DDEV PHP/database overlay and `ddev phpr` command; installer owns base config |
| `runtime/` | Pinned Docker build, ordered patches, hashes, service template and license |
| `tests/` | Differential fixtures, HTTP checks and test scripts |
| `scripts/` | Build, reconstruct, configure and benchmark tools |
| `docs/` | Usage, handover, testing and contribution guidance |
| `.agents/plan/current/` | Active overall plan and blockers |
| `.agents/plan/archive/` | Superseded plans and extraction receipts |
| `.agents/plan/future/` | Deferred work, including broader installer tests |

Raw logs, generated Drupal code, passwords, browser sessions and database dumps
are ignored. Historical notes are labelled and separated under `docs/history/`.
