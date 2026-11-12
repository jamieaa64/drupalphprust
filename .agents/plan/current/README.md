# Overall plan

Provide a reproducible environment where people can try Drupal CMS on PHP Rust,
see documented failures and take over tasks. Full runtime parity is not required
to share the environment. Continue making the out-of-box CMS baseline work,
install it through DDEV / One Line Installer, then explore its capabilities, limitations and tradeoffs against Zend.
Coder is optional. Migration tooling, OAuth and MCP are excluded.

These are agent implementation/contribution tasks. Tests are acceptance criteria
inside each task, not separate project milestones. **Patched** means the described fixes are implemented locally with recorded tests.
**Partial** is reserved for identified unfinished implementation within a task,
not simply waiting for upstream. Each detail explains scope and evidence.
Upstream status is separate: not submitted, prepared, submitted, then merged
(committed upstream). No fixes have been submitted or merged upstream yet.

1. [Complete Drupal runtime support](01-fiber-generators.md)
2. [PDO MySQL driver](02-pdo-mysql.md)
3. [Iterators and filesystem](03-iterators-filesystem.md)
4. [Class loading and serialization](04-class-loading.md)
5. [Callables and references](05-callables-references.md)
6. [Rendering and Twig semantics](06-rendering-twig.md)
7. [Sessions and HTTP serving](07-sessions-http.md)
8. [Deferred CMS compatibility](08-optional-cms-gaps.md)
9. [Portable one-line installation](09-installer.md)
10. [Coder tracker integration](10-coder-tracker.md)
11. [Fast build and clean seed](11-fast-seed.md)
12. [Compare capabilities and tradeoffs](12-benchmark.md)
13. [Work out upstream submission process](13-upstream-process.md)
14. [Benefits now versus Drupal changes](14-drupal-opportunities.md)
15. [Partial HTTP request handling](15-http-partial-requests.md)
16. [Implement XMLWriter for out-of-box Drupal CMS](16-xmlwriter.md)

The fresh installation homepage now renders after the XMLWriter fix (task 16);
the earlier scoped Fiber/Generator blocker and authenticated flow remain open. Installer and Coder integration can
progress independently. The bundled DDEV add-on now requires DDEV 1.25.5+,
uses native deletion and a separate tracker hostname; full fresh-install
acceptance remains open (task 09). Validate homepage, login, create, edit and view after
runtime fixes. That journey checks compatibility; it is not the project's purpose.
Prepare independent upstream changes along the way, submit only after end-to-end
acceptance and user approval. Optional CMS gaps remain deferred. Comparison design and upstream-process research
can start now; claims about running Drupal require executed experiments.
[Comparison study](../../../docs/COMPARISON.md) separates today's behavior from
opportunities requiring Drupal or runtime changes.

[Tracker](../../../tracker.html) · [Summary](../../../PROGRESS.md) ·
[Handover](../../../docs/HANDOVER.md) · [Testing](../../../docs/TESTING.md) ·
[Contribution map](../../../docs/UPSTREAM.md) · [Future](../future/README.md) ·
[Archive](../archive/README.md)
