# XMLWriter memory support — 12 November 2026

The unchanged Drupal CMS homepage now renders on PHP Rust. This fixes the
missing-class blocker; it does not establish full CMS compatibility or speed.

## Candidate and oracle

- Project: `phprusttest1`, DDEV 1.25.5, arm64 Docker Desktop.
- Upstream pin: `72f688c3183b79858c01d250ce3999d86e143544`, existing 31 patches
  plus `xmlwriter-memory.patch` (32 total).
- Rust toolchain: 1.98.1; serving build: `dev-release`, not a promoted release pin.
- Oracle: DDEV PHP 8.4.27, libxml 2.9.14. Runtime identifies as PHP 8.5.7;
  PHP 8.5.7 was subsequently added as the upstream differential oracle (below);
  the three XMLWriter fixtures also match it exactly. Full reflection/type/error
  coverage remains unproven.
- Running image: `debian:bookworm-slim-phprusttest1-phpr-built`, ID
  `sha256:ccbe09a0f1f5a9a17390cd983126a569d1f69041d3debae46aefdac131c75576`.
- Serving CLI SHA256: `4db982691cf30ed600d991f2ac99a1f0a2ee68b99f2093c1f2756a3362dbf15b`.
- Serving HTTP binary SHA256: `bee8ae90573664d680acf2183aff742f55d27ece11fbd26e38d95431d7a232da`.
- Composer lock SHA256:
  `80530d439d9a78720834b53bbc67f6b4fc4d6a6ba2911ad28142f58e936ade58`.

## Scope and review

The patch supplies the OO in-memory writer API used by Simple Sitemap's
`SitemapWriter extends XMLWriter`, including a child constructor that does not
call its parent. Libxml supplies XML escaping, indentation, namespaces, encoding
and document/element/attribute state. Output can be retained, drained and reopened.
Native state is separate from PHP properties and serialization. Weak object
identity prevents reused object IDs from inheriting a buffer; clones are rejected.

The single required adversarial reviewer initially identified procedural-dispatch
and diagnostic mismatches. Procedural wrappers were removed. PI wording was
corrected; libxml diagnostics now respect PHP handlers and internal-error mode,
including delayed encoder errors during output. Callback state is restored before
PHP error handlers execute. Final scoped verdict: no remaining blocker found for
the tested OO memory contribution.

URI, DTD and procedural APIs remain absent. Buffers belonging to expired PHP
objects are reclaimed at later allocation or request shutdown, not necessarily
immediately at `unset`. Destructor-time diagnostic equivalence remains unproven.
The change is not a claim of complete XMLWriter parity or successful sitemap
creation. No Drupal source, module enablement or database reset was used.

## Executed validation

- `cargo build --locked --profile dev-release -p php-cli -p php-server` in the
  Serena DDEV container, target `/tmp/serena-target`: passed.
- `cargo test --locked --profile dev-release -p php-runtime --test xmlwriter`:
  2 integration tests passed (inherited writer/escaping/draining; catchable
  uninitialized-object, clone and invalid-PI errors).
- Each of `tests/probes/xmlwriter-{sitemap,state,errors}.php` ran on Zend and the
  candidate, exact stdout agreement and empty stderr: 795, 955 and 965 bytes.
  Corresponding standalone PHPT expectations are committed.
- Native release `phpt-runner --isolate --list-fails` on those three PHPTs:
  3 passed / 0 failed / 0 skipped with each of `PHPR_REG_LOWER=0` and `=1`.
- `python3 scripts/prepare-runtime.py --source .work/php-rust --destination
  .work/xmlwriter-final`: fresh reconstruction succeeded; all six changed/new
  runtime files were byte-identical to the edited candidate.
- `docker build --build-arg RUNTIME_PROFILE=dev-release -t
  debian:bookworm-slim-phprusttest1-phpr-built runtime`, then `ddev start`: passed.
  Running container image ID matches the built image.
- `bash tests/test-oob-runtime.sh "$PWD"
  debian:bookworm-slim-phprusttest1-phpr-built phprusttest1`: all 32 probes passed,
  including the three XMLWriter cases. No prior probe regressed.
- HTTPS Rust homepage: 200; browser at
  `https://rust.phprusttest1.ddev.site/` showed the expected hero and CTA, without
  visible fatal/warning/class placeholders. Standard PHP homepage remained 200.
- A subsequent login request timed out after browser activity. Server log showed
  an accepted connection with no completed request. After restarting only
  `ddev-phprusttest1-phpr`, a direct anonymous `/user/login` request returned 200
  with username/password form controls and no error indicators. This is consistent
  with task 15's existing connection-handling limitation, not proof of its cause.
  No login was submitted. Authenticated create/edit/view remains open.
- `python3 scripts/check-repo.py`: 32 patch hashes/order and static checks passed.

## Upstream gate limits

`CARGO_BUILD_JOBS=2 cargo test --locked --release` was killed during fat-LTO
linking (SIGKILL). A single-job retry compiled successfully, then stopped at the
upstream differential harness because its expected PHP oracle executable is
not installed inside Serena. The final run used the explicit upstream option:

```bash
PHPR_ALLOW_MISSING_ORACLE=1 CARGO_BUILD_JOBS=1 CARGO_TARGET_DIR=/tmp/serena-target \
  cargo test --locked --release --no-fail-fast
```

Initial diagnostic run, exit 101: Cargo reported 1,757 passed, 2 failed, 2 ignored. Three reported passes
are explicit missing-oracle early returns (`builtins_match_oracle`,
`evaluator_matches_oracle`, `differential_operators_vs_oracle`), leaving 1,754
executed passes. Both new XMLWriter tests passed in release. The two failures
are `vm::census::tests::op_index_maps_sampled_variants_in_range` (177 vs 178) and
`nessun_sorgente_rs_oltre_cap` (pre-existing source line-count limits). Baseline
comparison reproduced both failures on a fresh **31-patch** checkout without
XMLWriter, using dev-release focused Cargo runs. The census failure is identical;
the same 11 files already exceed their caps. XMLWriter adds 5 lines to vm/mod.rs,
2 to vm/run.rs and 1 to lower/mod.rs, all already over cap. No thresholds were
raised or failures hidden. This is **not a green full upstream gate**.

### Final run with the real upstream oracle

At the user's request, the developer image now contains PHP 8.5.7 from
`php:8.5.7-cli-bookworm@sha256:1ff2cdf2754bcac61128bfda455d418639c0f45a8b1583b4b90f9873a2ba1368`.
The executable, required shared libraries and build-time version/XMLWriter check
were verified. Its source archive and license are retained in `/opt/php-oracle`.
The wrapper disables CLI OPcache/JIT; the launcher explicitly unsets the skip flag.
The same bundle was installed into the live Serena container without restarting
MCP or discarding its test cache; the built developer image supplies future starts.

`bash scripts/test-runtime-upstream.sh` completed with **1,757 executed passes,
2 failures, 2 ignored** (exit 101). All three differential tests executed against
PHP 8.5.7 and passed; none was bypassed. The only failures remain the two baseline
guards identified above. The three XMLWriter fixtures also matched the official
PHP 8.5.7 image byte-for-byte with empty stderr (795/955/965 bytes), using
`docker run --rm --network none` with the project mounted read-only.

The canonical corpus
script depends on its maintainer's `/Volumes/Extreme Pro/Claude/...` PHP source,
runner and frozen paths, which are unavailable in this checkout. No canonical
pin/stash, full corpus promotion or R=5 performance gate is claimed. This is a
local compatibility candidate; upstream submission remains gated on the complete
Drupal flow and user approval.

References: [PHP XMLWriter](https://www.php.net/manual/en/book.xmlwriter.php),
[libxml writer API](https://gnome.pages.gitlab.gnome.org/libxml2/html/xmlwriter_8h.html).
