# Theme-hook references and remaining browser blockers

## Implemented change

`callable-unpack-reference.patch` preserves reference cells when constructing
and extracting dynamic call arguments. The parameter binder still separates
by-value parameters. An explicit bytecode flag distinguishes calls from array
literals; literals use existing PhpArray copy rules, separating singleton
references while retaining externally shared aliases.

The minimized `reset($listeners)(...$args)` theme-hook pattern lost mutations on
the previous 32-patch runtime. The new fixture covers method closures, array
callables, closures, captured references, value isolation, variadics, dynamic
sort/strlen, array copies, singleton references and namespace fallback.
The required adversarial reviewer caught an initial singleton-reference copy
regression; the corrected distinction passed independent controls and re-review.
Statically resolved by-reference spread calls still have a pre-existing compiler
rejection. Full named/iterator unpacking parity and performance are not claimed.

## Verification

Selected site: `phprusttest1`. Rust 1.98.1, dev-release image
`debian:bookworm-slim-phprusttest1-phpr-built`, ID
`sha256:a170639244aa024b16bfc3afe40aa2103d2621f9b09d49bc7e95bd45d291a120`.
Binary hashes:

- phpr: `8c8cba67cb5b39195b1ee7f64accc61f797c5dc35929309566497cfb649afced`
- php-server: `9372f645334579208906fa8b613685242694994f65103141eb0df09875197797`

Executed:

```bash
docker build --build-arg RUNTIME_PROFILE=dev-release \
  -t debian:bookworm-slim-phprusttest1-phpr-built runtime
docker compose -f .ddev/.ddev-docker-compose-full.yaml \
  up -d --no-deps --force-recreate phpr
bash tests/test-oob-runtime.sh "$PWD" debian:bookworm-slim-phprusttest1-phpr-built phprusttest1
python3 scripts/prepare-runtime.py --source .work/php-rust --destination .work/font-stall/reconstructed
ddev drush cr
```

All 33 combined probes pass against DDEV Zend PHP 8.4.27 with exact stdout and
empty stderr. The new fixture also matches the pinned PHP 8.5.7 oracle. All nine
local stylesheet/script/font/favicon URLs extracted from the corrected homepage
returned 200. All five changed Rust files match fresh reconstruction. Repository
hash/order checks pass. Canonical corpus and performance promotion are unexecuted.
The native release PHPT runner passes the new fixture with `PHPR_REG_LOWER=0`
and `=1`: one pass, zero failures/skips each. Full release-suite results follow.

After cache rebuild, homepage HTML returned 200 in 4.926 seconds with correct
`/themes/contrib/mercury/fonts/...` preload paths. Before clearing the render cache
it retained the incorrect paths. This proves the scoped application fix, not
complete browser acceptance.

## Still failing

- Browser showed the hero plus repeated DOMXPath warnings and unresolved title
  `| [site:name]`. HTTP 200 is therefore a failure.
- `tests/probes/dom-namespace-axis.php` rejects Masterminds' serializer expression
  `namespace::*[not(.=../../namespace::*)]`. Zend outputs `html:2` and `body:0`;
  Rust warns and returns false for both. This establishes a concrete gap, not
  the cause of every browser warning.
- `/fonts/Outfit/latin.woff2` still stalls. Temporary instrumentation identifies
  Renderer::executeInRenderContext's sleep loop during a Canvas 404 subrequest:
  child Fiber started=true, suspended=false, terminated=false while the caller
  executes; it is not the current Fiber. All instrumentation was restored and
  byte-compared with originals.
- `tests/probes/fiber-nested-render.php` is bounded. Zend finishes both cases;
  Rust panics during nested Generator/Fiber suspension in exception unwinding.
  Its exact connection to the browser's running-state loop remains unproven.
  Both known-failing probes are excluded from the passing combined runner.

Only the selected Rust service was restarted to recover the listener. No Drupal
templates, exclusions, font aliases or warning suppression were introduced.
Full browser acceptance still fails. Next: task 01 for Fiber/Generator
continuations, task 08 for namespace XPath, task 06 for token replacement.

## Full release gate

`bash scripts/test-runtime-upstream.sh` completed with exit 101 after an 11m44s
optimized build: **1757 passed, 2 failed, 2 ignored**. The PHP 8.5.7 oracle was
required; no missing-oracle bypass was used. The two failures match the preceding
XMLWriter baseline: `vm::census::tests::op_index_maps_sampled_variants_in_range`
(177 vs 178) and `nessun_sorgente_rs_oltre_cap`. No new failing test name appeared.
The source-size guard remains failing: this patch adds seven lines to run.rs
(7662 to 7669, cap 7551) and two to calls.rs. No guard was disabled or cap raised.
This is not a green full gate or an upstream-ready submission.
