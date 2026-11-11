# Resume from Git

Read AGENTS.md and `.agents/plan/current/README.md` first. This document is the
handover for the standalone repository, not a request to resume migration work.

## Exact current runtime blocker

Drupal's page creation route `/node/add/page` now gets past moderation-state
handling but fails loading `web/core/modules/views/src/Plugin/views/display/Page.php`.
The runtime's diagnostic logging reports:

```text
deferred decl re-lower failed .../Page.php:
Unsupported { what: "assignment target", line: 129 }
```

The relevant code is `static::$pageRenderArray = &$element` inside a reference-
returning static method. This is a **lowering** failure in a deferred class include,
not the earlier `&self::getValue(...)` call-binding issue (already patched).
The lowerer needs a minimal reproduction before changing compiler/VM code.
Direct execution of Page.php can instead report a missing parent class: reproduce
through the real autoloader or a two-file parent/child fixture.

## Resume sequence

```bash
python3 scripts/check-repo.py
python3 scripts/prepare-runtime.py
```

Read the reconstructed `.work/php-rust/AGENTS.md` and referenced runtime process
files. Prepare a minimal fixture using parent/child autoload and reference assignment
into `static::$property`, including alias mutation and a reference return. Compare
with standard PHP, then patch the correct lowering path. Do not work around it by
editing Drupal core. Use the runtime-required single adversarial reviewer.

Build using Docker after exporting the patch and updating series/manifest. In an
installed disposable project with the new sidecar enabled, the diagnostic probe is:

```bash
ddev exec -s phpr env PHPR_LOG=warn phpr /dev/stdin < tests/probes/views-page-class.php
```

For the original environment use its existing Docker image/network and the same
probe rather than assuming a DDEV `phpr` service already exists. `PHPR_LOG=warn`
is the runtime logger switch; `RUST_LOG` does not enable it. Avoid logging secrets.
The probe boots Drupal using the actual autoloader, then checks the Views Page class.

After it passes, retry `/node/add/page` in the browser. Continue each new runtime
failure with a focused reproducer; never treat the next HTTP 200 as success without
checking content. Verify dashboard and create/edit/view, then the combined suite.

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
