# Automatic hostname setup and contributor entry — 12 November 2026

## Implemented locally

OLI's existing `before_launch_site` hook now runs
`python3 scripts/register-runtime-hostnames.py` before publishing the tracker.
The script reads only the two labelled bases in `.ddev/.runtime-urls`, validates
them, and invokes `ddev hostname HOST 127.0.0.1` for unresolved local aliases.
It inherits the installer's terminal, never handles passwords, rechecks resolution,
and skips aliases already resolving to 127.0.0.1. A Coder target does not change
host DNS; its receipt says private routing is required. Target selection is saved
before building so even a failed build retains the intended host mode.

The network receipt is ignored and contains only an enumerated status. Failure
or cancellation produces a failing hook exit while still publishing the tracker.
OLI may continue after failed hooks, so the page and final message explicitly
report hostname status. Runtime-build failure may leave no site URLs; the final
message now reports unavailable URLs rather than crashing with a missing key.

`docs/TRY-IT.md` renders prominently above the Markdown task list, explaining the
standard PHP control, shared database, missing XMLWriter (task 16), historical
Fiber/Generator failure, deferred coverage and partial-request server limitation.
Task 08's classification is unchanged; XMLWriter's fresh-site blocker has its
own active task. No module was disabled and no runtime compatibility fix is claimed.

## Verification

Runtime/image unchanged: `drupalphprust:dev-release`, DDEV v1.25.0, project
`phprusttest1`. Prior receipt identifies source/runtime image and HTTP results.

- `python3 tests/test-runtime-hostnames.py`: five tests pass. Injected DDEV and
  resolver fixtures cover unresolved custom hostnames with ports, successful
  registration, idempotence, failed command, wrong post-registration DNS,
  credential URL rejection and Coder skip. No hosts file mutation in these tests.
- `python3 tests/test-tracker.py`: seven tests pass.
- `python3 tests/test-installer-launcher.py`: eight tests pass, including host
  target selection without evaluating unrelated dotenv values.
- After verifying both actual hostnames resolve locally, executed
  `bash .drupalaibp/setup-tracker.sh`: both report ready, no authentication or
  hosts mutation required, installed tracker published successfully.
- `python3 scripts/tracker.py --message`: all three actual URLs plus
  `Hostname setup: ready` printed.
- Reloaded the installed tracker in Chrome: both hostname cards show ready,
  and the visible introduction explains known failures with links to task owners.
- Repository checks, snapshot freshness and diff whitespace checks pass.

Limits: the user already registered these two hosts. The new missing-host branch
is fixture-tested; the real DDEV registration command was exercised earlier by
the user, not re-executed against a new hostname by the agent. A fresh OLI install
and Coder browser routing remain to be tested. This change makes the experiment
accessible and explains gaps; it does not fix XMLWriter or establish Drupal parity.
