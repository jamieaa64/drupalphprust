# What could PHP Rust change for Drupal?

The study should explain where PHP Rust is useful, where standard PHP is better,
and what would need to change to unlock something different. A faster homepage
alone is not the goal. This is an experiment design, not a list of proven benefits.
No security, cost, async or agent-productivity advantage has been demonstrated.

[Comparison task](../.agents/plan/current/12-benchmark.md) ·
[Now versus changes task](../.agents/plan/current/14-drupal-opportunities.md) ·
[Execution and baseline controls](TESTING.md) · [Overall plan](../.agents/plan/current/README.md)

## Two questions, kept separate

1. **What is better or worse now?** Use unchanged Drupal and equivalent enabled
   features on both runtimes. Runtime bugs and setup friction count as costs.
2. **What could become better with changes?** Label the required runtime, Drupal,
   library or infrastructure changes. Prototype them separately; do not attribute
   a redesigned application's results to the runtime alone.

Keep an untouched standard Drupal CMS control for feature coverage and a matched
scoped control for fair comparisons. Reduced functionality is not an efficiency
win. The existing HTTP script is one starting instrument, not the whole study.

## Experiment menu

### Useful work under constraints

Go beyond page latency: measure successful concurrent sessions, CPU time per
completed operation, peak/steady memory, startup and idle resource use. Try a
small-memory developer machine, several isolated sites, and a burst after idle.
Find where each configuration fails or degrades; report errors and tail latency.
Include database writes and rendered-content validation, not just HTTP status.

Question: can the same resources support more useful work, or does compatibility,
server behavior or memory retention erase any apparent advantage?

### Developer and operating cost

Measure clean install/build, cached rebuild, clean-seed restore, restart and time
to diagnose an injected fault. Count downloads, disk use, manual steps, patch
maintenance and recovery effort as well as CPU/RAM. Distinguish developer cost
from hosting cost: an expensive build may be amortised across many requests.

Estimate cost per verified workload and per idle/active site-hour using explicit
resource quantities. If converting to money, record provider, region, date and
prices when running the experiment. Include sensitivity to traffic and utilisation;
do not invent a single universal monthly saving.

### Security boundaries and resilience

First map the compared stacks: runtime, server, extensions, dependencies, filesystem
permissions and process/network isolation. Rust implementation alone does not prove
a safer Drupal application. Separate memory-safety hypotheses from application
vulnerabilities, dependency risk and operational configuration.

On disposable local fixtures, compare malformed requests, request-size limits,
slow/partial connections, error disclosure, resource exhaustion and cross-request
state leakage. Record whether failures stay contained, cause a crash, expose data,
or disrupt unrelated work. Also review update/provenance and patch-maintenance
processes. These are bounded observations, not a security audit or vulnerability
count contest; do not test third-party services.

### Ease of working with coding agents

Use the same model/version, task prompt, tools, time/token budget and clean starting
state on both stacks. Try a small Drupal change, a broken service diagnosis, a
runtime regression reduction and creating a fresh disposable site. No migration
agent or MCP integration is required for this study.

Measure verified completion rate, time/tokens, human interventions, reproducibility,
quality of error messages and whether another agent can resume from the repository.
Repeat tasks with fresh contexts and vary run order to reduce familiarity effects.
Include developer tooling compatibility and documentation gaps. Score output with
an independent acceptance check rather than the agent's own success statement.

### Behavior under waiting and long-lived work

Use a controlled local dependency with known delays/failures to study independent
outbound calls, background jobs and concurrent requests. Measure throughput,
resource use, fairness and recovery alongside correctness. Fiber support alone
is not proof of an async scheduler, nonblocking I/O or safe parallel rendering.
Keep server differences explicit: nginx/FPM versus a native server is a whole-stack
comparison unless a separate experiment isolates the runtime.

## Opportunity register: today versus redesigned Drupal

Create one short record per idea with: problem, current baseline, proposed change,
owner, dependencies, prediction, smallest falsifying experiment, risks and result.
Assign one of these categories before measuring:

- **Available today:** no Drupal changes; verify actual support on the pinned builds.
- **Runtime change:** PHP Rust work only, preserving Drupal/PHP semantics.
- **Drupal change:** application/core/contrib/library changes with a standard-PHP path.
- **Combined change:** requires both; isolate their effects where possible.

Initial ideas to investigate, not promised capabilities:

- Concurrent independent external I/O: do relevant clients and schedulers support
  it, and does batching help both runtimes equally? Check timeout/cancellation and
  deterministic output before measuring reduced waiting.
- Async rendering or background work: identify true dependency boundaries; preserve
  access checks, cache metadata, transaction ordering and exception semantics.
- Persistent workers: explore startup amortisation against memory growth, service
  reset requirements and session/user-state leakage across requests.
- More structured diagnostics and reproducible environments for agents: compare
  current tooling first, then a small diagnostic or setup improvement on each stack.

For feasible ideas compare unchanged Zend, unchanged PHP Rust, modified Zend and
modified PHP Rust. If an arm is unsupported, explain why rather than awarding it
zero or crediting a hypothetical improvement. Include implementation/maintenance
cost, portability and upstream appetite in the recommendation. Keep architectural
proposals separate from fixes needed for existing PHP behavior.

## Deliverables and decision gates

Start with experiment specifications while runtime work continues. Focused probes
can run early if labelled as probes; do not generalise them to Drupal outcomes.
After correctness, build a runner that captures versions, configuration, commands,
resource limits, seeded data identity and sanitised evidence. Use separate restored
DB/cache/files for each arm, warm independently, repeat runs and vary order.

Each experiment report should fit on one page: question, prediction, baseline,
change, correctness checks, measurements, uncertainty, result and next decision.
Use verdicts **better**, **worse**, **no material difference**, **inconclusive** or
**unsupported** with an explicit metric and workload. Keep larger raw results out
of the short tracker. Results may support a tradeoff rather than an overall winner.

Produce a final capability/tradeoff matrix and a prioritised opportunity register.
Choose a few high-value experiments from the menu rather than claiming exhaustive
coverage. Publish no composite score until its weights and purpose are justified.
