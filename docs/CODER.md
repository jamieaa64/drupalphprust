# Optional Coder freeform host

Coder supplies a machine with Docker and DDEV. The source and local installation
have no Coder dependency. Both hosts use the same One Line Installer config
fetched directly from this repository and branch.
No API keys, workspace tokens, Terraform state or new Coder template are needed
in this repository. Coder authentication stays in the user's local CLI store.

## Create or select a workspace

Use an existing **DDEV freeform** workspace or create a fresh one in your Coder
UI. It must provide Docker access, DDEV, Git, Python 3, Bash, the installer and
enough disk/CPU for a Rust build. Workspace creation and provider login are
separate from project installation. No workspace name or server is hardcoded.

If authentication is needed, run these yourself on your laptop:

```bash
coder login YOUR_CODER_URL
coder ssh WORKSPACE
```

Inside that workspace, from a parent directory:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust --config coder
```

Or omit `--config coder` and choose Coder in the installer's Local/Coder picker.
The normal local command uses the same repository URL, with Local selected by
default. Publish changes to `main` first; `--ref` is only for branch testing.
The installer reads `.drupalaibp/coder.json` and creates the project using DDEV.
No pre-clone or custom launcher is required.

The repo is public, so cloning needs no GitHub API key. A private fork can use
user-managed Git authentication; never add credentials to the remote URL.

## Access the site privately

The named `php.<project-host>` and `rust.<project-host>` routes are served by the
DDEV router on the workspace machine. They are not automatically public Coder app
URLs. The Rust service also retains a direct workspace-loopback port for private
tunnelling. On your laptop:

```bash
coder port-forward WORKSPACE --tcp 127.0.0.1:18090:18090
```

Keep the tunnel running and visit `http://127.0.0.1:18090/`. Choose a different
local port if occupied; prefix the installer with `PHPR_HTTP_PORT=18091` if the remote
port is occupied. This route needs no template modification or public exposure.

For the normal Zend site run `ddev describe` in the installed project and forward
its direct HTTP port in the same way. If your existing template supplies named
DDEV routes, follow its instructions (`ddev coder-setup` may be available). This
is optional and is not invoked by our portable installer.

## Work locally, update remotely

Use Git for source changes. The user performs pushes/authentication:

```bash
# Local source checkout, after reviewing and committing changes:
git push -u origin HEAD
```

Then inside the Coder workspace's source checkout:

```bash
git fetch origin
git switch YOUR_BRANCH
git pull --ff-only
```

For a fresh reproducible site run the One Line Installer with a new `--name`. For
an existing disposable installed site, first ensure its tracked working tree is
clean, then fetch/switch/pull there and rebuild:

```bash
bash scripts/build-runtime.sh dev-release
bash scripts/enable-runtime.sh dev-release
```

Installer-managed edits to AGENTS.md or other files may make the installed tree
dirty. Inspect them; never use reset/clean to force an update. Do not rerun the
fresh-site installer into an existing destination. Database/content changes and
Composer lock differences require a separate deliberate sync/restore plan.

## Acceptance and boundaries

Check both services, login response content, restart persistence and private
access. A local install does not prove Coder routing or full Drupal compatibility.
The earlier experiment used a standalone server container on an old workspace;
those receipts do not certify this DDEV service or this freeform setup.

For the proposed private workspace button and server lifecycle work, see
[Tracker in Coder](CODER-TRACKER.md). This integration is not deployed yet.
