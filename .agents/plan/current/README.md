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
17. [Resolve runtime release gate failures](17-release-gates.md)

The current installation has completed login, dashboard, Utility-page creation,
editing, publishing and anonymous viewing on PHP Rust. The saved page survives a
service restart. See the [current receipt](../../../docs/receipts/2026-11-13-authenticated-runtime.md)
for clean rendering after cache rebuild, exact build/test evidence and remaining limits.
The final release run has 1,756 passes, three failures and two ignored tests.
Task 17 owns the three unresolved failures. Next priorities are those fixes, fresh One Line Installer acceptance,
DDEV Freeform validation and discrete upstream contribution preparation. No full
runtime-parity or performance claim follows from the scoped browser journey.
No upstream fixes have been submitted. User approval remains required for that work.
[Comparison study](../../../docs/COMPARISON.md) separates today's behavior from
opportunities requiring Drupal or runtime changes.

[Tracker](../../../tracker.html) · [Summary](../../../PROGRESS.md) ·
[Handover](../../../docs/HANDOVER.md) · [Testing](../../../docs/TESTING.md) ·
[Contribution map](../../../docs/UPSTREAM.md) · [Future](../future/README.md) ·
[Archive](../archive/README.md)
