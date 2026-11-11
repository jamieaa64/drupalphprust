# Source contracts and provenance

- One Line Installer project: https://www.drupal.org/project/one_line_installer
- Config reference: https://project.pages.drupalcode.org/one_line_installer/drupalaibp-json/
- Installer source: https://git.drupalcode.org/project/one_line_installer
- Runtime source: https://github.com/francescotinti/php-rust
- Project destination: https://github.com/jamieaa64/drupalphprust

On 2026-11-11 the Drupal project/docs fetches were unavailable to the web tool.
The configuration was checked against the clean local One Line Installer checkout
at `abb8deb61a4c8f7f710d3c1a55c6847b7523dd97` (commit dated 2026-09-14), specifically:

- docs/drupalaibp-json.md
- src/lib/config/load_config_url.sh and load_config_path.sh
- src/lib/config/hooks.sh and load_config.sh
- src/lib/steps/create_drupal_site.sh

Observed contract: template profile requires drupal/<site_template>; source overlay
happens after scaffolding; late hooks run from project root; hook paths resolve from
.drupalaibp config directory; hook failure warns and continues. Reconfirm these against
the actual installer revision chosen for the first smoke test. No upstream CLI code
was copied or modified, and latest upstream compatibility has not been asserted.

Runtime pin and per-patch integrity live in runtime/manifest.json. Existing exports
were imported from the earlier local investigation; patches and licenses are
included so another agent need not recover local branch history. Test PHPTs were
copied separately from that runtime checkout. No private site/database data was copied.

The earlier Coder template inspection showed DDEV project-name registration and
`ddev coder-setup` in its freeform test scripts. This establishes the host contract
to check, not proof that this new repository's service is routed correctly.
