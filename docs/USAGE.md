# Install and use

## Status and prerequisites

The config/hook/service wiring is prepared; a complete One Line Installer run has
not been performed for this repository. The user deferred the broad installer test
matrix. Start with the smoke test below when returning to provisioning work.

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

Use the [official one-liner](https://project.pages.drupalcode.org/one_line_installer/guides/getting-started/)
from the **parent directory**, not the source checkout or an existing DDEV site:

```bash
bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust \
  --ref codex/portable-installer \
  --config drupalphprust \
  --name drupalphprust-site
```

If the CLI is already installed, the equivalent is:

```bash
drupalaibp https://github.com/jamieaa64/drupalphprust --ref codex/portable-installer --config drupalphprust --name drupalphprust-site
```

The selected branch must be published. The installer fetches its
`.drupalaibp/drupalphprust.json` and scripts; you do not need to clone this repo
first or use `scripts/install.py`. That script is an optional developer helper
for committed local-source experiments, not the project's installation interface.

The config uses `profile: template` and `site_template: drupal_cms_starter`.
The late host hook builds the runtime and enables the DDEV sidecar. Use a new
`--name` for each fresh attempt; the previous `drupalphprust-local` folder is a
partial installation. Run interactively so you can answer DDEV's prompts yourself.

For another runtime port, prefix the command with `PHPR_HTTP_PORT=18091`.
The port is saved in the managed service and survives restarts/rebuilds.
Both local and Coder freeform hosts use the same config. Optional environment
variable `DRUPALPHPRUST_TARGET=coder` records host context; it does not provision
Coder or require credentials. There is no upstream `--target` flag.

The installer can warn and continue after a failed hook, so verify explicitly:

```bash
cd drupalphprust-site
test -s .ddev/.runtime-ready
ddev describe
ddev exec -s phpr phpr --version
ddev logs -s phpr
curl --fail http://127.0.0.1:18090/user/login -o /tmp/phpr-login.html
```

Inspect response content too. The installer manages the disposable site's initial
credentials; never put passwords, cookies or database dumps in Git. For local-path
installer sources use a clean checkout: upstream copies the whole source directory,
including ignored files. The published Git-source command avoids that problem.

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

The regular DDEV URL uses Zend PHP. PHP Rust is `http://127.0.0.1:18090/` on the
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
