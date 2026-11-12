# Try the project: what works and what is missing

This is an experimental development environment, not a finished replacement for
PHP. The installer builds the candidate, starts both runtimes and opens this
tracker so you can explore failures and take over a task. Completing every PHP
Rust feature is not a prerequisite for sharing the environment.

## Start here

Use **Drupal on standard PHP** for the working control homepage. Use **Drupal on
PHP Rust** to try the candidate against the same Drupal files and database.
Changes made in either site affect the other; service status alone does not prove
runtime compatibility. Use synthetic content, and do not compare performance on
this shared database.

## Current known failures

- **Fresh CMS homepage: missing XMLWriter.** PHP Rust returns HTTP 500 on the
  observed installation because this PHP class is absent. This is expected to
  remain broken until [task 16](../.agents/plan/current/16-xmlwriter.md) is fixed.
  The exact dependency call path still needs investigation. Standard PHP returned
  HTTP 200 on the same installation. Reinstalling does not implement XMLWriter.
- **Earlier reduced-feature experiment: Fiber suspension inside a Generator.**
  Page creation reached this runtime failure after earlier fixes and explicit
  feature exclusions. [Task 01](../.agents/plan/current/01-fiber-generators.md)
  owns it; it has not yet been reached on the untouched fresh CMS installation.
- **Additional known coverage gaps:** DOM XPath, stream-wrapper writes, request
  cron and other CMS paths remain unverified or incomplete. See the historical
  scope in [task 08](../.agents/plan/current/08-optional-cms-gaps.md). XMLWriter now
  has its own active task; the older deferred classification is not a fresh-site
  compatibility claim.
- **HTTP server limitation:** partial requests may block the development server.
  [Task 15](../.agents/plan/current/15-http-partial-requests.md) owns this. Do not
  treat the experimental server as production-ready.

## If site links do not open

Local installation registers unresolved runtime hostnames through DDEV. Enter any
administrator password directly in the installer's terminal. If that step fails
or is cancelled, run `python3 scripts/register-runtime-hostnames.py`, followed by
`python3 scripts/tracker.py --install`. The tracker shows hostname setup separately
from timestamped service status and runtime compatibility. Coder users need their
existing private workspace routes or tunnels; see [Coder access](CODER.md).

## Pick up the work

Select a numbered task below for scope, tests and upstream status. **Patched**
means implemented locally; it does not mean submitted upstream or full CMS parity.
Start with [handover](HANDOVER.md) and [testing](TESTING.md). Record the image,
fixture and exact result in a receipt. Do not conceal runtime failures by silently
turning off Drupal features. Historical exclusions are documented experiments,
not automatic installer changes. No end-to-end or speed claim has been established.
