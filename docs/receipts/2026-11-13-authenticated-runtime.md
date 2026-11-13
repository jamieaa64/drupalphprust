# Authenticated Drupal CMS runtime work — 13 November 2026

The selected disposable site is `phprusttest1`. The user explicitly authorized
its demo admin login. Login, create, edit, publish and anonymous view succeeded; cold-cache follow-up is recorded below.
No password or session data is included in this receipt.

## Verified changes

| Patch | Owner | Reproduction and result |
| --- | --- | --- |
| 39 `stream-wrapper-registry.patch` | Task 08 | Registered wrappers now appear in registration order and clear at request reset. Removes duplicate-registration warnings on Drupal subrequests. |
| 40 `fiber-generator-collector.patch` | Task 01 | `iterator_to_array` retains partial results across Generator/Fiber suspension; overwrite/destructor ordering and invalid keys match the oracle. Authenticated dashboard now loads. |
| 41 `ref-builtin-field-spread.patch` | Task 05 | Named builtins such as `array_push($element['#submit'], ...$handlers)` now compile with reference writeback. Utility-page form opens. |
| 42 `function-reference-forward.patch` | Task 05 | Reference-returning functions can forward function/dynamic-call references; Drupal's `batchGet()` wrapper retains its alias. |
| 43 `static-reference-value.patch` | Task 05 | Value-context static calls dereference their return; explicit `=&` retains aliases. Repeated ordinary assignment previously created a self-referential cell and crashed `isset` with native stack overflow. Drupal save now reaches validation. |
| 44 `preg-binary-replacement.patch` | Task 06 | Replacement bytes no longer pass through lossy UTF-8 conversion. Drupal's EF/FF date-format markers survive and dashboard dates render correctly. |
| 45 `date-current-errors.patch` | Task 06 | Current-time mutable/immutable constructors clear stale parse diagnostics; setters retain diagnostics. Drupal revision timestamp validation now succeeds. |
| 46 `mb-stringable.patch` | Task 06 | Multibyte case conversion resolves Stringable input and encoding arguments; cold-cache token label warnings triggered this follow-up. |

Each change received the required independent adversarial review. Focused fixtures
match PHP 8.5.7 byte-for-byte with empty stderr. This is scoped acceptance, not
complete PHP or regular-expression parity.

## Commands and environment

Build inside the project's Serena service, pinned Rust 1.98.1:

```bash
ddev exec -s serena bash -c 'export PATH=/usr/local/cargo/bin:$PATH; cd /var/www/html/.work/php-rust/php-rust && CARGO_TARGET_DIR=/tmp/serena-target CARGO_BUILD_JOBS=1 cargo build --locked --profile dev-release -p php-cli -p php-server -p phpt-runner'
```

The differential executables are `/opt/php-oracle/php` (PHP 8.5.7) and
`/tmp/serena-target/dev-release/phpr`. Fixtures are under `tests/probes`.
Final development candidate: all **49** `tests/test-oob-runtime.sh` probes pass
against DDEV Zend PHP **8.4.27**, with identical output and empty stderr. New
focused fixtures also match the bundled PHP **8.5.7** oracle. Native project PHPT
results are **47/47 passed, 0 failed, 0 skipped** in each compiler mode:

```bash
ddev exec -s serena env PHPR_REG_LOWER=0 timeout 180 /tmp/serena-target/dev-release/phpt-runner --isolate --list-fails /var/www/html/tests/phpt
ddev exec -s serena env PHPR_REG_LOWER=1 timeout 180 /tmp/serena-target/dev-release/phpt-runner --isolate --list-fails /var/www/html/tests/phpt
python3 scripts/prepare-runtime.py --source .work/php-rust --destination .work/runtime-reconstructed-46
python3 scripts/check-repo.py
```

Reconstruction applies all 46 patches; all **49 patched source files** match the
edited checkout byte-for-byte. Hash/order and repository checks pass.

The candidate binaries were copied into the local DDEV runtime image and only
`phpr` was recreated. This is development activation, not a clean Dockerfile
release build. Exact candidate identity:

- Image: `sha256:e252597d43bd7ee21332b1c155d542dc069533bbbadd14e5fc944b0391caa967`.
- `phpr`: `e050bf3989539f991a314eeba1bec8ef0163117f7e875e5e0a18289c65296a1c`.
- `php-server`: `d8c9dea04a347daccc0b1e22c0b49ee12e5beb27e2617429353c30a8a8c09785`.

`bash scripts/test-runtime-upstream.sh` runs the unchanged release recipe with
PHP 8.5.7. Its final run is in progress. The last completed run was patch 37:
1,757 passed, 2 failed, 2 ignored. See [the previous receipt](2026-11-12-continuations-and-rendering.md).
The failures are the opcode census and Rust source-file size caps; no full green
release gate is claimed. The official upstream external corpus and performance
gates have not been run.

## Browser evidence and remaining work

- Demo login and authenticated dashboard succeed.
- Created Utility-page node 2 through PHP Rust, edited its body and published it.
  Drupal displayed its update success message and the edited content.
- Anonymous HTTPS GET to `/php-rust-verification-13-november` returned 200 with
  the expected edited body, no administrative toolbar and no runtime diagnostics.
- Restarted only `phpr`, rebuilt Drupal caches with `ddev drush cache:rebuild` and
  confirmed the saved content persists. This exposed Token module translated-label
  warnings in `mb_strtolower`; after patch 46 and another cache rebuild, the
  published page renders cleanly with no warnings or placeholder class names.
- The first create redirect used HTTP after repeated diagnostic retries; direct
  HTTPS view succeeded. The later edit/publish redirected correctly to HTTPS.
  A fresh create redirect should be checked again before claiming it fully resolved.
- Earlier filter-settings warnings did not recur on the fresh form. Rich-text editor
  initialization and broader cold-cache plugin discovery still need coverage.
- General regex limits remain: empty/nonempty alternatives can skip a subsequent
  nonempty match; invalid `/u` array subjects remain null entries instead of being
  omitted. Both predate patch 44 and are outside its byte-preservation claim.
- General textual DateTime constructor diagnostics, dynamic by-reference builtin
  calls, strict-mode string coercion and unsupported native-helper Fiber suspension
  remain broader limits. The fixes do not establish complete PHP compatibility.

## Next work

1. Resolve the remaining release gate failures and preserve their exact outcomes.
2. Test a clean One Line Installer build from the pushed repository; verify
   hostname/client setup and deletion cleanup on that installation.
3. Validate DDEV Freeform integration on the known Coder deployment, with the user
   performing authentication, template publication and workspace updates.
4. Cover rich-text/media/sitemap and remaining runtime gaps with independent fixtures.
5. Prepare discrete upstream contribution packets, then request approval for actual
   submissions. No upstream PRs or performance comparison have been made.

Temporary debugger images, diagnostics, raw browser state and local logs remain
outside Git. No Drupal workaround was retained: the temporary validator diagnostic
was restored byte-for-byte before the successful save.
