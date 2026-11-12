# Discrete upstream contributions

Nothing is submitted upstream until the scoped Drupal end-to-end flow works and
the user decides to submit. Preparation can happen now: small fixes, independent
fixtures, explicit dependencies and local review. Do not publish one giant
“Drupal support” PR.

## Ownership

| Change | Destination |
| --- | --- |
| PHP language, compiler, VM, SPL/PDO/DOM behavior | francescotinti/php-rust |
| Project config, DDEV service, benchmark/handover tooling | jamieaa64/drupalphprust |
| Generic One Line Installer defect | Drupal one_line_installer, source in src/ |
| Coder host/routing/template defect | The actual DDEV/Coder template repository |
| Drupal bug reproducible on supported standard PHP | Relevant Drupal core/contrib project |

Do not submit installer/template changes merely because this config needs a hook.
Do not patch Drupal to conceal PHP Rust language differences.

## Candidate units and evidence

Order below is the integration patch order. Rows are candidate review units, not
an assertion of independent applicability. Several patches touch the same VM files;
use declared dependency chains or rebuild a minimal branch from pinned upstream.

| Patch | Review focus / regression |
| --- | --- |
| pdo-mysql | MariaDB PDO behavior and explicit unsupported modes; database probes; likely split driver from edge fixes |
| recursive-callback-iterator | Recursive callback and filesystem traversal; contains more than one original branch, split if independently testable |
| trait-autoload-case | Preserve trait name case through autoload; trait-autoload PHPT |
| trait-diamond | Reuse identical trait implementation; depends on trait resolution behavior |
| unserialize-private-fields | Restore private storage; inheritance round-trip fixture |
| builtin-object-dispatch | Dynamic builtin object protocols (Countable etc.) |
| callable-method-arguments | Deferred callback arguments resolved in caller scope |
| callable-autoload | Class autoload while building static callable |
| cross-unit-reference-call | Reference-returning function across included units |
| fiber-static-dispatch | Native Fiber calls through dispatch variants; builtin-dispatch dependency |
| fiber-callback-trampoline | Suspension through call_user_func wrappers; Fiber dispatch dependency |
| dom-doctype | DOM document/namespace construction; document limited namespace semantics |
| strtr-stringable-map | Stringable replacement-map conversion |
| concat-stringable | Stringable concatenation in slot operations |
| dynamic-reference-builtin | Correct mutation through dynamic reference-first builtins |
| enum-serialization | Enum round-trip serialization |
| static-reference | Reference binding to static call results; original NestedArray blocker |
| field-concat-stringable | Render-array leaf concatenation, aliases, reentrancy and COW; concat dependency |
| arrayaccess-reference | Nested reference-returning offsets, WeakMap dimension semantics; field-concat dependency |
| builtin-reflection | Six builtin descriptors used by Twig; metadata generated with PHP 8.4.8; not generic reflection support |
| return-reference | Forwarded method/static reference returns and arguments; static-reference dependency |
| touch-directory | touch an existing directory without opening it as a file |
| empty-class-autoload | Empty class name must not reach autoload |
| server-preconnect | Poll idle speculative sockets; partial request behavior remains a limit |
| object-vars-snapshot | Singleton internal reference wrappers must not leak into property snapshots |
| stream-constants | Stream-wrapper constant values and namespaced resolution |
| session-regenerate-cookie | Emit replacement cookie after ID rotation; HTTP regression |
| preg-byte-offset | Convert byte-mode start offset into internal Latin1 view; Unicode Twig reproduction |
| chained-magic-argument | Dereference receiver before deferred property read; magic/hooks/visibility fixtures |
| spl-fileinfo-ctime | Timestamp accessor, inherited iterator behavior and failed-stat RuntimeException |
| static-property-reference | Bind static storage to source cell, preserve old aliases, return references and execute finally; static-reference/return-reference dependencies |

The `static-property-reference` candidate covers static-property reference binding
and bare reference returns. Its alias/rebinding/finally fixture passes Zend parity and fixes Views class loading;
full browser acceptance is still blocked elsewhere. Reference-call RHS binding, parenthesized/dynamic forms and typed static-property enforcement
remain outside this candidate.
Runtime assertions differ in coverage: see HANDOVER.md rather than equating every
exported patch with completed browser validation.

## Reconstruct, edit and export

1. Run `scripts/prepare-runtime.py` into a fresh `.work` directory. Read upstream's
   AGENTS/process files. The current candidate is uncommitted in that checkout.
2. Reproduce the behavior on a documented standard-PHP oracle. Minimise the test.
3. Add a focused PHPT in tests/phpt, then implement the smallest correct runtime fix.
4. Review object/reference/COW, exception, reentrancy and error/notice semantics;
   follow upstream's single adversarial reviewer requirement.
5. Export only the new change relative to the applied series, not the entire tree
   diff against upstream. Use a temporary local baseline commit if helpful. Add the
   patch to series/Dockerfile and regenerate its hash in manifest.json.
6. Reconstruct from scratch and run affected tests, combined Drupal checks, and the
   upstream-required gates before calling a submission ready. Full upstream corpus
   and performance promotion gates have not been completed for this experiment.
7. Build per-fix branches from upstream with only necessary dependencies. Include
   fixture, explanation, before/after evidence, error behavior and coverage limits.
   Keep unrelated setup files and generated benchmark outputs out of runtime PRs.

A good PR says: trigger, previous failure, corrected behavior, minimal regression,
why this layer owns the fix, dependencies, executed validation and remaining gaps.
List behavioral dependencies explicitly; “applies after the integration branch” is
not enough for an upstream reviewer. Mark larger PDO/DOM work experimental until
it meets the maintainer's own quality bar.

The user authenticates and pushes. Prepare exact commands and reviewable commits;
do not submit through an API to bypass that preference.
