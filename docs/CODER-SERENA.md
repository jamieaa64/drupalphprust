# Serena dashboard in DDEV Freeform

Target: the existing **DDEV Freeform** (`freeform`) template in
`ddev/coder-ddev/freeform`, deployed at `https://staging-coder.ddev.com`.
Read-only template download succeeded on 12 November 2026: active version
`thankful_west78`, ID `416847ac-a65f-4303-83e0-e5a3824347a2`.
Do not use `canvas-migration` or create a separate Drupal PHP Rust template.

## Prepared button

`coder/freeform/serena.tf` adds an opt-in **Serena dashboard** button to
`coder_agent.main`. It always uses `share = "owner"`, independent of the
template's public website-sharing switch. It uses a subdomain because Serena's
dashboard makes root-relative requests. The button serves the dashboard only;
MCP port 9121 remains internal to DDEV.

The default backend is `http://localhost:24283/dashboard/index.html`. This is
the workspace loopback, not the user's laptop. One button targets one Serena
service per workspace. Change the parameter if that port is already in use.

## Prepare and publish

Download the currently active template before every update, preserve its other
files, and add the supplied `serena.tf`. Do not replace the deployed template
with an upstream checkout: the deployed version has additional modules.

```bash
coder templates pull freeform .work/coder-freeform-new --version active --yes
cp coder/freeform/serena.tf .work/coder-freeform-new/serena.tf
mkdir -p .work/coder-freeform-new/tests
cp coder/freeform/tests/serena.tftest.hcl .work/coder-freeform-new/tests/
```

The user publishes an inactive version for review:

```bash
coder templates push freeform --directory .work/coder-freeform-new \
  --name serena-dashboard-20261112 --activate=false
```

Review/activate that version in Coder, then update an explicitly selected
disposable **Freeform** workspace and enable its Serena dashboard parameter.
Updating a workspace can stop/restart it; no existing workspace was updated by
the assistant. Template publication, workspace update and the real proxied button
remain unverified until performed by the user.

## Configure the installed project

Inside that workspace, pin the same loopback port while installing:

```bash
SERENA_DASHBOARD_PORT=24283 bash <(curl -fsSL https://aibp.drupalstarforge.ai/install.sh) \
  https://github.com/jamieaa64/drupalphprust --config coder
```

For an existing selected site use:

```bash
SERENA_DASHBOARD_PORT=24283 bash scripts/enable-runtime.sh dev-release
```

The chosen port is saved across restarts. Only the dashboard is published, bound
to `127.0.0.1`. Coder's proxy may preserve its external app Host header. Copy the
Serena button's hostname (no scheme/path) from Coder and explicitly trust it:

```bash
ddev dotenv set .ddev/.env --serena-dashboard-extra-hosts=YOUR_EXACT_SERENA_APP_HOSTNAME
ddev restart
```

Do not use `*` or an empty trusted-host list. A 400 Invalid Host response means
the exact external hostname has not been allowed; it is not an MCP failure.
This does not require a Coder token in Drupal or the repository.

## Acceptance

Verify button visibility, owner-only access, dashboard assets and API requests,
the active `php-rust` project, tool activity and stop/start recovery in a disposable
Freeform workspace. Confirm the public website-sharing switch does not publish
the dashboard. Local DDEV checks do not certify Coder proxy behaviour.

The shared dashboard reflects the long-running HTTP Serena process. Host Codex
STDIO connections have separate Serena processes and their calls are not included.
The Mac URL on port 24282 belongs to the separately installed host Serena instance.
