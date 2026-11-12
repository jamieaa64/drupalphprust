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

- [Simple progress checklist](PROGRESS.md)
- [Overall and current plan](.agents/plan/current/README.md)
- [New-agent handover](docs/HANDOVER.md)
- [Install and use](docs/USAGE.md)
- [Architecture and ownership](docs/ARCHITECTURE.md)
- [DDEV / Coder contract](docs/CODER.md)
- [Testing and performance comparison](docs/TESTING.md)
- [Discrete upstream submissions](docs/UPSTREAM.md)
- [GitHub handoff](docs/GITHUB.md)

## Local or Coder setup

Use the [One Line Installer](https://project.pages.drupalcode.org/one_line_installer/guides/getting-started/)
directly, passing this repository and a **published** branch. Run in your terminal
from the parent directory where the new project should be created:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust \
  --ref codex/portable-installer \
  --config drupalphprust \
  --name drupalphprust-site
```

The installer reads `.drupalaibp/drupalphprust.json` from that branch, creates
`drupalphprust-site`, installs Drupal CMS using DDEV, and runs our late hook to
build and enable PHP Rust. Follow its prompts, including any DDEV administrator
password request. No Python launcher or pre-clone is required.

Use the same command inside a Coder DDEV **freeform** workspace, choosing a new
project name. Coder supplies the host; no new template, API key or Coder dependency
is needed for the local build. See [usage](docs/USAGE.md) and [Coder workflow](docs/CODER.md).

`codex/portable-installer` must be pushed before this command can fetch it. Once
merged into main, omit `--ref` or select a released ref. The runtime remains
experimental: installation is not proof of Drupal end-to-end compatibility.

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
