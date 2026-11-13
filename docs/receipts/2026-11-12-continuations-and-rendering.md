# Generator continuations and Drupal rendering

Historical checkpoints for patches 34–38. Later browser and test evidence is in
[the authenticated runtime receipt](2026-11-13-authenticated-runtime.md).

Scope: local DDEV `phprusttest1`, unchanged Drupal CMS files and configuration.
PHP Rust is the target; Zend PHP is the comparison oracle. No fallback-site claim.

## Implemented contribution units

- Task 01: `fiber-generator-continuation.patch` parks explicit pending foreach,
  Generator-method and yield-from operations with the Fiber frames. Suspension is
  control flow, not an exception. Resuming at another call depth rebases frame
  indices. Unsupported synchronous callback/collector boundaries reject suspension
  before unwinding, preserving PHP finally blocks and a terminated Fiber state.
- Task 08: `dom-namespace-axis.patch` adds namespace-axis values, inheritance,
  shadowing, parent traversal, predicate comparisons and PHP namespace-node
  properties. QName lexing stops at `::`. Namespace declarations are excluded from
  ordinary attribute collections; SimpleXML filters namespace results.
- Task 06: `preg-extended-comments.patch` removes extended-mode comments before
  pattern rewrites. It preserves quoted literals, scoped modes, escaped hash,
  POSIX classes, initial class brackets/carets and control escapes. This fixes
  Drupal's token scanner returning no matches for `[site:name]`.
- Task 06: `multisort-stringable.patch` invokes VM string conversion during
  string/natural comparisons. Drupal's translated token labels now sort without
  object-to-string warnings; an exception prevents array writeback.

## Executed evidence

Rust 1.98.1, `cargo build --locked --profile dev-release -p php-cli -p php-server`,
`CARGO_TARGET_DIR=/tmp/serena-target`, one build job in the Serena container.
Seven focused fixtures match PHP 8.5.7 with clean stderr: fiber-generator-suspend,
fiber-nested-render, fiber-generator-continuation, dom-namespace-axis,
dom-namespace-inheritance, preg-extended-comments and multisort-stringable.
PHPT expectations come from that oracle. The separate helper-boundary PHPT tests
an explicit runtime limitation, not Zend equivalence.

An isolated server request to the formerly hanging missing-font route completed
with HTTP 404. A cold homepage request after `ddev drush cr` returned HTTP 200,
`Home | phprusttest1`, expected hero markup and no PHP diagnostics or redundant
namespace attributes. The browser subsequently exposed a separate asset-generation
failure: `file_put_contents(temporary://...)` bypasses registered stream wrappers.
That remaining failure is being addressed; full browser acceptance is not claimed.
Old session messages from the previous XPath failure were visible on the first
browser request and consumed on reload; they are distinct from the new stream warning.

The 37-patch candidate was activated by copying the tested development binaries
into the existing runtime image and recreating only the `phpr` service. This is a
local activation, not a clean Dockerfile rebuild or installer validation.

- Image: `sha256:9fb603ce77cbad8b73c4935eabff50a88eb598984191f782c8f8cd77b6fd14e7`.
- phpr: `1eaad549934fdea1df09ca5723fb6ce410be144a6efc8e2172f468acc13daafa`.
- php-server: `29fc23d53ab445f703a0c5a94c7b07d5a850b4b45bf3d05182a35997ac9470c0`.

The complete 40-probe `tests/test-oob-runtime.sh` run against this image passed
with byte-identical stdout and clean stderr. Public-service missing-route and
anonymous login requests returned clean HTTP 404 and 200 respectively. The release
suite completed with 1,757 passed, two failed and two ignored. The failures remain
`vm::census::tests::op_index_maps_sampled_variants_in_range` and
`nessun_sorgente_rs_oltre_cap`; source-size excesses include these new fixes.
The suite is not green. Command: `bash scripts/test-runtime-upstream.sh`,
PHP oracle 8.5.7 in Serena, release profile. All 37 patch hashes/order and
reconstruction passed; no full upstream corpus or performance gate was run.

## Adversarial review and limits

One reviewer checked suspension cleanup, nested Fibers, pending-value release,
namespace behavior and regex lexical boundaries. Reported regressions were fixed
and added as fixtures. Pending-send destruction no longer waits until shutdown;
its remaining catch/destructor ordering difference also exists in the old
nonsuspending runtime and is not claimed fixed. Abandoned-Fiber destruction and
suspension through arbitrary native helpers remain incomplete.

The existing merge sort schedules comparisons differently from Zend. Pure
Stringable labels are supported; observable side effects in `__toString()` can
occur in a different order. A three-element throwing probe preserves original
arrays on both engines but logs different conversion order. No general sorting,
XPath, regex, Fiber or CMS parity claim, upstream submission, or speed claim.

## Registered stream writes: final candidate

Task 08 adds `user-wrapper-write.patch` as patch 38. `file_put_contents` now
opens the registered PHP wrapper with the correct mode/context, writes arrays,
scalars or resource data, retries partial writes, and flushes/closes according to
the oracle. Zero, false, negative and oversized callback results have distinct
warnings and cleanup behavior. Conversion/write/flush exceptions skip close as
Zend does. Default stream-context APIs remain absent in the earlier runtime.

The main fixture and an independent adversarial fixture match PHP 8.5.7 exactly
with clean stderr. Both are retained in `tests/probes/user-wrapper-write.php`
and its PHPT. The reviewer reran scalar/array failure cases independently and
reported no outstanding scoped blocker. Full wrapper API parity is not claimed.

Build recipe is unchanged (CLI server, dev-release, Rust 1.98.1, one job).
An accidental Axum-feature build was stopped and discarded; a login-shell PATH
failure was corrected before the successful normal build. No measurement used
either attempt. Final candidate:

- Image: `sha256:6110defbee62cef0a6a411e4e29915ed0f3bb582279b003357ed91d00f964d3d`.
- phpr: `4fba887cbee2ecc999a2f44cc8be984231574fae1d541d4704377e6a60540013`.
- php-server: `8db93db284b5d80e33be75621e0d26d1de1a85df954851c82ccefaa7bbd65d1d`.

`bash tests/test-oob-runtime.sh /Users/jamesabrahams/code/ai/phprust/phprusttest1
debian:bookworm-slim-phprusttest1-phpr-built phprusttest1` passes all 41 probes.
`python3 scripts/check-repo.py` passes all 38 hashes/order and structure checks.
`python3 scripts/prepare-runtime.py --source .work/php-rust --destination
.work/stream-reconstructed` reconstructs the candidate; all 37 changed runtime
source files match byte-for-byte. As above, local activation copied tested
binaries into the existing base; a clean installer/image build is not claimed.

Browser verification on the first stream candidate showed the expected Mercury
homepage without error messages; styles, generated CSS/JavaScript and both fonts
returned HTTP 200. One previously queued stream warning was consumed on the first
reload. The second reload was clean. The final candidate only amends error paths;
final browser recheck on patch 38 also passed. Its release rerun was killed
during PHPT-runner linking under memory pressure; the retry was intentionally
stopped before source changes for the newly reproduced registry gap. The completed
37-patch release result above remains the last full result.
The user subsequently authorized the disposable demo login; submission succeeded,
but the authenticated dashboard exposed generator collection through a synchronous
helper. Follow-on registry and collector work is recorded in the current plan.
