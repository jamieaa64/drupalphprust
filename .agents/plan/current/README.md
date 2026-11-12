# Overall plan

Run Drupal CMS on PHP Rust using DDEV and the One Line Installer; then compare
it fairly with standard PHP. Coder is an optional host. No migration tooling.

Open [the tracker](../../..//tracker.html) for the short view. Each step below
contains its work, tests, status and links to neighbouring steps.

1. [Portable repository](01-repository.md)
2. [Static references](02-static-references.md)
3. [Runtime compatibility](03-runtime.md)
4. [Drupal browser check](04-browser.md)
5. [Combined regressions](05-regressions.md)
6. [Local installation](06-local-install.md)
7. [Coder freeform](07-coder.md)
8. [Fast setup and restore](08-fast-setup.md)
9. [Compare with Zend](09-comparison.md)
10. [Upstream submissions](10-upstream.md)

Runtime fixes unblock browser acceptance. Local provisioning can progress alongside
that work. Performance and upstream submission follow correctness, not before it.

[Current summary](../../../PROGRESS.md) · [Handover](../../../docs/HANDOVER.md) ·
[Future scope](../future/README.md) · [Archive](../archive/README.md)

Update the affected step after verified progress. Keep it short; put detailed
receipts in docs/receipts. Regenerate tracker.html with `python3 scripts/tracker.py`.
