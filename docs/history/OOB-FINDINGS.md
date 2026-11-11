> Historical investigation record imported on 2026-11-11. Paths and status below may be stale. Read ../../.agents/plan/current/README.md first.

# OOB Drupal comparison: working notes

## Environment

- Coder workspace `php-rust-migration`, separate clean project `/home/coder/drupal-oob`.
- Official Drupal CMS 2.2.8 Starter recipe, Drupal core 11.4.12, Mercury theme.
- Zend PHP 8.4.27, MariaDB 11.8, DDEV 1.25.5.
- Runtime upstream source pinned at `72f688c3183b79858c01d250ce3999d86e143544`.
- The original migration project and seed workspace are retained.
- `simple_sitemap` alone was uninstalled: XMLWriter is absent in PHP Rust.
- No OAuth, MCP or migration-agent acceptance requirements.

## Confirmed so far

- PHP Rust boots Drupal, builds its dependency container and reads configuration
  using the experimental PDO MySQL driver against MariaDB.
- Zend browser: homepage, login, dashboard, create and publish a Utility page,
  edit and save it, and render the updated body all passed on 2026-11-11.
- PHP Rust HTTP rendering is still under investigation. A successful bootstrap
  is not a successful browser workflow.

## Runtime corrections isolated locally

PDO MySQL, recursive callback/filter/filesystem iterators, nested trait autoload
case, diamond trait reuse, private-property unserialization, builtin object
protocol dispatch, and deferred arguments in object-method callbacks each have
focused fixtures and separate local contribution branches. Callable class
autoloading is the next candidate under validation.

## Known limits outside the current fixes

- The driver targets the tested MariaDB subset, not complete PDO MySQL parity;
  see DATABASE-FINDINGS.md for restrictions and explicit rejections.
- Countable objects with an explicit second `count()` mode argument still fail.
- `Closure::fromCallable()` invalid-callable errors and exceptions thrown by an
  autoloader do not yet match Zend's TypeError wrapping. The autoload candidate
  tests successful class loading, not full error-message parity.
- These are experimental compatibility patches, not upstream release pins.
  The upstream full corpus/performance promotion gate has not been run.

## Measurements

No validated PHP Rust HTTP performance result yet. `benchmark-http.py` records
interleaved requests, validates status and required page content, and retains raw
samples. It measures the whole HTTP stack, not isolated interpreter throughput.

Latest regression run: the four database parity suites, explicit driver safety
restrictions, recursive iterator suite, and seven focused runtime fixtures pass.
Cross-file reference-return binding subsequently passed its focused fixture and
advanced rendering to Fiber suspension. Dynamic Fiber dispatch is being tested.

The adversarial review identified a same-unit namespace fallback aliasing edge
case and missing native Fiber argument validation. Both have source corrections
and new fixtures queued for the next build; neither is counted as verified yet.

The Fiber dispatch fixture now passes pause/resume through static, dynamic,
spread, named and instance calls, including argument-error classes. Drupal then
exposed a panic when suspension crosses `call_user_func_array`'s synchronous host
callback bridge. A frame-based trampoline candidate is under validation.

The callback trampoline fixture passes direct, dynamic, spread and namespaced
calls, reference arguments and reference returns. Suspension through other
synchronous host callbacks (for example an outer `array_map`) remains unsupported.
Rendering advanced to HTML5 DOM document/namespace construction; those additions
and Stringable replacement-map handling are now under validation. An HTTP 200
containing warnings or a fatal error is explicitly not a successful page.

DOM namespace additions currently support explicit prefixes without rebinding,
plus element default namespaces. Automatic attribute-prefix generation and
rebinding an existing prefix are explicitly rejected; they must not silently
change the namespace of existing attributes. The underlying DOM implementation
has other pre-existing limitations and is not advertised as full ext/dom parity.

## 2026-11-11 continuation: static references and render-array concatenation

- `NestedArray::unsetValue()` failed compiling `$ref = &self::getValue(...)`.
  The compiler now reuses static dispatch followed by checked reference binding.
  `probes/static-reference.php` matches Zend on mutation through the returned
  alias, self/parent/static/dynamic calls, named/spread value arguments, and the
  notice/copy behavior of a non-reference return. A standalone PHPT is included.
- Broader probes exposed pre-existing gaps: direct static-property reference
  arguments/returns, and reference input arguments through named/spread calls.
  These are not claimed fixed by the static return-binding change.
- Automatic request-triggered cron is disabled (`automated_cron.settings interval
  0`) for this disposable comparison site. Canvas's cron health check currently
  encounters a schema callback returning null instead of bool. Foreground page
  verification does not establish cron compatibility.
- Minimal Twig rendering, Markup serialization round trips, and a Fiber render
  pass on both engines. Full HTML remained malformed even after truncating the
  response caches: render-array `.=` used a separate path that omitted
  `__toString`. A field-concatenation fix and expanded regressions are in progress.
- An HTTP 200 containing Markup class names is explicitly a failed page check.
- The first field-concatenation approach was rejected during review because it
  broke ArrayAccess reads. The replacement keeps the original path walker and
  defers Stringable conversion until storage borrows end. A weak destination
  handle preserves Zend behavior if conversion copies or replaces the root;
  singleton internal references remain invisible to ReflectionReference.
  `field-concat` now passes the bilateral regression, including ArrayAccess.
- Chromium exposed the next real component errors: nested writes discarded
  reference-returning `offsetGet()` results (including WeakMap), and reflection
  failed for builtin closures used by Twig. Fixes are under validation. Builtin
  reflection metadata is explicitly limited to abs/json_encode/max/min/range/
  is_iterable, exported with the installed PHP 8.4.8 oracle; it does not claim
  complete builtin reflection coverage.
- Nested ArrayAccess regression now includes expression results and the
  distinction between WeakMap's public value-returning method and its dimension
  access. Both match Zend. All six Twig builtin callbacks now reflect correctly
  through Drupal's actual Twig service.
- Current remaining foreground warnings include forwarded by-reference method
  returns, `touch()` of Twig cache directories, empty-name class autoload, and
  a Twig documentation-target index warning. The simple recursive property-stack
  probe matches Zend; the last warning needs a fuller reproduction.
- Browser navigation exposes a separate CLI-server issue: an idle speculative
  TCP connection blocks ready requests for its 60-second read timeout. The
  candidate polls accepted sockets for readiness, retaining sequential PHP
  execution. A dedicated idle-preconnect regression is included.

### Browser login continuation

- Forwarded return references, directory touch, empty-name autoload, object-var
  snapshots and stream constants have focused Zend/Rust passing probes. The
  expanded suite still needs a final run against the combined candidate.
- Idle-preconnect HTTP regression passes. The server still processes PHP
  sequentially and is not claimed hardened against partial HTTP requests.
- The HTML scanner's bounded ASCII/Unicode/NUL probe matches Zend. A diagnostic
  instruction budget interrupted a large HTML parse but did not prove an
  infinite loop. Repeated session warnings expanded rendered pages substantially.
- Login POST persisted an authenticated database session, but its redirect was
  anonymous: `session_regenerate_id()` did not emit the new session cookie.
  `test-session-regenerate.py` reproduces this on the old candidate without
  printing cookie values. A narrowly separated cookie-renewal patch is building.
- Optional Klaro consent processing and CSS/JS aggregation are being disabled
  on the shared test site to focus on authoring. Known excluded gaps are DOM
  XPath expressions used by Klaro and `file_put_contents()` to registered
  userland stream wrappers. These exclusions apply to both comparison engines;
  they must not be described as full default Drupal CMS compatibility.
- Session renewal now passes the HTTP regression on Zend and Rust: two rotations
  in one response, resumption, existing-cookie rotation, cookie flags, unrelated
  cookies, and cookies disabled. Login now reaches authenticated routes.
- Clean homepage and login form render without warnings after the scoped config
  changes. Dashboard then failed Twig tokenization after non-ASCII text: byte-mode
  preg_match passed a raw byte offset into its Latin1-expanded engine subject.
  Offset translation fixes the isolated Twig cases and regex regression.
- Page creation exposed a separate deferred argument read bug: a reference-returning
  magic getter left the next property receiver wrapped, bypassing __get and hooks.
  A minimal chained-property method-argument reproducer is now included; its
  receiver-dereference correction is under validation.
