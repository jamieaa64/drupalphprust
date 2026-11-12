# Normal project deletion

Use the usual AIBP **delete** command. No separate cleanup command or installer
patch is required for new installations using DDEV **1.25.5 or newer**.
AIBP calls `ddev delete` from the selected project, then removes its directory.
DDEV's native `pre-delete` hook releases this checkout's recorded host aliases
and stops its live tracker previews before DDEV removes project containers,
volumes and Compose-built images. DDEV alone leaves the source directory intact.

The bundled `php-rust` DDEV add-on installs the service configuration and deletion
hook. PHP Rust is built by DDEV under a project-scoped image tag, rather than a
shared `drupalphprust:dev-release` tag. Docker base images and build cache may
remain, as in other DDEV projects. No global prune or process-name kill is used.

## Ownership and failure

Registration records the absolute checkout root and only aliases it attempts to
create in ignored `.ddev/.runtime-owned-hostnames.json`. Already resolving names
are not adopted. Cleanup refuses a receipt from another root and keeps failed
entries for retry. DDEV stops deletion if this hook fails. Handle administrator
passwords directly in your terminal; never put them in Git or the tracker.

Manually created aliases without ownership evidence are preserved. Old sites do
not acquire hooks merely by upgrading DDEV: their project configuration must
also be updated. The old globally tagged runtime image is not owned by the new
add-on and is never removed automatically, because other sites may use it.

To disable the experiment while keeping Drupal and its database, use:

```bash
ddev add-on remove php-rust
ddev restart
```

This removes add-on-managed configuration and commands and releases its aliases.
It does not delete your Drupal database. Normal full project deletion remains
the AIBP delete workflow. See the [verification receipt](receipts/2026-11-12-native-ddev-addon.md)
for tested scope and remaining fresh-install checks.
