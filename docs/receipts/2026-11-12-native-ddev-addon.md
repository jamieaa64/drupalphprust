# Dedicated tracker and native DDEV lifecycle

Date: 2026-11-12. Implementation prepared against project commit 09f4cd8.
No runtime patches, database changes, live-site upgrades or upstream submissions.

## Implemented locally

- Bundled `php-rust` DDEV add-on installs service configuration, host commands
  and native `pre-delete` cleanup. Minimum DDEV version: 1.25.5.
- DDEV/Compose builds the pinned PHP Rust candidate as
  `debian:bookworm-slim-${DDEV_SITENAME}-phpr-built`. No shared custom image tag.
- Static tracker service uses `tracker.<actual-project-host>`, separately from
  `php.` and `rust.`. Only `.ddev/.tracker` is mounted into this service.
- Exact scheme/host/port comes from DDEV. OLI's existing `.ddev/.env` LAUNCH_PATH
  override selects the tracker; runtime and hostname setup precede completion.
- Deleted the bespoke delete wrapper and unsubmitted OLI cleanup patch.
  Markdown task navigation, runtime compatibility labels and Coder option remain.

## Executed checks

An isolated `/tmp/phprust-native-addon-fixture` used DDEV v1.25.5 downloaded to
`/tmp/phprust-ddev-tools/ddev`. System DDEV remains v1.25.0. No existing site's
services were restarted or databases touched.

- `ddev add-on get ./ddev-addon`: installed all four managed files; actual
  pre/post-install working directories confirmed.
- `ddev utility compose-config`: valid configuration. web has base/php hosts;
  phpr has rust host; tracker has tracker host. Rust loopback port allocated.
- `PATH=/tmp/phprust-ddev-tools:$PATH python3 scripts/configure-runtime.py`:
  generated `https://tracker.phprust-native-addon-fixture.ddev.site/` and set
  OLI's LAUNCH_PATH through `ddev dotenv set`.
- Native `ddev delete --clean-containers=false -Oy` from fixture root:
  invalid ownership receipt stopped deletion (exit 1); corrected empty owned
  receipt ran cleanup and deletion (exit 0).
- A zero-filesystem scratch test image with the fixture's Compose ownership label
  and project image tag was built. Native deletion removed it; subsequent
  `docker image inspect` confirmed absence. This verifies image lifecycle,
  not the PHP Rust build. No global prune or other project's deletion was run.
- `ddev add-on remove php-rust`: ran removal cleanup and removed managed files.
- Three routing/configuration tests, five hostname tests, eight launcher tests,
  seven tracker tests and four cleanup tests passed. Cleanup tests use two
  loopback previews and prove only the selected checkout is stopped.
- `python3 scripts/check-repo.py`: 31 patch hashes/order and shell/config contracts
  pass. The existing DDEV 1.25.0 is rejected by the new version guard.

The named fixture deletion before registration did not exercise its hook;
the successful and failed hook checks above used current-directory deletion,
which is the path used by AIBP.

## Remaining acceptance and upstream prerequisite

Subsequent scope: the user requested manual upgrade instructions rather than an
automatic upgrade offer. The [early prerequisite check](2026-11-12-ddev-prerequisite.md)
now provides those instructions with a native DDEV constraint, without an OLI
change. The analysis below describes the earlier automatic-upgrade proposal.

Full new PHP Rust build, fresh OLI installation, actual browser launch, HTTPS
service identities, restart persistence and Coder access remain unverified for
this revision. A `ddev launch --print-url` attempt on the stopped fixture tried
starting it through the older system DDEV and was rejected for unsupported
pre-delete configuration; it did not verify browser launch. No live installation
is claimed upgraded. Old installation receipts remain historical evidence only.

OLI's `_ensure_ddev_cli` currently accepts any existing `ddev` executable. The
inspected source is One Line Installer base 5c2c6467f52a891a18a3059af305d6f2ba2020b7.
It has no minimum-version configuration. Existing older-DDEV users therefore
still need a manual upgrade with the public installer. This is an OPEN installer
integration dependency, not a finished one-line experience.

The smallest upstream improvement is a configured minimum DDEV version enforced
in `_ensure_ddev_cli`, before project creation, with an upgrade offer using the
supported DDEV installer and a version recheck. Declining or a failed upgrade
must stop before scaffolding. Ordinary config hooks are best-effort and cannot
provide this prerequisite guarantee. That improvement is identified, not yet
implemented or submitted. Cleanup itself needs no OLI patch. A tagged OLI release
would be required before the public one-line command gains prerequisite handling.

DDEV references: [1.25.5 release](https://ddev.com/blog/release-v1-25-5/),
[add-ons](https://docs.ddev.com/en/stable/users/extend/creating-add-ons/),
[hooks](https://docs.ddev.com/en/stable/users/configuration/hooks/).
