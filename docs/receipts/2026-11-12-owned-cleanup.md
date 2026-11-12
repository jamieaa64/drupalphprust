# Scoped project cleanup — 12 November 2026

Historical approach: superseded by [native DDEV add-on cleanup](2026-11-12-native-ddev-addon.md). The custom delete wrapper and unsubmitted OLI patch were removed.

## Cause and owning components

The public One Line Installer delete implementation was inspected from the
[project](https://www.drupal.org/project/one_line_installer) and source checkout
at `5c2c6467f52a891a18a3059af305d6f2ba2020b7`. Both deletion paths call
`ddev delete -Oy` then remove the folder; neither invokes project cleanup.
[DDEV delete hooks](https://docs.ddev.com/en/stable/users/configuration/hooks/)
were introduced in 1.25.5, later than the installed 1.25.0. Adding those hook names
to this older project is not a portable fix.

## Changes

Project registration keeps an ignored ownership receipt containing the checkout
root and aliases it attempts to create. Already resolving names are not adopted.
Cleanup uses only that receipt; it rejects another root, invokes DDEV removal for
each owned alias and keeps failed entries for retry. It never prunes Docker or
removes the reusable runtime image. Coder target routing remains separate.

Live previews stop when their own root disappears or their root receives a stop
signal. No PID or port ownership is guessed. AIBP can delete the static tracker
with the folder; there is no separate static tracker service.

The local OLI patch adds a conventional optional `.drupalaibp/before-delete.sh`
script, executed only after confirmation in the selected project. Cleanup failure
and DDEV failure abort before folder removal. Both deletion paths are covered.
The generated script was rebuilt using `bin/generate` with Bashly 1.4.0; generation
completeness checks passed. The patch includes source, generated script, tests
and contributor/user documentation. Nothing is submitted or published upstream.

Until that change ships, the project's `scripts/delete-project.sh` confirms and
performs scoped cleanup followed by DDEV deletion, leaving folder removal to the
user. An alternative is cleanup followed by the existing AIBP delete command.

## Verification

- Five project cleanup tests pass: only owned aliases removed, foreign root
  rejected, failure retains retry state, and two real isolated loopback tracker
  processes stop independently on root signal/deletion. The fallback delete
  wrapper also covers declined confirmation, cleanup failure and DDEV failure.
- Five hostname, seven tracker and eight installer tests pass.
- OLI deletion coverage includes 13 tests. The full 1,197-test suite completed
  with an unchanged agent_handoff assertion failure. Isolated handoff tests
  subsequently passed on both baseline and modified checkouts. The full-suite
  gate is not reported as green; no unrelated handoff fix was attempted.
- OLI ShellCheck 0.11.0: clean; CSpell 9.2.2: clean.
- Project integrity, generated tracker freshness and diff checks pass.
- Original orphan PID 54472 was confirmed using `ps` command and `lsof` cwd as
  `/Users/jamesabrahams/code/ai/phprust/phprusttest1`; its own execution session was
  stopped. Follow-up `lsof` on 8766 and `ps` on that PID returned no process.
  No other project's process or shared image was removed.

Limits: no real project/database deletion was performed for testing. Password
handling and actual hosts edits remain user-run. Older installs without ownership
receipts deliberately retain manual/pre-existing aliases for individual inspection.
Public AIBP remains unchanged until its maintainers publish a release containing
the prepared patch. See [cleanup instructions](../CLEANUP.md).
