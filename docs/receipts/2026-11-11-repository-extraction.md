# Repository extraction validation — 2026-11-11

Prepared locally for `https://github.com/jamieaa64/drupalphprust`. No remote push,
new Drupal install or Coder provisioning was performed for this extraction.

## Executed checks

- `python3 scripts/check-repo.py`: exit 0. All 29 patch hashes, application order,
  Docker upstream pin, config JSON, shell syntax and plan folders agree.
- `python3 scripts/prepare-runtime.py --source <earlier-runtime-clone>`: exit 0.
  Fresh non-hardlinked checkout at pinned upstream, all 29 patches applied cleanly.
  The source argument avoided a redundant network clone; the script defaults to
  the public upstream URL when another agent has no existing checkout.
- Compared all 35 patched/new runtime source files from the reconstruction to the
  earlier integration checkout: byte-identical. No sibling-repo source is needed
  by the committed Docker build or normal reconstruction path.
- `python3 -m compileall -q scripts tests`: exit 0.
- `docker compose -f runtime/docker-compose.phpr.yaml config --quiet` with synthetic
  DDEV_SITENAME/DDEV_APPROOT: exit 0. This validates Compose structure/interpolation,
  not DDEV's service lifecycle, mounts or application routing.
- `php -l` over 51 PHP probe files: all passed on the local PHP oracle.
- Active documentation Markdown links checked: no missing local targets.
- Source inventory excludes generated site files, databases, credentials, browser
  sessions, raw historical logs and `.work` source reconstruction.

## Not executed / not claimed

No One Line Installer run, new DDEV service startup, Coder template smoke test,
new runtime compilation, expanded PHP differential suite, full upstream corpus,
create/edit/view browser acceptance, seed restore or performance comparison.
Previous runtime/browser observations are recorded separately in HANDOVER.md and
are not treated as proof that the new installer/DDEV integration works.
