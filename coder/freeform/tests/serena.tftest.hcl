mock_provider "coder" {
  mock_data "coder_workspace" {
    defaults = {
      start_count = 1
      id          = "mock-workspace-id"
      name        = "test-workspace"
    }
  }
  mock_data "coder_workspace_owner" {
    defaults = { name = "testuser" }
  }
  mock_data "coder_parameter" {
    defaults = { value = "[]" }
  }
}
mock_provider "docker" {}

run "serena_disabled_by_default" {
  command = plan
  assert {
    condition     = length(coder_app.serena_dashboard) == 0
    error_message = "Serena must remain opt-in."
  }
}

run "serena_private_even_when_sites_public" {
  command = plan
  override_data {
    target = data.coder_parameter.enable_serena_dashboard
    values = { value = "true" }
  }
  override_data {
    target = data.coder_parameter.serena_dashboard_port
    values = { value = "24283" }
  }
  override_data {
    target = data.coder_parameter.share_projects
    values = { value = "true" }
  }
  assert {
    condition     = coder_app.serena_dashboard[0].share == "owner" && coder_app.serena_dashboard[0].subdomain
    error_message = "Serena must use a private subdomain regardless of website sharing."
  }
  assert {
    condition     = coder_app.serena_dashboard[0].url == "http://localhost:24283/dashboard/index.html"
    error_message = "The button must target the configured workspace-loopback dashboard port."
  }
}
