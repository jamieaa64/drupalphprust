# 16. Implement XMLWriter for out-of-box Drupal CMS

Status: patched

Destination: PHP Rust + this repository

The missing-XMLWriter homepage blocker is fixed locally. The unchanged Drupal
homepage returns HTTP 200 and its hero content renders in the browser. Standard
PHP still responds successfully. Authenticated create/edit/view remains open.

`xmlwriter-memory.patch` implements OO memory writers through libxml2, including
inheritance, namespaces, escaping, indentation, buffer lifecycle and PHP/libxml
error handling. Three parity fixtures and all 32 combined runtime probes pass.
Two Cargo integration tests pass in dev-release; the required adversarial review
found no remaining blocker for this tested scope. Fresh reconstruction matches.

URI, DTD and procedural APIs remain absent. Native buffers for expired objects
are reclaimed at later allocation or request shutdown, not necessarily `unset`.
PHP 8.5-specific reflection/type/error contracts are not certified. See
[implementation receipt](../../../docs/receipts/2026-11-12-xmlwriter-memory.md).

The developer image now bundles a pinned PHP 8.5.7 oracle. The real upstream
differential checks pass without bypasses; the full release run reports 1,757
passes, 2 ignored and 2 failures reproduced on the pre-XMLWriter baseline.
Run `bash scripts/test-runtime-upstream.sh`; full upstream promotion remains open.

## Work

- Identify the exact Drupal/dependency call path and XMLWriter methods required;
  do not assume the triggering module from the earlier scoped experiment.
- Minimise the failure into independent Zend parity fixtures.
- Implement the required XMLWriter runtime support in the reconstructed upstream
  checkout, following its process and adversarial review requirement.
- Export a discrete patch and keep series, Dockerfile and manifest aligned.
- Do not disable modules or alter Drupal core to count this task as complete.

## Test / finish

- [x] Reproduce ordinary HTTPS homepage: Rust 500, standard PHP 200 on this installation.
- [x] Match Zend output, escaping, state transitions and error behaviour for required APIs.
- [ ] Run upstream-required checks and relevant combined runtime regressions.
- [x] Rebuild the candidate and verify the unchanged CMS homepage.
- [ ] Continue the full login/create/edit/view journey; record subsequent blockers separately.

## Upstream

Not submitted. Scoped memory support is patched; full upstream promotion gates
remain open. Keep this as a discrete contribution with its review and fixtures.
The user handles authentication and pushes; no upstream submission yet.

## Initial reproduction on phprusttest1 (2026-11-12, before the patch)

The 31-patch candidate returned HTTP 500 for missing XMLWriter. The independent
sitemap fixture passed Zend and exited 255 on Rust at its subclass declaration.
This historical baseline is preserved in the
[reproduction receipt](../../../docs/receipts/2026-11-12-phprusttest1-xmlwriter.md).

[Overall plan](README.md) · [Runtime completion](01-fiber-generators.md) ·
[Installation evidence](../../../docs/receipts/2026-11-12-tracker-landing.md)
