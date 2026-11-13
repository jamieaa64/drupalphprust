# Resume from Git

Read AGENTS.md and `.agents/plan/current/README.md` first. This document is the
handover for the standalone repository, not a request to resume migration work.

## Portable project entry

The earlier extraction checkout was recreated outside the migration folder at
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
now renders on PHP Rust after the XMLWriter memory fix, owned by task 16.
See [implementation evidence](receipts/2026-11-12-xmlwriter-memory.md): 32
combined probes pass and the browser shows the expected hero. That was the earlier XMLWriter checkpoint; the newer browser evidence is below.
The historical Fiber/Generator receipt describes the earlier reduced-feature site;
the current runtime section below records the newer CMS journey.
Full parity is not required to share this experimental environment.

The new bundled DDEV add-on requires DDEV 1.25.5+, gives the tracker its own
hostname, and uses normal DDEV deletion hooks. See the
[native integration receipt](receipts/2026-11-12-native-ddev-addon.md); fresh full
installation and browser checks remain pending. Older sites are not upgraded automatically. An early project check now prints
manual upgrade instructions and writes a native DDEV version constraint before
the first service start; see the [prerequisite receipt](receipts/2026-11-12-ddev-prerequisite.md).

Automatic local hostname registration now runs before OLI announces completion using
actual runtime URLs and DDEV; passwords remain in the user terminal. Coder keeps
private routes/tunnels. No Rust language fix was made in this installer change.

## Current runtime status — 13 November 2026

The selected disposable installation is `phprusttest1`, at
`/Users/jamesabrahams/code/ai/phprust/phprusttest1`. Its local DDEV runtime image is
`debian:bookworm-slim-phprusttest1-phpr-built`. These names describe this receipt,
not portable installer requirements.

PHP Rust has now completed the actual browser journey: login, dashboard, create a
Utility page, edit its body, publish it and view the updated content anonymously.
The synthetic page is node 2, `/php-rust-verification-13-november`. It survives
restarting `phpr`. A cache rebuild exposed translated-label conversion warnings;
the focused follow-up and final test status are recorded in the
[current receipt](receipts/2026-11-13-authenticated-runtime.md).

The runtime fixes remain a pinned upstream commit plus ordered, hash-checked
patches in `runtime/series`, `runtime/manifest.json` and `runtime/Dockerfile`.
No sibling checkout, old branch or private image is required to reconstruct them.
Development image activation is not a clean installer build or a release pin.

## What to do next

1. Use the receipt's final regression/release status; resolve remaining checks
   without treating known census/file-size failures as a full pass.
2. Verify fresh One Line Installer installation from the pushed repository,
   including a clean runtime image build, hostnames, Serena client selection and
   normal deletion cleanup. Existing-site browser success does not cover this.
3. Validate the existing DDEV Freeform template and private Coder routes. The user
   handles template/workspace updates and authentication.
4. Exercise broader CMS paths with independent oracle fixtures: rich-text editor,
   media, sitemap generation, cold-cache plugin discovery and request handling.
   Retain known regex, date, reference and Fiber-helper limits in the receipt.
5. Prepare small upstream contribution packets with dependencies, reproducer,
   tests and adversarial review; no upstream PRs without user approval. Measure
   performance only after the isolated comparison design is ready.

## Resume commands

Connect to the project's container-backed `drupalphprust-serena` MCP and call
`initial_instructions` before Rust work. Read the reconstructed upstream process.

```bash
python3 scripts/check-repo.py
python3 scripts/prepare-runtime.py
bash tests/test-oob-runtime.sh "$PWD" debian:bookworm-slim-phprusttest1-phpr-built phprusttest1
bash scripts/test-runtime-upstream.sh
```

The last command uses upstream's release Cargo suite and the bundled PHP 8.5.7
oracle inside Serena. Keep its memory-intensive build separate from other builds.
Use standard DDEV PHP for Composer and Drush. Do not change Drupal to conceal
runtime failures, and reject HTTP 200 responses containing warnings/placeholders.

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

- `simple_sitemap`: memory XMLWriter implemented; sitemap generation itself remains unverified.
- `klaro`: DOM XPath gaps.
- Asset aggregation: registered stream-wrapper writes are patched; retain cold-cache checks.
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
