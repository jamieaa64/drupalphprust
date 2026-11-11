# Test inventory

These files are fixtures and diagnostic tools, not blanket compatibility claims.
Use an explicitly selected disposable DDEV project; never run DB fixtures on client data.

- `test-oob-runtime.sh`: the selected focused differential suite; exact stdout and
  empty unexpected stderr, using DDEV PHP versus the candidate Docker image.
- `test-database.sh`: PDO/MariaDB and Drupal database fixtures, including deliberately
  rejected modes. These create/mutate temporary fixture tables in the DDEV database.
- `test-iterator.sh`: recursive callback iterator regression.
- `test-session-regenerate.py`: HTTP cookie-jar regression without printing cookies;
  takes the full URL of the temporary `probes/session-regenerate-http.php` endpoint.
- `test-server-preconnect.py`: idle socket regression; configurable `--host`, `--port`
  and `--path` of a real static asset. Five-second bound is functional, not a benchmark.
- `phpt/`: upstream-oriented standalone regressions; use the runtime's own runner and
  oracle requirements. Some mirrored PHP probes need Drupal/vendor and are not PHPTs.
- `probes/`: minimal regressions plus **historical diagnostic probes**. Not every file
  is a test with a PASS criterion. The runner is the authoritative selected suite.

Historical diagnostics include `component-*`, `twig-callables`, `dom-restrictions`,
`login-request`, `moderation-state`, `views-page-class`, `replace-markup`, and
`drupal-markup`. Read before running: some bootstrap a live Drupal installation;
component-cache experiments can require generated `.ddev/component-fixtures.json`
that is intentionally not shipped. Their absence is not a fresh-install failure.

The next active reproducer is `probes/views-page-class.php`. A new standalone PHPT
for the Views static-property reference-assignment lowerer failure is still needed.

Raw output goes to ignored `tests/results/` or `results/`. Put safe summaries with
commands, pins, exit status and caveats in docs/receipts. The original deployment's
private test account/password and browser profile were deliberately not copied.
