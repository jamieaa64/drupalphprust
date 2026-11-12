# Tracker button in Coder

Research checked 2026-11-12. This is a proposed integration, not a deployed button.
The staging template version has not been compared with upstream freeform yet.

## Can buttons be added later?

Yes. Coder's documented persistent workspace buttons are `coder_app` resources in
the workspace template. A template administrator can add one to the existing
freeform template, then existing workspaces adopt that template version. Updating
a running workspace stops and restarts it. Creating a new Drupal-specific template
is unnecessary. See [Coder app/port forwarding](https://coder.com/docs/admin/networking/port-forwarding/)
and [updating workspaces](https://coder.com/docs/user-guides/workspace-management).

The installer runs inside the workspace; it does not own Terraform resources.
No supported installer-only dynamic app-registration mechanism was found in the
reviewed documentation. Do not put a Coder API token in the project to bypass that
boundary. Without a template change, start the tracker and open port 8765 through
Coder's **Open Ports** UI (if allowed by that deployment).

## What freeform already provides

The reviewed [DDEV freeform template](https://github.com/ddev/coder-ddev/blob/main/freeform/template.tf)
uses `coder_agent.main` and a mutable `project_names` parameter that creates app
buttons for DDEV projects. That parameter is useful for additional Drupal sites;
it does not register an arbitrary Python tracker port. The default site-sharing
setting is private. Keep the tracker owner-only independently of site sharing.

## Small proposed addition

Add an optional tracker app to the existing freeform template. This illustrative
resource matches the reviewed agent name; reconcile it with the deployed template
before applying. It does not start the server itself.

```hcl
resource "coder_app" "drupalphprust_tracker" {
  agent_id     = coder_agent.main.id
  slug         = "drupalphprust-tracker"
  display_name = "PHP Rust tracker"
  url          = "http://localhost:8765"
  share        = "owner"
  subdomain    = false
  open_in      = "tab"

  healthcheck {
    url       = "http://localhost:8765/"
    interval  = 10
    threshold = 3
  }
}
```

Confirm fields against the [coder_app provider schema](https://registry.terraform.io/providers/coder/coder/latest/docs/resources/app).
For general freeform use, make app creation opt-in with a mutable parameter.
A later generic contribution should support configurable app names and ports,
not hard-code this project into everyone's workspace.

## Work still needed

- Inspect the selected staging freeform version and prepare the minimal template
  change in its owning repository. No credentials or template state belong here.
- Add opt-in server lifecycle handling for Coder mode: start after installation
  and restart with the workspace, using the actual checkout path. Avoid duplicate
  servers or claiming an unrelated process on port 8765. Until then, run
  `python3 scripts/tracker.py --serve` from the checkout in a workspace terminal.
- Keep local installation unchanged. The tracker binds loopback and only serves
  its documentation snapshot. Its refresh URL is relative for Coder path routing.

## Acceptance

Create/update a disposable freeform workspace and install with the existing Coder
config. Verify button visibility, owner-only access, nested document navigation,
live refresh after Markdown changes and workspace stop/start recovery. Check a
missing checkout or failed server is shown as not ready. Recheck local preview.
The Terraform example and Coder proxy behavior have not yet been deployment-tested.

[Implementation task](../.agents/plan/current/10-coder-tracker.md) ·
[Tracker usage](TRACKER.md) · [Coder setup](CODER.md) ·
[Overall plan](../.agents/plan/current/README.md)
