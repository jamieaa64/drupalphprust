# Standalone project decision — 2026-11-11

The investigation began in a migration/Coder workspace only because a Coder
machine was needed. The user clarified that the actual project is getting Drupal
CMS working on PHP Rust, with a fast repeatable setup and comparisons against Zend.

Destination: https://github.com/jamieaa64/drupalphprust
Foundation: DDEV and the Drupal One Line Installer; Coder freeform is a host, not a
separate Drupal provisioning implementation.

Retained: 29 ordered runtime patches, independent fixtures, database/SPL findings,
current blockers and experimental build recipe. Standard-PHP provisioning remains
necessary while PHP Rust compatibility is incomplete.

Excluded: migration dashboard/agents, MCP/OAuth integration, earlier migration
installer/seed gates, private credentials, browser sessions, database contents and
raw logs. The old repositories remain untouched as historical local evidence.

Replaced by: ../current/README.md. This decision does not claim that the new
installer hook, DDEV service or Coder routing has passed end-to-end tests.
