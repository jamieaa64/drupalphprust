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
| callable-unpack-reference | Preserve explicit references through dynamic argument unpacking; distinguish array-literal copy semantics; theme-hook regression |
| fiber-generator-continuation | Explicit resumable Generator operations within Fibers; scoped synchronous-helper rejection and cleanup |
| dom-namespace-axis | Namespace-axis values, inherited declarations, PHP namespace nodes and attribute separation |
| preg-extended-comments | Lex extended comments before regex rewriting; scoped flags, quotation and class boundaries |
| multisort-stringable | VM string conversion for natural/string sorting; side-effectful comparator order remains a limitation |
| user-wrapper-write | Registered wrapper file_put_contents, partial counts, contexts and flush/close behavior |
| stream-wrapper-registry | Registered protocol enumeration order and request reset |
| fiber-generator-collector | Resumable iterator_to_array collection across Generator/Fiber suspension |
| ref-builtin-field-spread | Reference builtin field targets with unpacked arguments |
| function-reference-forward | Function/dynamic-call reference return forwarding |
| static-reference-value | Value-context static calls copy returned references; explicit bindings retain aliases |
| preg-binary-replacement | Raw replacement bytes and capture offsets across byte/Unicode patterns |
| date-current-errors | Current-time DateTime constructors clear stale parse diagnostics |
| mb-stringable | Multibyte lower/upper case conversion uses VM Stringable input and encoding values |
| xmlwriter-memory | OO libxml memory writer; inheritance, buffer lifecycle, namespaces, encoding and PHP/libxml diagnostics; URI/DTD/procedural APIs excluded |

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

## Agent work queue

The [current plan](../.agents/plan/current/README.md) assigns each patch below to
one implementation/contribution task. Patched status does not imply upstream
readiness; each task includes remaining work and acceptance checks.

## Establish the submission process before publishing

Owned by [task 13](../.agents/plan/current/13-upstream-process.md). The existing
patch map is not a verified guide to each maintainer's current submission process.
For each actual destination, research its current contribution/process files,
issue tracker, supported base branch, test commands, sign-off/licensing rules and
AI-assistance disclosure policy. Record exact source links and date checked;
resolve conflicting historical instructions before preparing a submission.

Create a local contribution register containing: fix, destination, duplicate issue
or PR, prerequisites, branch/base commit, minimal fixture, required gate results,
draft title/body, review status and next action. Start with one small isolated fix
to prove the process before splitting the larger driver/compiler changes.

Prepare a dry-run submission packet with before/after evidence, minimal diff,
executed validation and known limits. Document the exact user-run authentication,
fork/push and issue/PR steps once the destination and branch are verified. Do not
invent commands or post maintainer questions during research. Publication remains
gated on end-to-end success and user approval.

Also document the follow-through: addressing review, rerunning dependent branches,
recording upstream acceptance, updating the pinned runtime and removing a local
patch only after its replacement passes the combined suite. An accepted upstream
change is not automatically integrated into this project.
