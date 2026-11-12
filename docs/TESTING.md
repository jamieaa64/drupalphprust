# Testing and comparison plan

For the broader study of capabilities, cost, security, agent workflows and Drupal
architecture opportunities, read [Comparison study](COMPARISON.md). This document
provides execution controls; latency is only one experiment.

Keep four independent verdicts: **runtime compatibility**, **installer/DDEV setup**,
**Coder accessibility**, and **performance**. Passing one is not proof of the others.

## 1. Repository integrity — run during this extraction

```bash
python3 scripts/check-repo.py
python3 scripts/prepare-runtime.py
```

Check JSON, shell syntax, source pin, patch hashes and Docker/series order. Reapply
the entire series to a fresh upstream checkout and compare reconstructed runtime
sources with the earlier candidate. Check safe files/relative links, then inspect
Git's staged contents for secrets, database dumps, site files and raw logs.
CI currently runs only repository checks. A green CI badge is not Drupal acceptance.

## 2. Runtime regressions — each change, then the combined candidate

Use an explicitly selected disposable DDEV site. Example after installation:

```bash
bash scripts/build-runtime.sh dev-release
bash tests/test-oob-runtime.sh "$PWD" drupalphprust:dev-release drupalphprust
bash tests/test-database.sh "$PWD" drupalphprust drupalphprust:dev-release
bash tests/test-iterator.sh "$PWD" drupalphprust:dev-release
```

Replace the final project name with the actual generated DDEV name. The scripts
use Docker container/network names, so a wrong name is not a harmless parameter.
The database fixtures mutate disposable test tables; never run them against client
data. Require exact stdout agreement and empty unexpected stderr on both engines.
Some probes require vendor/Drupal; standalone tests are also exported under tests/phpt.
Use upstream's PHPT runner/process for contribution validation, not a fabricated
claim that a lab stdout comparison replaces upstream's corpus gates.

For HTTP checks, run the idle-preconnect fixture against the candidate's base URL.
Read `tests/test-server-preconnect.py` for its static asset expectation; adapt it
explicitly if the installed theme differs. For session rotation, temporarily copy
`tests/probes/session-regenerate-http.php` to a uniquely named path in the disposable
web root, run `tests/test-session-regenerate.py URL` against both engines, and remove
that endpoint in a `finally`/shell trap. Never print cookie values. Run this while
no browser is generating competing requests to the sequential development server.

## 3. Browser acceptance — required before upstream submission or benchmarks

Use browser automation with fresh snapshots and observed controls. Use a dedicated
local disposable account; credentials stay in an ignored file or user-managed secret.

1. Homepage: expected theme/hero/content, no raw class names, warnings or errors.
2. Login: submit the actual form; verify a subsequent authenticated request, not
   only a redirect or session row. Check dashboard content independently.
3. Create: open a standard page form, fill title/body, save/publish, verify success
   and database persistence through Drupal's normal interfaces.
4. Edit: change the saved page through the UI, save and confirm the changed text.
5. View: fresh request and anonymous browser context show published content correctly.
6. Reload/restart: verify the result after a service restart and record any limitation.

Repeat on standard PHP with the same application version, enabled modules and seed.
Inspect browser console/server diagnostics and response content. HTTP 200, a title
or a heading alone is insufficient. Record which runtime handled each request.
No successful Rust create/edit/view has been recorded yet.

## 4. Installer and DDEV smoke test — prepared, deliberately not run now

Use a clean source checkout and disposable target outside it. Record installer
revision/version, source branch/commit, DDEV/Docker versions and architecture.

- Run USAGE.md's local-source One Line Installer command once.
- Confirm profile installed Drupal CMS Starter and Composer lock was captured.
- Confirm source overlay retained the generated base DDEV name/config and docs/plans.
- Require `.ddev/.runtime-ready`, correct image, actual running phpr service and
  expected page responses; installer may swallow hook failures as warnings.
- Check Zend still works, files/database are shared as designed, `ddev phpr` works.
- Stop/start DDEV and verify both endpoints again.
- Verify optional exclusions were not silently applied by the installer.

Full rerun/upgrade/failure matrix is deferred in `.agents/plan/future/README.md`.
Do not spend this phase testing all One Line Installer features.

## 5. Coder smoke test

Run the same installer/config in DDEV freeform. Validate the additional host/router
contract in CODER.md: project name, Docker permissions, persistence, private URLs,
login cookies and restart behavior. Do not use the old CMS migration template as a
hidden dependency. Template changes, if needed, are their own contribution.

## 6. Normal Drupal CMS control and fair runtime comparison

Maintain two kinds of control:

- **Untouched standard Drupal CMS:** identifies features currently excluded from
  PHP Rust. This is a coverage reference, not a fair speed comparator if it runs
  more modules/features.
- **Matched standard-PHP control:** identical lock, modules, settings, seed DB/files
  and content to the candidate. This is the speed/correctness comparator.

Capture a clean synthetic seed after setup; give it a content hash, include files
and config, and exclude real client data. Restore into separate named DDEV projects
for measured runs. Each engine needs its own DB/cache/files to avoid warming or
corrupting the other arm. Warm each independently and use matching cache states.
Prepared seed/restore automation is future work; do not reuse the old migration
harness with MCP/OAuth gates.

Record before every run: Git/runtime/patch manifest hashes, image ID and profile,
Composer lock hash, Drupal/modules/theme, PHP/database/DDEV/Docker versions, host
CPU/RAM/architecture, resource limits, server stack, OPcache/JIT settings, fixture
and seed identity, cache policy, run order and sample count.

Measure separately:

| Metric | Boundary | What it answers |
| --- | --- | --- |
| Runtime source build | Clean build vs cached rebuild | Developer/build cost |
| Installer provisioning | Empty project to ready verified services | Setup cost |
| Seed restore | Prepared artifact to verified usable site | Fast start strategy |
| Service startup | Existing image/site stopped to ready | Restart cost |
| First request | Known cold application cache | Cold request cost |
| Warm requests | Equivalent warmed caches, valid responses | Steady latency |
| Memory/CPU/errors | Same workload and concurrency | Resource/robustness cost |

Use release builds for reported performance. Do not compare Rust dev-release with
an optimized Zend stack and call it an interpreter result. The supplied script:

```bash
python3 scripts/benchmark-http.py --zend http://127.0.0.1:ZEND_PORT \
  --rust http://127.0.0.1:RUST_PORT --contains 'Your hero content goes here' \
  --rounds 20 --output results/homepage-http.json
```

Replace port placeholders. It records sequential ABAB whole-stack requests and
excludes the first observation from warm summaries. It validates status/text but
still needs prior browser/error validation. Fixed ABAB order can bias results;
repeat with reversed order or extend to seeded random/ABBA order before publishing.
First observation is not a cold-cache measurement unless caches were controlled.
Report raw samples, median/p95, errors and variance across repeated runs. No speedup
claim until both arms pass all correctness checks. Match serving stacks in a later
experiment if isolating interpreter cost; nginx/FPM versus native server is a
whole-stack comparison.
