# Static-property reference compatibility — 2026-11-12

## Scope and source

Pinned runtime: `72f688c3183b79858c01d250ce3999d86e143544` plus ordered
repository patches. Development image `drupalphprust:dev-release`, built using
`docker build --build-arg RUNTIME_PROFILE=dev-release -f runtime/Dockerfile`.
Disposable Coder site `drupal-oob`; DDEV standard PHP 8.4.27 is the differential
oracle. Local PHP 8.4.8 generated the PHPT expectation.

## Results

- Old `canvas-phpr:oob-dev` fails the new fixture at static reference assignment.
- First candidate built but failed alias-return parity with notices. Fixed the
  missing bare-static return lowering; no workaround in Drupal core.
- Binding replaces the declaring-class storage cell, preserving old aliases when
  rebound. Literal static reference returns use the live cell.
- Single adversarial review caught finally being skipped. Shared ReturnRef
  completion now parks the reference and runs finally. Added a regression checking
  both finally output and mutation through the returned alias. Re-review found no
  remaining blocker in that change.
- `bash tests/test-oob-runtime.sh /home/coder/drupal-oob drupalphprust:dev-release drupal-oob`:
  exit 0, all 28 probes passed with empty stderr and byte-identical stdout.
- Actual Drupal Views class probe returned `bool(true)` after the reference fix
  (before the final finally amendment); browser acceptance remains separate.
- All 30 patches reconstructed cleanly; nine changed source files matched the
  working candidate byte-for-byte. Local-source reconstruction exposed an existing
  branch-name collision; checkout now uses `-B` only in the newly created clone.
- Fresh Chromium login reached `/admin/dashboard?check_logged_in=1`, then HTTP 500:
  `Call to undefined method SplFileInfo::getCTime()` in ProjectInfo.php line 84.
  This is the next discrete candidate patch, not proof of completed E2E.

## Limits

Static reference-call RHS bindings, parenthesized/dynamic return forms and typed
static-property enforcement remain outside this fix. Full upstream corpus and
performance promotion gates were not run; this is a development compatibility
candidate, not a promoted runtime release. The existing comparison container was
used, not the new DDEV sidecar or One Line Installer integration.
Raw logs and generated test credentials remain outside Git.
