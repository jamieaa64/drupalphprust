# Overall plan

Make the agreed out-of-box Drupal CMS baseline run correctly on PHP Rust,
install it through DDEV / One Line Installer, then measure it against Zend.
Coder is optional. Migration tooling, OAuth and MCP are excluded.

These are agent implementation/contribution tasks. Tests are acceptance criteria
inside each task, not separate project milestones. **Partial** means some local fixes exist but implementation, coverage or contribution
work remains. No runtime family is complete. Fiber is the next reproduced blocker,
not evidence that the rest of Drupal works.

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
12. [Comparison tooling](12-benchmark.md)

Resolve Fiber/Generator suspension first; installer and Coder integration can
progress independently. Validate homepage, login, create, edit and view after
runtime fixes. That journey checks compatibility; it is not the project's purpose.
Prepare independent upstream changes along the way, submit only after end-to-end
acceptance and user approval. Optional CMS gaps remain deferred.

[Tracker](../../../tracker.html) · [Summary](../../../PROGRESS.md) ·
[Handover](../../../docs/HANDOVER.md) · [Testing](../../../docs/TESTING.md) ·
[Contribution map](../../../docs/UPSTREAM.md) · [Future](../future/README.md) ·
[Archive](../archive/README.md)
