# Project-owned cleanup

The installed Drupal files/database are owned by this DDEV project. The
`drupalphprust:dev-release` image and Docker build caches are reusable across
projects and must not be removed by a project deletion. No global Docker prune,
process-name kill, port-based kill or hosts-file sweep is used.

## Current published One Line Installer

The inspected AIBP delete command runs `ddev delete` and removes the project
folder, without invoking project cleanup. Until the accompanying upstream patch
is released, use this from the installed project:

```bash
bash scripts/delete-project.sh
```

It asks for explicit deletion confirmation, stops this checkout's live tracker,
removes only aliases recorded as created by this checkout, and runs DDEV deletion.
It preserves the source folder; remove that folder yourself afterwards. It stops
on cleanup/DDEV failure. Any DDEV password prompt is handled directly by you.
Alternatively run `python3 scripts/cleanup-project.py` first, then AIBP delete.
Do not interpret the presence of before-delete.sh as support in an older installer.

## Prepared AIBP change

The companion patch adds the `.drupalaibp/before-delete.sh` contract to AIBP.
It runs only after deletion confirmation, in the selected project's directory,
and stops deletion if cleanup fails. Both current-directory and selected-child
paths are tested. Source and regenerated installer are included; no change has
been submitted or published upstream. See `upstream/oli-cleanup.patch` and its
base revision in the cleanup receipt. DDEV 1.25.5 introduced native delete hooks,
but this project was using 1.25.0; we do not silently add unsupported hook names.

## Ownership and older installations

Registration now records the absolute checkout root and only aliases it attempts
to create in ignored `.ddev/.runtime-owned-hostnames.json`. Already resolving
aliases are not adopted. Cleanup refuses a receipt from another root, removes
one recorded alias at a time and keeps failed entries available for retry.
Manual/pre-existing aliases without ownership evidence are deliberately preserved.
For older installs, inspect those names and remove them individually yourself.

Live tracker servers now poll for deletion of their own checkout or a local stop
signal. Deleting that folder stops its servers even with old AIBP. Cleanup signals
only that checkout, without storing or trusting a process ID. Ordinary server
restart ignores an old stop signal. Installed static tracker pages disappear with
the project folder; they have no separate process to stop.

The earlier orphan preview on port 8766 was confirmed by PID, command and working
directory to belong to the deleted phprusttest1 checkout, then stopped through its
original execution session. No process or runtime image belonging to another
project was removed.
