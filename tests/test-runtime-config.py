#!/usr/bin/env python3
"""Verify routing derives from DDEV and keeps the three service hosts distinct."""
import importlib.util,json,shutil,tempfile,unittest
from pathlib import Path
from types import SimpleNamespace
from unittest.mock import patch
ROOT=Path(__file__).resolve().parent.parent
spec=importlib.util.spec_from_file_location('configure',ROOT/'scripts/configure-runtime.py')
configure=importlib.util.module_from_spec(spec);spec.loader.exec_module(configure)

class RuntimeConfigurationTests(unittest.TestCase):
    def test_custom_domain_scheme_port_and_project_image(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp);(root/'.ddev').mkdir()
            shutil.copytree(ROOT/'ddev-addon',root/'ddev-addon')
            raw={'primary_url':'http://custom.example.test:8088','name':'actual-name','password':'private-canary'}
            result=SimpleNamespace(stdout=json.dumps({'raw':raw}))
            with patch.object(configure.subprocess,'run',return_value=result) as run:
                configure.routes(root,'release','0')
            urls=(root/'.ddev/.runtime-urls').read_text()
            for host in ['php','rust','tracker']:
                self.assertIn(f'http://{host}.custom.example.test:8088/',urls)
            self.assertNotIn('private-canary',urls)
            self.assertEqual(run.call_args.args[0][-1],'--launch-path=http://tracker.custom.example.test:8088/')
            configure.render(root)
            compose=(root/'.ddev/docker-compose.phpr.yaml').read_text()
            self.assertIn('127.0.0.1::8090',compose)
            self.assertNotIn('__TRACKER_HOST__',compose)
            self.assertIn('VIRTUAL_HOST: "tracker.custom.example.test"',compose)
            self.assertIn('bookworm-slim-actual-name-phpr-built',(root/'.ddev/.runtime-installed-image').read_text())

    def test_credentials_in_primary_url_rejected(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp);(root/'.ddev').mkdir()
            result=SimpleNamespace(stdout=json.dumps({'raw':{'primary_url':'https://user:secret@example.test','name':'test'}}))
            with patch.object(configure.subprocess,'run',return_value=result):
                with self.assertRaises(ValueError):configure.routes(root)
            self.assertFalse((root/'.ddev/.runtime-urls').exists())

    def test_setup_precedes_completion_in_both_configs(self):
        for name in ['coder','drupalphprust']:
            config=json.loads((ROOT/f'.drupalaibp/{name}.json').read_text())
            hooks=config['hooks']['before_announce_complete']
            self.assertEqual([h['script'] for h in sorted(hooks,key=lambda h:h['weight'])],
                             ['./setup-runtime.sh','./setup-tracker.sh'])
            self.assertNotIn('before_launch_site',config['hooks'])

if __name__=='__main__':unittest.main()
