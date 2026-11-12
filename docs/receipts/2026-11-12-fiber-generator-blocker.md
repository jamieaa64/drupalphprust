# Next blocker: Fiber suspension inside Generator

## Verified state

31 ordered patches reconstruct successfully. Development Docker build exits 0.
The combined differential runner passes all 29 probes, including
`spl-fileinfo-ctime`. The timestamp fixture covers file/directory/iterator values
and a missing-path RuntimeException, with empty stderr and byte-identical Zend
output. Review found that `@filectime` still invokes a user error handler; the
final patch uses a silent internal metadata lookup instead. The extended fixture
installs a throwing error handler and still receives RuntimeException. Re-review
found no additional blocker. The corrected build and all 29 probes exit 0
(`results/combined-runtime-ctime-handler.log` in the disposable workspace).
The 31-patch reconstruction also matches all three timestamp source files. This closes the missing-method failure on Drupal's update-check path.

The new candidate remains `drupalphprust:dev-release`, served by the disposable
comparison container for DDEV project `drupal-oob`. Browser `/node/add/page`
returns HTTP 500 with `VM: cannot suspend a Fiber from within a Generator
(unsupported nesting)` from Renderer::executeInRenderContext, line 639.

## Minimal reproduction

```bash
php tests/probes/fiber-generator-suspend.php
docker run --rm -i drupalphprust:dev-release phpr /dev/stdin < tests/probes/fiber-generator-suspend.php
```

Local Zend PHP 8.4.8: exit 0, generator and fiber both complete and receive the
resume value. PHP Rust: exit 255, internal panic in vm/exceptions.rs:241 after
printing `suspended:generator:start`. This is a known-failing fixture and is
intentionally not in the passing combined runner. PHPT records expected Zend
behavior, not an assertion that the runtime passes it.

## Root cause and next implementation work

`vm/coroutines.rs::native_fiber_static` parks frames starting at the fiber baseline.
`resume_generator` runs its own nested run_loop with a generator baseline. On
RunExit::Suspended it marks the generator Done and returns an ordinary error.
Its caller then attempts exception unwinding against frames already parked in
FiberState, explaining the empty-stack panic in the minimal reproducer.

Do not remove the rejection or treat a suspend as a yield/return. Preserve both
continuations and propagate suspension without ordinary exception unwinding.
On Fiber::resume, deliver the resume value, finish/resuspend the generator, and
continue its consumer exactly once. Generator consumers include foreach,
yield-from, direct Generator methods and synchronous helpers; inspect each
resume_generator/ensure_started call before changing the protocol.

Acceptance fixtures must include nested yield-from, repeated suspend/resume,
Generator::send, early close, exceptions/finally, nested Fibers and side-effect
counts (no replay of consumed operations). A clean unsupported error is better
than a panic, but does not satisfy Drupal acceptance. Do not bypass suspension
or edit Drupal core to hide this gap.

After a reviewed fix: rebuild, run focused and combined regressions, then verify
homepage/login/dashboard/create/edit/view on the selected disposable site.
Installer/DDEV sidecar smoke and performance benchmarks remain separate and
unexecuted. Raw browser snapshots and generated credentials stay outside Git.
