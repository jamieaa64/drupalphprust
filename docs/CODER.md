# Coder and DDEV freeform

Coder provides the machine, Docker, persistent project directory and authenticated
access. DDEV and the One Line Installer provide Drupal. There is no separate
migration template dependency and no duplicated Coder-only Drupal installer.

## Intended contract

1. Start a DDEV freeform workspace (or a template with the same Docker/DDEV setup).
2. Check `docker info`, `ddev version`, disk space and project-directory ownership.
3. Run the exact One Line Installer source/config command in USAGE.md.
4. Ensure the DDEV project name is registered with the template if it requires a
   predefined project-name list. On templates providing it, run `ddev coder-setup`
   and follow that template's routing instructions; do not assume the command is
   installed on every DDEV host.
5. Verify normal DDEV/Zend access, then PHP Rust on workspace loopback port 18090.
6. Expose/forward that port using a **private authenticated** Coder app/port route
   or a local tunnel. Workspace loopback is not your laptop's loopback.

One Line Installer working on the freeform host establishes the provisioning
path. It does not by itself prove external URLs, TLS, routing, restart persistence,
port conflicts or database/file mounts. Those get their own smoke checks.

## Template acceptance (not yet run for this extraction)

- Docker usable by workspace user; DDEV can create and restart its network/services.
- Site files and database survive workspace stop/start according to template policy.
- Project naming and DDEV route setup work with the generated config.
- The `phpr` service sees the same Drupal files and can connect to DDEV's database.
- Loopback Rust route returns the expected rendered page, with no PHP error content.
- Private external route can reach it; verify proxy host handling/login cookies.
- A second project uses a distinct name and Rust port; no fixed public/global route.
- `ddev stop/start` restarts both engines without launching the installer again.

Do not modify an unrelated workspace or publish a port merely to make a check pass.
If a Coder template change is needed, prepare a separate template contribution
that invokes this repository/config. No template source was copied into this repo.

## Earlier experimental environment

Historical workspace: `php-rust-migration` on staging Coder; project:
`/home/coder/drupal-oob`; lab tools: `/home/coder/php-rust-lab`.
Its name predates this standalone project. The PHP Rust server there was started
as an independently labelled Docker container on port 18090, **not** through the
new DDEV sidecar. Existing browser receipts therefore do not validate this sidecar.
Do not start both on the same port. The original migration-related sites were
not the test target and are not needed to reproduce this repository.

The user may run `coder login https://staging-coder.ddev.com` when authentication
is needed. Agents may use the already-authorized local CLI/SSH for this experiment;
never collect the user's password or perform their authentication for them.
