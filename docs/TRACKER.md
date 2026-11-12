# Small progress tracker

Open **tracker.html** in a browser or the site's preview window. It is a standalone
snapshot with no external libraries, login or network dependency. Select a step
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

Edit PROGRESS.md for the short summary. Edit `.agents/plan/current/01-*.md` through
`10-*.md` for status, work and tests. Allowed statuses: done, active, blocked,
pending. Keep detailed logs in receipts. The overall index and adjacent-step
links connect the documents; future/archive folders remain in place.

Browsers normally restrict reading neighbouring files from a file:// page. The
standalone HTML therefore embeds a generated snapshot; the optional local server
reads the same Markdown live. No file-picker permissions or browser exceptions.

[Overall plan](../.agents/plan/current/README.md) · [Progress](../PROGRESS.md)
