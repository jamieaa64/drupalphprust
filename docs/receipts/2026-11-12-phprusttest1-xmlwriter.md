# phprusttest1 XMLWriter reproduction — 2026-11-12

## Environment and scope

Explicitly selected installation: `phprusttest1`, this checkout. No database reset,
module removal, Drupal core change, authentication or push was performed.
Source HEAD: `baf794e`, branch `main`; installer-generated guidance changes were
already uncommitted and were preserved separately.

- DDEV 1.25.5; web, db, phpr and tracker reported OK after Docker socket access.
- Oracle: DDEV PHP 8.4.27, libxml 2.9.14.
- Candidate: `debian:bookworm-slim-phprusttest1-phpr-built`, image
  `sha256:8e0c9c68900e53ad361355c03a0ec457b0367d63302cd70891f5c6f196e555c1`.
- Composer lock SHA256:
  `80530d439d9a78720834b53bbc67f6b4fc4d6a6ba2911ad28142f58e936ade58`.
- Runtime source reconstructed from `72f688c3183b79858c01d250ce3999d86e143544`
  plus all 31 manifest patches; no runtime edits.

## Commands and results

`python3 scripts/prepare-runtime.py` passed integrity checks and applied the entire
series after retrying with network access. The initial sandbox denied network and
Docker socket access; the resulting initial DDEV stopped labels were not real service
state. `ddev describe` with socket access confirmed running services.

`curl -ksS --max-time 30 URL -o OUTPUT -w '%{http_code}\n'` returned:

| URL | Status | Observation |
| --- | --- | --- |
| `https://rust.phprusttest1.ddev.site/` | 500 | Class XMLWriter not found, Symfony ContainerBuilder.php:416 |
| `https://php.phprusttest1.ddev.site/` | 200 | HTTP availability only |
| `https://tracker.phprusttest1.ddev.site/` | 200 | HTTP availability only |

TLS certificate validation was skipped for these diagnostic requests; they do not
certify TLS or browser acceptance. Raw responses remain outside Git.

`ddev exec php /dev/stdin < tests/probes/xmlwriter-sitemap.php` exited 0 with empty
stderr. Its output is captured as EXPECT in `tests/phpt/xmlwriter-sitemap.phpt`.
`ddev exec -s phpr env PHPR_LOG=warn phpr /dev/stdin < tests/probes/xmlwriter-sitemap.php`
exited 255 with missing XMLWriter at the subclass declaration. This is an expected
failing regression fixture, not a passing runtime test; it is not yet added to the
passing combined runtime suite.

`ddev exec php -r 'echo PHP_VERSION, "\n", LIBXML_DOTTED_VERSION, "\n";'`
recorded oracle versions. `phpr --version` is unsupported by this candidate
(exit 1, tries to open an input file); image identity is recorded above instead.

## Diagnosis and next action

Simple Sitemap's `SitemapWriter` inherits XMLWriter and has its own constructor.
The required surface includes openMemory, setIndent, startDocument, writePI,
writeComment, startElement, writeAttribute, writeElement, endElement, endDocument
and outputMemory. The fixture exercises this independently of Drupal. Error-state
and argument-validation fixtures still need adding before implementation is complete.

Read upstream AGENTS.md, REGOLE.md, NEXT_SESSION_WORDPRESS.md, migration/RULEBOOK.md,
the latest available session (185) and its adversarial review. Serena is required
for Rust editing there but is unavailable in this session; the user has been asked
whether available code tools may be used. No substitute runtime stub was added.
Next: implement faithful libxml-backed writer support and inheritance dispatch,
obtain the required single adversarial review, export a discrete patch, rebuild,
then test the unchanged CMS. Full upstream gates, browser acceptance, the earlier
Fiber/Generator blocker and performance remain open.

One adversarial reviewer checked the fixture and evidence: no blocking findings.
The PHPT FILE matches the probe and EXPECT matches the captured oracle output.
Follow-ups: error/state fixtures, PHP 8.5.7 oracle validation, and a separate
runtime implementation review. This review does not establish Rust semantics or
upstream readiness.
