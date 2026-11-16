# Coder integration handoff — issue 1

Published: [GitHub issue #1](https://github.com/jamieaa64/drupalphprust/issues/1).
The issue is the coordination record; this is its initial handoff snapshot from
16 November 2026. Keep implementation status in tasks 09–10 and link PRs in the issue.

## Outcome

A new contributor can use the existing **DDEV Freeform** Coder template, install this repository through the **One Line Installer (OLI), Coder configuration**, open the project tracker and both PHP runtimes, and connect Claude or Codex to the bundled Serena service. Workspace restart must preserve usable routes and agent connections without repeatedly opening Serena tabs.

This issue is the Coder integration handoff for Claude and owns existing plan tasks **09 (installation)** and **10 (Coder integration)**. It is not a new migration platform or a new Coder template.

## Agent ownership and coordination

- **Claude in Coder:** lead this issue, tasks 09–10, Coder setup/docs, OLI integration, buttons/routing, Serena installation and workspace acceptance. Suggested branch: `codex/coder-freeform-integration`.
- **Codex locally:** retain task 17 (runtime release failures), review the Coder PR when requested, and supply focused runtime fixes separately. Do not independently edit the Coder implementation while Claude owns it.
- Before editing, comment with the claimed scope, branch, base SHA and selected disposable workspace/template version. Report blockers and proposed scope changes here so ownership is visible outside either agent's chat.
- Use separate checkouts, DDEV projects/databases and writable `.work/php-rust` trees. Coordinate shared files such as AGENTS.md, current-plan README, tracker.html and runtime manifests before changing them.
- One reviewable PR per coherent change, targeting this repository's `main`; link this issue and task numbers. Document dependent PRs explicitly. The user handles authentication, Git pushes, Coder template publication/workspace updates and merge decisions. Do not create PHP Rust upstream submissions under this issue.

## Read first and keep current

1. [AGENTS.md](https://github.com/jamieaa64/drupalphprust/blob/main/AGENTS.md).
2. [Current plan](https://github.com/jamieaa64/drupalphprust/blob/main/.agents/plan/current/README.md) and [handover](https://github.com/jamieaa64/drupalphprust/blob/main/docs/HANDOVER.md).
3. [Task 09](https://github.com/jamieaa64/drupalphprust/blob/main/.agents/plan/current/09-installer.md), [task 10](https://github.com/jamieaa64/drupalphprust/blob/main/.agents/plan/current/10-coder-tracker.md), [Coder setup](https://github.com/jamieaa64/drupalphprust/blob/main/docs/CODER.md), [Serena setup](https://github.com/jamieaa64/drupalphprust/blob/main/docs/SERENA.md).
4. [Tracker integration](https://github.com/jamieaa64/drupalphprust/blob/main/docs/CODER-TRACKER.md), [Serena button](https://github.com/jamieaa64/drupalphprust/blob/main/docs/CODER-SERENA.md), [testing](https://github.com/jamieaa64/drupalphprust/blob/main/docs/TESTING.md).

Use the existing planning folders: `.agents/plan/current`, `archive`, `future`. Keep tests inside their owning task; distinguish implemented, tested locally, tested in Coder and published. Update tasks 09–10 after meaningful verified progress, keep PROGRESS.md short, and write safe evidence receipts under docs/receipts/ with exact commands, source/image/template identities, outcomes and limitations.

Regenerate `tracker.html` with `python3 scripts/tracker.py`; run `python3 scripts/tracker.py --check`. Use `python3 scripts/tracker.py --install` to refresh the installed tracker after changes. The tracker must show current ownership/status and link this issue and resulting PRs.

## Required environment and installation documentation

Target the existing **freeform** template from [ddev/coder-ddev/freeform](https://github.com/ddev/coder-ddev/tree/main/freeform), on the known staging deployment `https://staging-coder.ddev.com`. Do not substitute `canvas-migration` or create a separate Drupal PHP Rust template.

Read-only access to active template version `thankful_west78` was verified on 12 November 2026; this is historical evidence. Recheck the currently active version and preserve deployment-specific modules before preparing any change.

Document prerequisites, workspace selection, OLI choices and recovery. The Coder VM must provide Docker/DDEV (this project's minimum is DDEV 1.25.5), Git, Python and sufficient Rust build resources. From a parent directory inside the selected workspace:

```bash
SERENA_DASHBOARD_PORT=24283 bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust --config coder
```

This uses [.drupalaibp/coder.json](https://github.com/jamieaa64/drupalphprust/blob/main/.drupalaibp/coder.json). Pin 24283 only when it matches the configured Serena app backend and is free. Explain the agent choices (Codex, Claude, both, manual) and execution location: `host` means the Coder VM; `ddev` means inside the web container. Coder config defaults to inside DDEV. Sign-in remains with the user. For testing an unmerged branch, document OLI's supported `--ref` flow; do not overwrite an existing installation.

## Expected buttons and behaviour

| Control | Expected result |
| --- | --- |
| **PHP Rust tracker** | Opens current plan, task details, receipts and runtime links. OLI completion should lead here. Works through Coder routing; nested docs and refresh remain usable. Shows unavailable/not-ready state rather than misleading success. |
| **Drupal — PHP Rust** | Opens the Rust-served Drupal installation through a working workspace route. Verify identity and content; an HTTP 200 containing a fatal/warnings is failure. |
| **Drupal — standard PHP** | Clearly labelled comparison/control, using the standard DDEV PHP service. This does not substitute for Rust compatibility. |
| **Serena dashboard** | Optional owner-only Coder app, opening only when clicked. Assets and API requests work through the proxy, including trusted-host handling. Never exposes MCP port 9121. |

Provide the two runtime links prominently in the tracker; document whether Freeform exposes them directly as workspace apps or through the tracker. Persistent tracker/Serena workspace buttons belong to the template (`coder_app`), not an OLI API-token workaround.

Reconcile the older illustrative loopback tracker server on port 8765 with the newer installed DDEV tracker service/hostname. Choose and document one supported Coder route and startup lifecycle; avoid duplicate servers. Account for port conflicts, missing checkout and stop/start recovery.

Serena dashboard uses root-relative requests: the prepared app uses a subdomain and exact trusted hostname. Keep it owner-only even when website sharing is public. Do not use wildcard trusted hosts or expose the MCP endpoint.

## Serena installation and no-focus-stealing requirement

The bundled DDEV Serena service supplies Serena, rust-analyzer and the pinned Rust toolchain; do not require host installations.

- VM-side agents use `scripts/serena-mcp.sh` (STDIO); it already passes `--enable-web-dashboard false --open-web-dashboard false`.
- Agents inside DDEV use `http://serena:9121/mcp`, internal only.
- The shared HTTP service enables its separate dashboard but passes `--open-web-dashboard false`.
- Verify actual MCP `initial_instructions` and a Rust symbol lookup, not merely HTTP reachability or a dashboard page. Runtime source navigation starts at `php-rust/` relative to Serena's project root.
- Verify reconnects and workspace/service restarts do not spawn dashboard tabs or steal focus. Manual dashboard opening remains available; intentional tracker launch is separate.
- A separate Mac-native Serena instance caused repeated local tabs. Its saved and live auto-open preference were disabled and an explicit startup override added. Native Serena selects the first free port from 24282, so 24282/24283 tabs alone do not identify their owning service. Those Mac changes are not automatically applied in Coder.
- The shared dashboard does not aggregate independent STDIO session calls. Document this so an empty tool log is not mistaken for a broken MCP connection.

## Already implemented versus still unverified

**Existing implementation/evidence:**
- Shared Local/Coder OLI integration, bundled DDEV services, tracker hostname, runtime links and project-owned hostname cleanup.
- Serena client/location selection and project-scoped Codex/Claude configuration; local HTTP and STDIO Rust symbol checks passed.
- [Prepared Serena Terraform app and tests](https://github.com/jamieaa64/drupalphprust/tree/main/coder/freeform): Terraform validation and two mocked plans passed against the downloaded template.
- Local Drupal homepage, login, dashboard, Utility-page create/edit/publish and anonymous view verified. Persistence after runtime restart and clean rendering after cache rebuild recorded.
- 49 comparison probes and 47 PHPT cases in each compiler mode pass. Full release suite remains **1,756 passed / 3 failed / 2 ignored**, owned by task 17. Do not broaden the compatibility claim.

**Not established:** fresh complete Coder installation, live proxied buttons, published template changes, updated workspace acceptance, actual authenticated agent clients in Coder, or full runtime parity/performance.

Additional collaboration guidance, PR template, Serena CI checks and tracker corrections are in local commit `b6fb080`, branch `codex/serena-dashboard-and-pr-workflow`. GitHub branch lookup on 16 November found that branch unpublished. Do not depend on those files until the user publishes/merges them; this issue contains the essential handoff.

## Acceptance checklist

- [ ] Confirm selected disposable workspace uses current DDEV Freeform; record version and source base SHA.
- [ ] Document and execute a fresh OLI `--config coder` installation, including prerequisites, client/location selection and failure recovery.
- [ ] Verify tracker and the two labelled runtime routes through Coder, with correct service identities and no error content hidden behind HTTP 200.
- [ ] Prepare/review minimal template changes; user publishes/updates the selected workspace. Verify tracker and optional Serena buttons in that actual workspace.
- [ ] Verify owner-only tracker/Serena access, exact dashboard trusted hostname, working assets/API and internal-only MCP.
- [ ] Verify Claude in the selected execution location can call Serena instructions and navigate a Rust symbol; repeat after reconnect/restart.
- [ ] Verify no repeated dashboard auto-opening/focus stealing; tracker opens only at the intended launch or on request.
- [ ] Verify workspace stop/start, port conflict, missing checkout and selected-site normal deletion/cleanup without disturbing another project.
- [ ] Run relevant installer, tracker and Serena setup checks plus repository/patch consistency and secret checks. Preserve local-mode behaviour.
- [ ] Update tasks 09–10, docs, short progress summary and tracker; link a safe receipt and reviewable PR here with remaining limitations.

Deliver a working Coder path and reproducible documentation. If blocked on publication/authentication, prepare the complete reviewable change and exact user-run commands, and record precisely what remains unverified. Do not reset an unconfirmed database, replace global agent configuration, copy credentials, or fold runtime work from task 17 into this issue.

