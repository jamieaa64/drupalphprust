# Homepage font request stalls PHP Rust

Selected site: `phprusttest1`, existing XMLWriter candidate, serving image
`debian:bookworm-slim-phprusttest1-phpr-built`. No runtime source or Drupal
configuration changes were made during this diagnosis.

The prior homepage hero observation does not establish complete browser loading.
On 12 November, the Rust route timed out after eight seconds with no bytes even
with `curl --resolve rust.phprusttest1.ddev.site:443:127.0.0.1`. Zend returned 200.
The Rust process was sleeping in `hrtimer_nanosleep`, with an open idle database
connection and no semaphore rows. This does not establish a partial-HTTP-request
cause; task 15 remains independent.

After `docker restart ddev-phprusttest1-phpr` and listener readiness, the homepage
returned 200 in 1.216 seconds. Its font preload URLs were `/fonts/Outfit/latin.woff2`
and `/fonts/Inter/InterVariable.woff2`. The same Zend homepage includes the correct
`/themes/contrib/mercury` prefix. The Twig preload template uses `mercury_path`.
The exact runtime cause of the missing prefix remains unconfirmed.

Sequential curl checks returned 200 for the favicon, aggregated CSS, fonts.css,
main.min.css and theme.css. The next request reproduced the stall:

```bash
curl --max-time 4 -o /dev/null -w '%{http_code}\n' \
  https://rust.phprusttest1.ddev.site/fonts/Outfit/latin.woff2
```

Result: timeout, zero bytes, HTTP 000; server log ends at `Accepted`. Standard PHP
returned 404 for the identical missing path in 0.552 seconds. The Rust service was
restarted again to clear the stalled request. Restart is recovery, not a fix:
a browser can trigger the bad font request again.

Next: minimise the missing Twig variable/prefix difference (task 06), and locate
the sleep loop in the missing-path request (runtime completion task 01). Test
both valid and missing assets, then recheck full browser loading. Do not alter
Drupal templates or add font aliases to conceal the runtime differences.

Follow-up: [font-prefix fix and remaining blockers](2026-11-12-callable-unpack-reference.md).
