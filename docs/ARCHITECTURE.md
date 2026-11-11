# Architecture and ownership

## Installation and runtime

```text
One Line Installer + this repository's .drupalaibp config
  -> DDEV creates Drupal CMS Starter with Composer/Drush on standard PHP
  -> source overlay supplies runtime patches, tools, docs and plans
  -> late host hook builds the pinned runtime image and enables DDEV phpr service
       |-- web: nginx/PHP-FPM (standard PHP control, Composer and Drush)
       |-- db: MariaDB
       `-- phpr: PHP Rust development HTTP server + CLI
```

The root `.ddev/config.yaml` belongs to the installer/generated project and is
ignored. The tracked `.ddev/config.php-rust.yaml` is a small version overlay;
it does not replace project names, Coder routing or other generated settings.
The service template is stored in `runtime/` and only installed into `.ddev/`
after the image exists, avoiding a missing-image failure on the installer's first
`ddev start`. `scripts/enable-runtime.sh` refuses to overwrite an unowned service
file and retains standard PHP as the default web service.

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
