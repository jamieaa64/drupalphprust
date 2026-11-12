# Portable installer and separate checkout — 2026-11-12

## Prepared

- New independent Git checkout: `/Users/jamesabrahams/code/ai/drupalphprust`.
  Branch `codex/portable-installer`; origin is the public GitHub repository via SSH.
  Preserves the previous runtime commits without ignored build/site/credential data.
- `scripts/install.py --target local|coder --destination NEW_FOLDER` invokes the
  existing One Line Installer config using a temporary clean clone of committed
  source. Neither target needs Coder tooling on the build host. Coder target
  prints laptop tunnel instructions; workspace creation/routing remains separate.
- Local source must be clean. Source/destination nesting and existing destinations
  are rejected. Selected runtime port and host target survive service rebuilds.
- No API keys are required. Authentication remains user-managed outside Git.

## Checks

- Seven focused tests pass using real temporary Git repositories and stubbed
  Docker/DDEV/installer commands: local and Coder modes, private-file exclusion,
  failed hook marker, existing destination, dirty source, invalid port, persistent
  port and unowned service-file protection.
- Repository contract/hash checks pass for all 31 runtime patches.
- Reachable Git history and tracked files scanned for private artifact paths and
  common private-key/GitHub/OpenAI/AWS token patterns: no matches. This heuristic
  scan is not a mathematical guarantee against every possible secret format.
- Actual host: DDEV v1.25.0, Docker 29.2.1, arm64 macOS.
- Official released installer downloaded to a temporary file from
  `https://project.pages.drupalcode.org/one_line_installer/drupalaibp`.
  Source contract also inspected at One Line Installer commit
  `abb8deb61a4c8f7f710d3c1a55c6847b7523dd97`.

## Real local smoke: prerequisite blocked

Attempted from source:

```bash
python3 scripts/install.py --target local --destination ../drupalphprust-local --runtime-port 18091 --installer /tmp/drupalphprust-installer --yes
```

The installer reached initial DDEV startup, then exited 1 because
`drupalphprust-local.ddev.site` did not resolve and DDEV could not elevate its
hostname helper. No Drupal installation or runtime build completed in that site.
The partial generated folder is preserved. Existing projects were not stopped or
changed. The user was asked to run DDEV's displayed hostname command themselves,
per their authentication rule. Never request the user's password.

After that prerequisite: inspect/preserve the partial folder and rerun a clean
installation. Do not claim this local install or a new Coder freeform install
passed based on the stubbed launcher tests. Full Drupal rendering remains blocked
by the separately documented Fiber/Generator issue.

## Handoff

The user pushes `codex/portable-installer` when ready. A Coder workspace can clone
that branch and run the same launcher with `--target coder`; no new template is
needed. Use private `coder port-forward` access. See USAGE.md and CODER.md.
