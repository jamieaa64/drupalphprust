# Local and Coder agents through GitHub PRs

Use `jamieaa64/drupalphprust` for project PRs targeting `main`. These PRs are
separate from PHP Rust upstream submissions, which still need explicit approval.
The user handles GitHub authentication, pushes and merge decisions. Agents may
prepare commits, review diffs and write PR descriptions without publishing them.

## Assign work before editing

Give each agent one numbered tracker task and one `codex/<topic>` branch, with
its own checkout. Local and Coder installations have separate DDEV projects,
databases and `.work/php-rust` trees. Do not edit a shared runtime tree or change
another agent's active branch. Start new work from the latest fetched `origin/main`;
record its base commit and intended task in the PR description.

If two tasks touch the same runtime patch or shared manifest/series/Dockerfile,
sequence them. Merge one first, then update the other branch and reconstruct its
runtime. A clean Git merge alone does not prove patch ordering or PHP behaviour.
For deliberate dependent PRs, name the dependency and base the second on the first
branch; retarget and revalidate after it merges. Never force-push another agent's
branch. Do not run destructive cleanup to hide installed/generated changes.

## Prepare and publish a reviewable change

1. Read AGENTS.md, the current plan and handover. Claim a specific task in the
   handoff/PR before starting; a private chat is not shared coordination state.
2. Create a topic branch in your checkout. Commit only intended source, fixtures,
   receipts and tracker updates. Keep site data, local agent settings and logs out.
3. Follow the task's required tests and runtime adversarial review. Use the PR
   template to state exact results and remaining failures; do not describe known
   failures as a passing suite.
4. Give the user these commands, substituting the prepared branch name:

```bash
git push -u origin codex/YOUR_TOPIC
gh pr create --repo jamieaa64/drupalphprust --base main \
  --head codex/YOUR_TOPIC --draft
```

The user authenticates if required and submits the prepared description. Agents
must not run these publication commands under the existing project rules.

5. Review the PR diff and checks. Another agent may review it in a separate
   checkout, without switching the author's branch or altering its database.
6. Merge only after user approval. Then update the other checkout from main with
   a clean tree (`git fetch origin` and `git pull --ff-only` on main). Preserve
   uncommitted installer changes; resolve them deliberately before switching.
7. Reconstruct/rebuild and verify the affected runtime on the receiving host.
   Git transfers code, not Docker images, installed databases or browser sessions.

## Checks and evidence

The existing `Repository contracts` workflow runs on pushes and pull requests.
It validates patch order/hashes, repository structure, secret heuristics,
installer/tracker tests and generated tracker consistency. Serena setup isolation
tests also run in CI. These offline checks do not run the Rust release suite,
install Drupal or certify Coder routing. Attach those results in a safe receipt
when the task requires them.

Branch protection and required-review settings have not been inspected or changed.
Do not assume a green GitHub check or this document enforces merge approval.

## Suggested Coder handoff

Start with task 17 (release failures) only if no other agent owns it. Use
`codex/release-gates` from current main, connect the project Serena MCP, read the
upstream process, and keep one adversarial reviewer. The local dashboard/PR
workflow change is separate. Record the base SHA and task ownership in the PR;
do not start overlapping runtime patches concurrently.
