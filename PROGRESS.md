# Drupal on PHP Rust

Updated: 2026-11-12

**Next blocker:** Fiber / Generator suspension. This is not the only remaining work.

**Still open:** Final Drupal flow and combined regressions; reference/reflection coverage; partial-request server blocking; separate upstream contributions. See [runtime completion](.agents/plan/current/01-fiber-generators.md).

**Test evidence:** 31 local patches; 29 earlier runtime probes passed. No end-to-end success or upstream submissions. Installation, Coder integration, clean seed and performance work remain; optional CMS gaps are deferred.

**Research alongside fixes:** [Upstream submission process](.agents/plan/current/13-upstream-process.md) and [comparisons beyond speed](docs/COMPARISON.md), separating today's behavior from Drupal-change opportunities.
