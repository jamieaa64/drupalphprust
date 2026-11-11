# Install and use

## Status and prerequisites

The config/hook/service wiring is prepared; a complete One Line Installer run has
not been performed for this repository. The user deferred the broad installer test
matrix. Start with the smoke test below when returning to provisioning work.

Host requirements: working DDEV/Docker, Git, Bash and Python 3; One Line Installer
2.x with config-source and late-hook support. No Rust toolchain is needed on the
host because Docker builds it. Allow several minutes and enough disk/CPU for a
Rust build. Coder freeform supplies the host; see CODER.md.

## New site through the One Line Installer

Install the CLI using its [official guide](https://project.pages.drupalcode.org/one_line_installer/guides/getting-started/).
Run from the **parent directory** where a new disposable project should be created:

```bash
drupalaibp https://github.com/jamieaa64/drupalphprust \
  --config drupalphprust --name drupalphprust
cd drupalphprust
```

Before a prepared branch is merged into main, add `--ref <published-branch>`.
Do not use a branch/ref that has not been pushed. Authentication, when needed,
is performed by the user in their terminal.

For local-source development before publishing, use a clean source checkout and
another destination parent directory (not a child of the source):

```bash
# From the destination parent; replace this example path with your source checkout.
drupalaibp /absolute/path/to/drupalphprust \
  --config drupalphprust --name drupalphprust-test
cd drupalphprust-test
```

The inspected installer source overlays the whole source directory into the new
project after Composer scaffolding. Use a clean checkout: a local source with
`.work`, vendor, logs or private artifacts could copy those too. The config uses
`profile: template` with `site_template: drupal_cms_starter`; it does not use the
old migration menu or canvas-storybook-ai configs.

The late host hook builds `drupalphprust:dev-release`, installs the service and
restarts DDEV. In the inspected installer version, hook failures are warnings and
the pipeline continues. **Installer exit 0 is insufficient.** Check:

```bash
test -s .ddev/.runtime-ready
ddev describe
ddev exec -s phpr phpr --version
ddev logs -s phpr
curl --fail http://127.0.0.1:18090/user/login -o /tmp/phpr-login.html
```

Inspect page content/browser results; a curl success can still contain PHP errors.
The installer manages its initial account. Keep the disposable site private and
set your own credentials before exposing it. Do not put them in receipts or Git.

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
machine running Docker, not necessarily your laptop. For another port, export
`PHPR_HTTP_PORT=18091` **before every DDEV start/restart** for that project. Set it
in the appropriate DDEV compose environment file only after verifying your DDEV
version's interpolation behavior. Different projects must use different ports.
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
