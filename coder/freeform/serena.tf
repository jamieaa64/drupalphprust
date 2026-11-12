# Add this file to the existing ddev/coder-ddev freeform template.
# Does not create a separate template or start a DDEV project.
data "coder_parameter" "enable_serena_dashboard" {
  name         = "enable_serena_dashboard"
  display_name = "Serena dashboard"
  description  = "Show a private dashboard button for the PHP Rust project's Serena service."
  type         = "bool"
  form_type    = "switch"
  default      = "false"
  mutable      = true
  order        = 20
}

data "coder_parameter" "serena_dashboard_port" {
  name         = "serena_dashboard_port"
  display_name = "Serena dashboard loopback port"
  description  = "Match SERENA_DASHBOARD_PORT used when setting up the DDEV project."
  type         = "string"
  default      = "24283"
  mutable      = true
  order        = 21
}

resource "coder_app" "serena_dashboard" {
  count        = try(tobool(data.coder_parameter.enable_serena_dashboard.value), false) ? 1 : 0
  agent_id     = coder_agent.main.id
  slug         = "serena"
  display_name = "Serena dashboard"
  url          = "http://localhost:${data.coder_parameter.serena_dashboard_port.value}/dashboard/index.html"
  share        = "owner"
  subdomain    = true
  open_in      = "tab"
  order        = 20

  healthcheck {
    url       = "http://localhost:${data.coder_parameter.serena_dashboard_port.value}/dashboard/index.html"
    interval  = 10
    threshold = 3
  }

  lifecycle {
    precondition {
      condition     = can(regex("^[0-9]+$", data.coder_parameter.serena_dashboard_port.value)) && try(tonumber(data.coder_parameter.serena_dashboard_port.value) >= 1024 && tonumber(data.coder_parameter.serena_dashboard_port.value) <= 65535, false)
      error_message = "Serena dashboard port must be an integer between 1024 and 65535."
    }
  }
}
