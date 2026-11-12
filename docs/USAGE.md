# Install and use

## Status and prerequisites

A local installation completed on 12 November 2026. Updated tracker launch and
completion stages were verified separately; a fresh install of the updated config
and Coder acceptance remain untested. See [receipt](receipts/2026-11-12-tracker-landing.md).

Host requirements: working DDEV/Docker, Git, Bash and Python 3; One Line Installer
2.x with config-source and late-hook support. No Rust toolchain is needed on the
host because Docker builds it. Allow several minutes and enough disk/CPU for a
Rust build. Coder freeform supplies the host; see CODER.md.

## Separate source and installed projects

Keep a source checkout and disposable sites as siblings, for example:

```text
~/code/ai/drupalphprust/          # Git source, patches, plans, docs
~/code/ai/drupalphprust-local/    # Generated Drupal/DDEV site
```

The source checkout needs no Coder client, credentials, API keys or sibling
repositories. The installed site contains generated Drupal files and its database;
those are not the portable source. Do not move databases through Git.

## Run the One Line Installer directly

From a **parent directory**, not an existing DDEV site:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust
```

The installer fetches the repository's default branch. Its configuration picker
shows **Local (default)** first and **Coder** second. Choose Local (or press Enter)
for your laptop. It asks for a new project name; no `--name`, `--config` or `--ref`
is needed for normal local installation. DDEV handles its usual setup prompts,
including administrator authentication in your terminal when needed.

For Coder, run inside an existing DDEV freeform workspace and either choose Coder
or append `--config coder`. This is the installer's existing config argument,
not an invented `--coder` flag. No new Coder template is required.

The two configs share the same build and hooks. They pre-answer the host choice
as `DRUPALPHPRUST_TARGET=local` or `coder`; the installer saves that non-secret
answer in ignored `.ddev/.env`. The host hook reads only that key, without sourcing
or exposing other environment values. `main` must contain these changes before
the default URL command uses them; `--ref BRANCH` remains optional for development.

For unattended installation use `--yolo --config drupalphprust` locally or
`--yolo --config coder` on Coder: the upstream installer requires a selector when
multiple configs are present in unattended mode. The normal interactive command
above needs no selector.

The late hook builds the runtime and enables the DDEV sidecar. Since upstream
can continue after hook failure, verify in the generated project:

```bash
test -s .ddev/.runtime-ready
ddev describe
ddev exec -s phpr phpr --version
ddev logs -s phpr
```

Installation still does not prove full Drupal rendering compatibility. Use a new
project name for retries: `drupalphprust-local` is a preserved partial attempt.
If another site occupies port 18090, prefix the installer with
`PHPR_HTTP_PORT=18091`; the selected port persists across restarts and rebuilds.

The optional Python developer helper is not part of this default workflow. Raw
upstream local-path sources copy the whole source folder, including ignored files;
use a clean checkout for those. Git URL installs fetch committed source only.

## Host prerequisite failures

If DDEV reports an unresolved `NAME.ddev.site` hostname and asks for administrator
privileges, enter your password directly in your interactive terminal when DDEV
requests it. A separate hostname command is not the normal installation workflow.
Do not give an agent your password. A failed fresh install may leave a partial
folder: inspect and preserve it before choosing a new destination or retrying.
The launcher never deletes or resets an existing directory to force a retry.

## Rebuild or recover a failed hook

From the installed project, after checking the error:

```bash
bash scripts/build-runtime.sh dev-release
bash scripts/enable-runtime.sh dev-release
# Or rerun the complete late hook, which also updates its completion marker:
bash .drupalaibp/setup-runtime.sh
```

The hook's working directory must be the installed project root. It uses Docker
on the host and DDEV for services; it does not run inside the PHP web container.
The source config's `./setup-runtime.sh` path is relative to `.drupalaibp/`; the
installer stages that hook into the generated project root. No `../` copy_paths
are needed: the source overlay supplies the repository.

## Day-to-day commands

```bash
ddev start
ddev drush status                 # standard PHP tooling
ddev phpr --version               # PHP Rust CLI in the sidecar
ddev logs -s phpr
ddev stop
```

The installer prints two named URLs and saves them in `.ddev/.runtime-urls`:
`https://php.<project-host>/` runs standard PHP, and `https://rust.<project-host>/`
runs PHP Rust. It derives the host and scheme from DDEV, rather than the folder
name. The original DDEV URL continues to use Zend. These are separate service
routes, not two names for the standard PHP web container.

The direct PHP Rust fallback remains `http://127.0.0.1:18090/` on the
machine running Docker, not necessarily your laptop. Choose another port with launcher `--runtime-port 18091`. It is written into the
managed service, so ordinary DDEV starts need no exported variables. To change it
on an existing installed site, run `PHPR_HTTP_PORT=18091 bash scripts/enable-runtime.sh dev-release`.
Different projects on the same Docker host must use different ports.
Use separate browser contexts for control/candidate to avoid shared-host cookies.

To disable the experiment, inspect and remove only the managed
`.ddev/docker-compose.phpr.yaml`, then `ddev restart`; the normal web/db services
remain. Do not delete volumes or databases. No automatic cleanup command is supplied.

## Scoped compatibility experiment

The existing evidence comes from a site with these explicit reductions. On a
new **disposable** site, apply them only if reproducing that scoped baseline:

```bash
ddev drush pm:uninstall simple_sitemap klaro -y
ddev drush config:set system.performance css.preprocess 0 -y
ddev drush config:set system.performance js.preprocess 0 -y
ddev drush config:set automated_cron.settings interval 0 -y
ddev drush cache:rebuild
```

Check module availability/dependencies first; do not force uninstall dependent
features. Record changes for both engines. These are not automatically applied
by the installer. Keep a normal, untouched Zend CMS control for reporting what has
been excluded; compare performance only between equivalently configured sites.

## Release and measurement

Only after correctness passes:

```bash
bash scripts/build-runtime.sh release
bash scripts/enable-runtime.sh release
```

This changes the runtime image, not the application's Composer lock or database.
Follow TESTING.md for cache/seed isolation and repeatable results. The included
benchmark is sequential HTTP latency tooling, not a complete load-testing system.

## Named-route checks

After runtime setup, read `.ddev/.runtime-urls` and open both URLs. Confirm the
Rust hostname appears only on the phpr service in `ddev utility compose-config`.
Do not add it to `additional_hostnames`, which would also route it to standard PHP.
The web service retains existing DDEV aliases and adds the labelled PHP alias.
Custom hostnames without wildcard DNS may need normal DDEV/local hostname setup.
Check TLS and Drupal trusted-host settings on the installed site. Use separate
browser contexts for the two runtimes; application cookie settings may span hosts.

Routing uses DDEV's documented [custom service hostnames](https://docs.ddev.com/en/stable/users/extend/custom-compose-files/).

## Tracker landing page

OLI opens `/project-tracker/` on the standard-PHP DDEV host. The final message
lists the tracker and both runtime sites using actual installation metadata.
`python3 scripts/tracker.py --install` publishes the page and refreshes timestamped
service status; `--message` prints the three URLs. Normal tracker regeneration
also refreshes Markdown in any managed installed copy. An unrelated existing
`web/project-tracker` directory is never overwritten.

The pre-launch hook registers unresolved local runtime hostnames through
`ddev hostname`, inheriting the installer terminal for any user authentication.
Already resolving names are skipped. Coder keeps its existing private routes and
tunnels. A failed or cancelled registration is shown in the tracker with a retry
command; it does not suppress the tracker page.

To retry, run `python3 scripts/register-runtime-hostnames.py`, then
`python3 scripts/tracker.py --install`. Alternatively, the user can run
`ddev hostname HOST 127.0.0.1` for each hostname in `.ddev/.runtime-urls`.
DDEV may request an administrator password. Do not add the Rust hostname to
`additional_hostnames`, which would route it to standard PHP.
