# Resume from Git

Read AGENTS.md and `.agents/plan/current/README.md` first. This document is the
handover for the standalone repository, not a request to resume migration work.

## Portable project entry

The active source checkout was recreated outside the migration folder at
`/Users/jamesabrahams/code/ai/drupalphprust` on `codex/portable-installer`.
This path is a local convenience, not a runtime requirement. Use the One Line Installer directly with just the GitHub repository URL from a parent directory. Local is the first
configuration choice; Coder is selectable interactively or with `--config coder`.
Publish to main for that default command; `--ref` is only for branch testing. The Python launcher
is an optional developer helper, not the supported user-facing setup command.
Source is shared through Git; installed databases, credentials and build outputs
stay local to each host. See USAGE.md and CODER.md before provisioning.

## Fresh installation and contributor entry

The installed tracker opens before Drupal exploration and prominently renders
[Try it](TRY-IT.md): known failures, ownership and recovery. The fresh CMS homepage
currently fails on PHP Rust with missing XMLWriter, owned by active task 16.
The Fiber/Generator result below comes from the earlier reduced-feature site.
Full parity is not required to share this experimental environment.

The new bundled DDEV add-on requires DDEV 1.25.5+, gives the tracker its own
hostname, and uses normal DDEV deletion hooks. See the
[native integration receipt](receipts/2026-11-12-native-ddev-addon.md); fresh full
installation and browser checks remain pending. Older sites are not upgraded automatically.

Automatic local hostname registration now runs before OLI announces completion using
actual runtime URLs and DDEV; passwords remain in the user terminal. Coder keeps
private routes/tunnels. No Rust language fix was made in this installer change.

## Current runtime status

The Views `Page.php` lowering blocker is fixed by `static-property-reference.patch`.
The original image fails the fixture with `unsupported construct (assignment target)`.
The new development candidate matches Zend for binding, returned aliases, rebinding,
inherited/redeclared storage, visibility, deferred include and finally execution.
The complete 29-probe `tests/test-oob-runtime.sh` run passes. The actual Drupal Views
class-load probe returned `bool(true)` before the final finally-only amendment.
Browser login succeeds. The next missing `SplFileInfo::getCTime()` method is
fixed by a separate patch and passes the combined suite. `/node/add/page` now
fails with `cannot suspend a Fiber from within a Generator` in Renderer.php.
The minimal `tests/probes/fiber-generator-suspend.php` passes Zend but causes
a runtime panic (exit 255) on the current candidate. Do not claim E2E.
See `docs/receipts/2026-11-12-fiber-generator-blocker.md` for the next work.

## Resume sequence

```bash
python3 scripts/check-repo.py
python3 scripts/prepare-runtime.py
```

Read the reconstructed runtime's AGENTS/process files before editing. On an installed
disposable project with the new sidecar enabled, run:

```bash
ddev exec -s phpr env PHPR_LOG=warn phpr /dev/stdin < tests/probes/views-page-class.php
```

The original Coder site still uses its separate Docker comparison container, not
this repository's prepared DDEV sidecar. Candidate image: `drupalphprust:dev-release`.
Run `tests/test-oob-runtime.sh PROJECT_PATH IMAGE PROJECT_NAME` for the differential
suite. `PHPR_LOG=warn` enables runtime diagnostics; `RUST_LOG` does not.
Continue browser dashboard and create/edit/view, minimising each new failure before
patching runtime code. Do not patch Drupal core or count error-containing HTTP 200s
as success. Full installer and performance validation remain separate milestones.

## Evidence snapshot at extraction (2026-11-11)

| Area | What was observed | Limit |
| --- | --- | --- |
| DB | MariaDB PDO fixtures passed; Drupal boots | Not full PDO parity; see historical database notes |
| SPL | Recursive callback/filesystem fixtures passed | Focused fixtures only |
| Homepage | Fresh Chromium snapshot showed Mercury hero without warnings | Scoped site, not untouched CMS |
| Login | Real login persists after session-ID cookie fix | Dashboard then exposed a separate Twig failure |
| Session | HTTP rotation regression passed on Zend and Rust | Start + double rotation, existing cookie, flags, unrelated cookies, cookies disabled |
| Twig | Regex byte-offset and minimal Unicode tokenization probes pass | Full dashboard rerun pending |
| Moderation | Chained-getter probe + actual Drupal state probe now return draft | Additional access-control cases added but combined rerun pending |
| Compiler | Original static reference-call fixture passes | New Views assignment lowerer gap remains |
| Browser create/edit/view | Not completed on PHP Rust | Current acceptance blocker |
| Regression suite | Earlier run passed through property-stack | Expanded runner with later probes not yet run as a complete final suite |
| Installer / new sidecar | Prepared here from inspected contracts | No full install or Coder freeform run yet |
| Performance | Tools prepared | No valid speed comparison yet |

The test oracle was DDEV PHP 8.4.27 (local PHPT generation used PHP 8.4.8).
Upstream runtime identifies as PHP 8.5.7; version-sensitive behavior needs explicit
oracle selection in upstream tests. Builtin reflection descriptors cover six named
functions only and were exported using local PHP 8.4.8.

## Runtime patch families already present

Database; recursive iterators/filesystem; trait autoload case/diamonds; private-field
unserialization; builtin object dispatch; callback arguments/autoload; cross-unit
reference calls; Fiber dispatch/trampolines; DOM construction; Stringable conversion;
enum serialization; static reference calls; nested ArrayAccess/WeakMap writes;
Twig builtin reflection; forwarded reference returns; directory touch; empty class
name autoload; idle preconnect handling; object property snapshots; stream constants;
session cookies; regex offsets; chained magic property argument reads.

Read UPSTREAM.md and runtime/series rather than inferring dependencies from names.
All exports can be reconstructed without the old integration Git branch.

## Known exclusions / hazards

- `simple_sitemap`: XMLWriter absent; `klaro`: DOM XPath gaps.
- Asset aggregation: registered stream-wrapper file writes incomplete.
- Request cron disabled; previous Canvas health/schema callback path not verified.
- Server idle-preconnect test passes, but partial HTTP requests can still block its
  sequential server. It is not a hardened production serving stack.
- Shared database caches can retain malformed rendered output from earlier bugs.
  Use `ddev drush cache:rebuild` on the disposable site; inspect cache contents before
  stronger cleanup. Never truncate an unconfirmed site's tables.
- A diagnostic instruction limit once interrupted HTML parsing. It did **not** prove
  an infinite loop; warning accumulation had grown the response/session considerably.
- Diagnostic/watchdog builds were not copied into the production candidate recipe.
- Reference input arguments through some named/spread/dynamic static-property paths
  remain broader unverified gaps. Do not broaden claims from the specific fixes.

## Old state and what is portable

The earlier local PHP Rust integration branch was `codex/drupal-oob` at `bad359b`
plus uncommitted fixes. Earlier isolated branches are not required by this repo.
The manifest's pinned upstream commit plus all ordered patches reconstructs the
candidate; new `.phpt` fixtures are under tests/phpt. Source rebuilds do not require
old Coder access, a prebuilt image, private account data or this conversation.

Raw logs/snapshots were intentionally not copied into a public repository. Historical
findings remain under docs/history with stale-state warnings. New verified receipts
should contain only safe command summaries, versions, exit codes and selected errors.
