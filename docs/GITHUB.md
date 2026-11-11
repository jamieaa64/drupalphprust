# GitHub handoff

Destination: https://github.com/jamieaa64/drupalphprust (public, empty when inspected
2026-11-11). This extraction is prepared on a local `codex/standalone-ddev-project`
branch. No push or authentication is performed by the agent.

After reviewing the local commit, the user can populate the empty repository's main:

```bash
cd /absolute/path/to/drupalphprust
git status --short
git log -1 --stat
git push -u origin HEAD:main
```

Use the actual checkout path provided in the handoff. If the remote has acquired
commits since extraction, fetch/review and integrate them first; do not force-push.
Alternatively publish the prepared branch for review using:

```bash
git push -u origin codex/standalone-ddev-project
```

In that case installer trials must name `--ref codex/standalone-ddev-project` until
main contains the config. The user performs any GitHub login in their own terminal.
Publishing this project is distinct from submitting patches to PHP Rust upstream.

Once published, another agent can clone the repository, read AGENTS.md, inspect
.agents/plan/current/README.md and reproduce runtime source with the provided script.
Do not require the previous local directories, chat or Coder credentials to begin.
