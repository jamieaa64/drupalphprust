#!/usr/bin/env python3
"""Configuration isolation, repeatability and installer choices; no Docker needed."""
import importlib.util
import json
from pathlib import Path
import tempfile
import tomllib
import unittest
from unittest.mock import patch
import subprocess

ROOT = Path(__file__).resolve().parent.parent
spec = importlib.util.spec_from_file_location('setup_serena', ROOT/'scripts/setup-serena.py')
setup = importlib.util.module_from_spec(spec)
spec.loader.exec_module(setup)


class SetupTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory(prefix='serena space ')
        self.addCleanup(self.tmp.cleanup)
        self.root = Path(self.tmp.name)
        (self.root/'.ddev').mkdir()

    def test_host_both_preserves_existing_settings_and_is_idempotent(self):
        (self.root/'.codex').mkdir()
        (self.root/'.codex/config.toml').write_text('model = "existing-model"\n')
        (self.root/'.mcp.json').write_text('{"mcpServers":{"other":{"command":"unchanged"}}}')
        setup.configure(self.root, 'both', 'host')
        before = (self.root/'.codex/config.toml').read_text()
        setup.configure(self.root, 'both', 'host')
        self.assertEqual(before, (self.root/'.codex/config.toml').read_text())
        codex = tomllib.loads(before)
        self.assertEqual(codex['model'], 'existing-model')
        self.assertEqual(codex['mcp_servers'][setup.NAME]['args'], [str(self.root/'scripts/serena-mcp.sh')])
        claude = json.loads((self.root/'.mcp.json').read_text())['mcpServers']
        self.assertEqual(claude['other']['command'], 'unchanged')
        self.assertEqual(claude[setup.NAME]['args'][-1], 'claude-code')

    def test_ddev_switch_replaces_only_owned_connections(self):
        setup.configure(self.root, 'both', 'host')
        setup.configure(self.root, 'both', 'ddev')
        codex = tomllib.loads((self.root/'.codex/config.toml').read_text())
        self.assertEqual(codex['mcp_servers'][setup.NAME]['url'], 'http://serena:9121/mcp')
        self.assertNotIn('command', codex['mcp_servers'][setup.NAME])
        claude = json.loads((self.root/'.mcp.json').read_text())
        self.assertEqual(claude['mcpServers'][setup.NAME]['type'], 'http')

    def test_conflicting_claude_entry_does_not_partially_write_codex(self):
        original = json.dumps({'mcpServers': {setup.NAME: {'command': 'custom'}}})
        (self.root/'.mcp.json').write_text(original)
        with self.assertRaises(ValueError): setup.configure(self.root, 'both', 'host')
        self.assertFalse((self.root/'.codex/config.toml').exists())
        self.assertEqual((self.root/'.mcp.json').read_text(), original)

    def test_unowned_codex_entry_rejected(self):
        (self.root/'.codex').mkdir()
        (self.root/'.codex/config.toml').write_text('[mcp_servers.drupalphprust-serena]\ncommand="custom"\n')
        with self.assertRaises(ValueError): setup.configure(self.root, 'codex', 'host')

    def test_symlink_config_rejected(self):
        target = self.root/'unrelated.json'
        target.write_text('{}')
        (self.root/'.mcp.json').symlink_to(target)
        with self.assertRaises(ValueError): setup.configure(self.root, 'claude', 'host')
        self.assertEqual(target.read_text(), '{}')

    def test_manual_creates_no_config(self):
        setup.configure(self.root, 'manual', 'ddev')
        self.assertFalse((self.root/'.mcp.json').exists())
        self.assertFalse((self.root/'.codex').exists())

    def test_installer_questions_and_order(self):
        for name in ['drupalphprust', 'coder']:
            data = json.loads((ROOT/f'.drupalaibp/{name}.json').read_text())
            questions = {q['env']: q for q in data['questions']}
            self.assertEqual(questions['DRUPALPHPRUST_AGENT']['select_options'], ['codex','claude','both','manual'])
            self.assertNotIn('value', questions['DRUPALPHPRUST_AGENT'])
            self.assertEqual(questions['DRUPALPHPRUST_AGENT_LOCATION']['default'], 'ddev' if name == 'coder' else 'host')

    def prepare_checkout(self):
        (self.root/'.work/php-rust/php-rust').mkdir(parents=True)
        (self.root/'.work/php-rust/php-rust/Cargo.toml').write_text('[workspace]\n')
        (self.root/'runtime/serena').mkdir(parents=True)
        (self.root/'runtime/serena/project.yml').write_text('language_servers: [rust]\n')

    def test_container_agent_setup_installs_selected_clients_without_login(self):
        self.prepare_checkout()
        with patch.object(setup, 'ROOT', self.root), patch('sys.argv', ['setup-serena.py', '--client', 'both', '--location', 'ddev', '--install-agents']), patch.object(setup.subprocess, 'run') as run, patch('builtins.print'):
            setup.main()
        self.assertEqual([c.args[0] for c in run.call_args_list], [
            ['ddev','add-on','get','e0ipso/ddev-assistant-codex'],
            ['ddev','add-on','get','e0ipso/ddev-assistant-claude'],
            ['ddev','restart']])
        self.assertTrue((self.root/'.ddev/.serena-setup-complete').is_file())
        self.assertNotIn('bypass', (self.root/'.ddev/commands/web/codex').read_text())

    def test_failed_addon_never_leaves_completion_marker(self):
        self.prepare_checkout()
        (self.root/'.ddev/.serena-setup-complete').write_text('{}')
        with patch.object(setup, 'ROOT', self.root), patch('sys.argv', ['setup-serena.py', '--client', 'codex', '--location', 'ddev', '--install-agents']), patch.object(setup.subprocess, 'run', side_effect=subprocess.CalledProcessError(1, 'ddev')):
            with self.assertRaises(subprocess.CalledProcessError): setup.main()
        self.assertFalse((self.root/'.ddev/.serena-setup-complete').exists())


if __name__ == '__main__': unittest.main()
