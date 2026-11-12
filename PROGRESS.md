# Drupal on PHP Rust

Updated: 2026-11-12

**Current installation:** PHP Rust homepage fails with missing `XMLWriter` ([active task 16](.agents/plan/current/16-xmlwriter.md)); standard PHP responds successfully. Earlier scoped-site blocker: Fiber / Generator suspension. See [installation receipt](docs/receipts/2026-11-12-tracker-landing.md).

**Still open:** Final Drupal flow and combined regressions; reference/reflection coverage; partial-request server blocking; separate upstream contributions. See [runtime completion](.agents/plan/current/01-fiber-generators.md).

**Test evidence:** 31 local patches; 29 earlier runtime probes passed. No end-to-end success or upstream submissions. Local installation and tracker launch are verified; named runtime links resolve after user-managed DNS repair; automatic installer hostname setup remains open. Coder integration, clean seed and performance remain; optional CMS gaps are deferred.

**Research alongside fixes:** [Upstream submission process](.agents/plan/current/13-upstream-process.md) and [comparisons beyond speed](docs/COMPARISON.md), separating today's behavior from Drupal-change opportunities.
