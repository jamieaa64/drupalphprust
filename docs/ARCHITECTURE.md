# Architecture and ownership

## Installation and runtime

```text
One Line Installer + this repository's .drupalaibp config
  -> DDEV creates Drupal CMS Starter with Composer/Drush on standard PHP
  -> source overlay supplies runtime patches, tools, docs and plans
  -> late host hook installs the bundled add-on; DDEV builds the project runtime image
       |-- web: nginx/PHP-FPM (standard PHP control, Composer and Drush)
       |-- db: MariaDB
       |-- phpr: PHP Rust development HTTP server + CLI
       |-- tracker: static tracker on tracker.<project-host>
       `-- serena: pinned Rust analysis tools + agent MCP connections
```

The root `.ddev/config.yaml` belongs to the installer/generated project and is
ignored. The tracked `.ddev/config.php-rust.yaml` is a small version overlay;
it does not replace project names, Coder routing or other generated settings.
The bundled `ddev-addon/` supplies services, host commands and a native
pre-delete hook (DDEV 1.25.5+). It is installed after Drupal scaffolding. DDEV
builds PHP Rust from `runtime/Dockerfile`, with a project-specific image tag and
Compose ownership labels. Standard PHP remains the default web service.
The static tracker has no PHP interpreter or database configuration and mounts
only generated tracker content. OLI reads its exact URL through `LAUNCH_PATH`.

The Serena developer service shares project source and has no public route.
Host agents connect over STDIO through DDEV; container agents use internal HTTP.
Installer choices configure Codex/Claude project settings and optionally install
their DDEV add-ons. See [agent setup](SERENA.md).

PHP Rust reads the same project volume and database as Zend. This is useful for
correctness diagnosis, but shared caches contaminate an independent performance
comparison. Use cloned seeds/isolated DDEV projects for measurements (TESTING.md).
The development server binds host loopback only; no public service is provisioned.

## Repository boundaries

- This repository owns the installer configuration, DDEV service integration,
  reproducible patch application, fixtures, comparison scripts and project plans.
- `francescotinti/php-rust` owns language/compiler/runtime fixes. This repository
  distributes patches against its pinned revision, not a vendored second runtime.
- `one_line_installer` owns installer behavior. Generic installer bugs should be
  isolated there; a project-specific hook belongs here. Its generated `drupalaibp`
  must not be edited directly: upstream changes go in `src/` and are regenerated.
- The Coder/DDEV template owns VM/container setup, Docker, project naming and
  authenticated routes. It should invoke the same installer/config, not fork its
  Drupal setup. No Terraform/template changes are made in this extraction.
- Drupal owns Drupal behavior. A PHP Rust failure is not a reason to modify Drupal
  core to mask missing language semantics. Explicit optional scope reductions are
  documented separately and applied to both comparison arms.

## Reproducibility

`runtime/manifest.json` pins upstream and SHA256 values. `runtime/series` records
application order, which is dependency order, not alphabetical order. Docker's
COPY/apply order must match. `prepare-runtime.py` reconstructs a fresh checkout
without the original local branches. PHP Rust-derived files retain upstream
licensing; see runtime/licenses and NOTICE.md.

Runtime pin does not pin Drupal. Store Composer lock, enabled-module list, theme,
DDEV/PHP/database versions, build profile, image ID, CPU architecture and seed hash
with each acceptance/benchmark receipt. Do not compare two fresh unpinned installs.
