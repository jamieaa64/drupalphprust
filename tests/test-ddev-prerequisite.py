#!/usr/bin/env python3
"""Version preflight tests use command doubles; never contact Docker or Homebrew."""
import json,os,subprocess,tempfile,unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parent.parent
HOOK=ROOT/'.drupalaibp/check-ddev-version.sh'
class PrerequisiteTests(unittest.TestCase):
    def run_hook(self,root,version,*args):
        bin=root/'bin';bin.mkdir(exist_ok=True)
        ddev=bin/'ddev';ddev.write_text('#!/bin/sh\n[ "$1" = --version ] || exit 99\nprintf "%s\\n" "$FIXTURE_DDEV_VERSION"\n');ddev.chmod(0o755)
        # Any accidental upgrade attempt is a test failure.
        for command in ['brew','sudo','curl']:
            p=bin/command;p.write_text('#!/bin/sh\ntouch "$FORBIDDEN_CALL"\nexit 99\n');p.chmod(0o755)
        env=dict(os.environ,PATH=str(bin)+':'+os.environ['PATH'],FIXTURE_DDEV_VERSION=version,FORBIDDEN_CALL=str(root/'forbidden'))
        result=subprocess.run(['bash',str(HOOK),*args],cwd=root,env=env,text=True,capture_output=True)
        self.assertFalse((root/'forbidden').exists())
        return result
    def test_old_unknown_and_prerelease_versions_fail_with_commands(self):
        for version in ['ddev version v1.25.0','ddev version v1.25.5-rc1','unknown','']:
            with self.subTest(version=version),tempfile.TemporaryDirectory() as tmp:
                root=Path(tmp);result=self.run_hook(root,version)
                self.assertEqual(result.returncode,1)
                for command in ['brew upgrade ddev/ddev/ddev','brew trust --formula ddev/ddev/ddev','ddev --version','new empty destination']:
                    self.assertIn(command,result.stderr)
                self.assertIn("ddev_version_constraint: '>= v1.25.5'",(root/'.ddev/config.php-rust-version.yaml').read_text())
                self.assertFalse((root/'web').exists())
    def test_supported_versions_pass_and_repeat_without_losing_guard(self):
        for version in ['ddev version v1.25.5','ddev version v1.25.6','ddev version v1.26.0','ddev version v2.0.0']:
            with self.subTest(version=version),tempfile.TemporaryDirectory() as tmp:
                root=Path(tmp)
                for _ in range(2):self.assertEqual(self.run_hook(root,version).returncode,0)
                self.assertTrue((root/'.ddev/config.php-rust-version.yaml').is_file())
    def test_check_only_writes_nothing(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp);self.assertEqual(self.run_hook(root,'ddev version v1.25.0','--check-only').returncode,1)
            self.assertFalse((root/'.ddev').exists())
    def test_unowned_configuration_preserved(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp);(root/'.ddev').mkdir();p=root/'.ddev/config.php-rust-version.yaml';p.write_text('user configuration\n')
            self.assertEqual(self.run_hook(root,'ddev version v1.25.5').returncode,1)
            self.assertEqual(p.read_text(),'user configuration\n')
    def test_both_installer_configs_check_before_ddev_configuration(self):
        for name in ['coder','drupalphprust']:
            config=json.loads((ROOT/f'.drupalaibp/{name}.json').read_text())
            self.assertEqual(config['hooks']['before_configure_ddev'][0]['script'],'./check-ddev-version.sh')
            self.assertEqual(config['hooks']['before_configure_ddev'][0]['where'],'host')
if __name__=='__main__':unittest.main()
