# Early DDEV prerequisite and manual upgrade guidance

Date: 2026-11-12. User requested a check and commands they run themselves,
then retry installation. No automatic package upgrades, trust or authentication.

## Implementation

Both OLI configurations run the standalone `check-ddev-version.sh` host hook at
`before_configure_ddev`. OLI stages this early script into `.ddev/drupalaibp-hooks`
at project-directory creation. The script prints the detected/minimum version
and Homebrew/apt upgrade commands; Homebrew formula-specific trust is conditional
on the user receiving that error. It writes an owned DDEV overlay containing
`ddev_version_constraint: '>= v1.25.5'`. Recovery uses the same script in read-only
`--check-only` mode through `scripts/require-ddev.py`.

OLI's hook runner warns and continues after failure. The DDEV constraint is the
actual enforced stop at first service start, before Drupal Composer installation
and the Rust build. OLI's `step_start_ddev` calls `ddev start` under its normal
strict pipeline. A small configuration scaffold can remain. Guidance tells users
to preserve it and rerun from the parent with a new empty destination; it never
instructs automatic deletion of an existing site. No upstream OLI change needed
for this manual-upgrade workflow. Aborting before directory creation would need
additional installer support.

DDEV documents this constraint in its [configuration reference](https://docs.ddev.com/en/stable/users/configuration/config/#ddev_version_constraint).

## Validation and limits

- `python3 tests/test-ddev-prerequisite.py`: five tests passed, covering old,
  unknown, prerelease and supported versions; no automatic upgrade commands;
  repeatability, check-only behavior, owned-file protection and both configs.
- `python3 tests/test-installer-launcher.py`: eight passed.
- `python3 tests/test-runtime-config.py`: three passed.
- `python3 scripts/check-repo.py`: patch hashes, config and shell checks passed.
- Real `ddev config` on an empty temporary fixture accepted the overlay. The
  attempted real `ddev start` did NOT validate rejection on old DDEV: the system
  binary had meanwhile become 1.25.5. Full OLI rejection/retry and browser
  acceptance remain unverified. Remaining tests used command doubles only.

## Test incident

The real fixture start used the host's DDEV global state. DDEV detected an upgrade
from 1.25.0 to 1.25.5 and its noninteractive upgrade prompt defaulted to a global
poweroff. This stopped/removed containers belonging to other projects, including
RathboneAICanvasPoC, drupalrusttest2 and workbench-gitlab-test. The agent interrupted
its identified process (exit 130) and informed the user immediately. It did not
restart unrelated projects. This was an unintended test side effect.

Read-only checks afterwards found no running fixture container and confirmed
RathboneAICanvasPoC-mariadb and drupalrusttest2-mariadb volumes still present.
No database deletion was invoked or reported. These checks do not assert every
project's application health. Further prerequisite tests avoid the host Docker
and DDEV global state entirely. Do not rerun this test using a host-global
`ddev start`; use command doubles or a fully isolated test environment.
