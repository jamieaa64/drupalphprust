# Small progress tracker

Open **tracker.html** in a browser or the site's preview window. It is a standalone
snapshot with no external libraries, login or network dependency. Select a task
to see its short work/test plan. Markdown remains the source of truth.

For a live preview from the repository root:

```bash
python3 scripts/tracker.py --serve
```

Open `http://127.0.0.1:8765` in the site window. It refreshes from the Markdown
files every five seconds. The server binds loopback and serves only the tracker
and its documentation snapshot, never the whole checkout. Use `--port` if needed.
Stop it with Ctrl-C. No Drupal, DDEV or Coder is needed to view progress.

After editing the plans, refresh the standalone file:

```bash
python3 scripts/tracker.py
```

Edit PROGRESS.md for the short summary. Edit numbered Markdown files under
`.agents/plan/current/` for implementation work, acceptance checks and contribution
ownership. Statuses: active, patched, partial, blocked, pending, deferred, done. **Patched**
means the described fixes are implemented locally with recorded test evidence.
**Partial** means identified implementation work remains within the task.
Upstream status is independent: not submitted, prepared, submitted or merged
(committed upstream). Preparing or waiting for review does not make a fix partial. Every runtime patch belongs to one
task; the patch inventory stays in Markdown while the UI keeps it out of the way.
Keep detailed logs in receipts. Overall, adjacent-task, future and archive links
connect the documents. Agents regenerate the snapshot after plan changes.

For a Coder UI button, see [Coder tracker integration](CODER-TRACKER.md).

Browsers normally restrict reading neighbouring files from a file:// page. The
standalone HTML therefore embeds a generated snapshot; the optional local server
reads the same Markdown live. No file-picker permissions or browser exceptions.

[Overall plan](../.agents/plan/current/README.md) · [Progress](../PROGRESS.md)

## Installed landing page

OLI opens `/project-tracker/`, a generated snapshot served by DDEV web. Prominent
links read `.ddev/.runtime-urls`; timestamped service status is separate from
runtime compatibility. Portable Git snapshots contain no installation metadata.
No dotenv file, credentials or complete DDEV response is embedded.

Use `python3 scripts/tracker.py --install` to refresh service status and publish.
Normal regeneration updates Markdown in an already managed installed page too.
`--serve` retains the live Markdown view; use `--port 8766` if 8765 is occupied.
Coder private routes/tunnels remain necessary; local URLs are not assumed to be
accessible outside the workspace.
