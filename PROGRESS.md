# Drupal on PHP Rust

**Goal:** Drupal CMS on PHP Rust, set up through DDEV / One Line Installer and
compared with standard PHP. No migration tooling.

**Now:** Standalone repository and handover prepared. Next runtime blocker:
Views `static::$pageRenderArray = &$element` fails lowering.

- [x] Preserve runtime patches and focused regressions; verify all 29 patches reconstruct the candidate.
- [x] Document current blocker, known limitations and how another agent resumes.
- [x] Prepare One Line Installer config and DDEV runtime service.
- [ ] Execute the new installer/DDEV integration smoke test (deferred for now).
- [x] Earlier site: database fixtures, homepage and persistent login work.
- [ ] Fix Views compiler gap and add a regression.
- [ ] Verify dashboard and create → edit → view.
- [ ] Run the expanded combined regression suite.
- [ ] Smoke-test the same setup in Coder freeform.
- [ ] Package a clean reusable seed for fast setup.
- [ ] Measure against normal Drupal CMS using release builds.
- [ ] Finalise separate upstream contribution commits after E2E success.

Details: [.agents/plan/current/README.md](.agents/plan/current/README.md).
No full Drupal compatibility or performance claim yet. Updated 2026-11-11.
