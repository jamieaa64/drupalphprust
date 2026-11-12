#!/usr/bin/env python3
"""Exercise launcher boundaries with a real Git clone and fake host/installer commands."""
import os, shutil, subprocess, tempfile, unittest
from pathlib import Path
ROOT = Path(__file__).resolve().parent.parent

class LauncherTests(unittest.TestCase):
    def setUp(self):
        self.tmp = tempfile.TemporaryDirectory()
        self.addCleanup(self.tmp.cleanup)
        self.base = Path(self.tmp.name)
        self.source = self.base/'source'
        self.source.mkdir()
        (self.source/'scripts').mkdir()
        for name in ['install.py','check-secrets.py']:
            shutil.copy2(ROOT/'scripts'/name, self.source/'scripts'/name)
        (self.source/'scripts/check-repo.py').write_text('print("contract fixture")')
        (self.source/'.gitignore').write_text('.env\n.work/\n')
        self.git('init', '-q')
        self.git('add', '.')
        self.git('-c','user.name=Fixture','-c','user.email=fixture@example.invalid','commit','-qm','fixture')
        (self.source/'.env').write_text('PRIVATE_TEST_CANARY=must-not-copy\n')
        (self.source/'.work').mkdir()
        (self.source/'.work/build').write_text('must-not-copy')
        self.bin = self.base/'bin'; self.bin.mkdir()
        for name, body in [('docker','exit 0'),('ddev','exit 0'),('coder','exit 93')]:
            f=self.bin/name; f.write_text('#!/bin/sh\n'+body+'\n'); f.chmod(0o755)
        self.installer=self.base/'installer'
        self.installer.write_text("""#!/usr/bin/env bash
set -euo pipefail
source="$1"
[[ ! -e "$source/.env" && ! -e "$source/.work" ]]
[[ "$(git -C "$source" remote get-url origin)" == https://github.com/jamieaa64/drupalphprust.git ]]
[[ "$2" == --config && "$4" == --name ]]
expected=drupalphprust; [[ "$DRUPALPHPRUST_TARGET" == coder ]] && expected=coder
[[ "$3" == "$expected" ]]
mkdir -p "$5/.ddev"
printf '%s:%s' "$DRUPALPHPRUST_TARGET" "$PHPR_HTTP_PORT" > "$5/observed"
if [[ "${TEST_NO_MARKER:-0}" != 1 ]]; then echo ready > "$5/.ddev/.runtime-ready"; fi
""")
        self.env=dict(os.environ, PATH=str(self.bin)+os.pathsep+os.environ['PATH'])
    def git(self,*args):
        return subprocess.run(['git',*args],cwd=self.source,check=True,capture_output=True)
    def run_install(self,*args,env=None):
        return subprocess.run(['python3',str(self.source/'scripts/install.py'),
            '--destination',str(self.base/'site'),'--installer',str(self.installer),*args],
            env=env or self.env,text=True,capture_output=True)
    def test_local_excludes_private_files_and_needs_no_coder(self):
        result=self.run_install('--target','local','--runtime-port','18091','--yes')
        self.assertEqual(result.returncode,0,result.stderr)
        self.assertEqual((self.base/'site/observed').read_text(),'local:18091')
    def test_coder_uses_same_installer_without_coder_client(self):
        result=self.run_install('--target','coder')
        self.assertEqual(result.returncode,0,result.stderr)
        self.assertEqual((self.base/'site/observed').read_text(),'coder:18090')
        self.assertIn('coder port-forward WORKSPACE',result.stdout)
    def test_missing_hook_marker_fails_even_if_installer_succeeds(self):
        result=self.run_install(env=dict(self.env,TEST_NO_MARKER='1'))
        self.assertNotEqual(result.returncode,0)
        self.assertIn('without runtime readiness',result.stderr)
    def test_existing_destination_not_modified(self):
        (self.base/'site').mkdir()
        result=self.run_install()
        self.assertNotEqual(result.returncode,0)
        self.assertEqual(list((self.base/'site').iterdir()),[])
    def test_dirty_source_rejected(self):
        (self.source/'uncommitted').write_text('change')
        result=self.run_install()
        self.assertNotEqual(result.returncode,0)
        self.assertIn('Commit source changes first',result.stderr)
    def test_invalid_port_rejected_before_install(self):
        result=self.run_install('--runtime-port','70000')
        self.assertNotEqual(result.returncode,0)
        self.assertFalse((self.base/'site').exists())

class RuntimePortTests(unittest.TestCase):
    def test_port_survives_rebuild_and_unowned_file_is_protected(self):
        with tempfile.TemporaryDirectory() as temp:
            root=Path(temp)
            for directory in ['scripts','runtime','.ddev','web','bin']:
                (root/directory).mkdir()
            shutil.copy2(ROOT/'scripts/enable-runtime.sh',root/'scripts/enable-runtime.sh')
            shutil.copy2(ROOT/'runtime/docker-compose.phpr.yaml',root/'runtime/docker-compose.phpr.yaml')
            (root/'.ddev/config.yaml').write_text('name: fixture\n')
            (root/'web/.ht.router.php').write_text('<?php')
            for name in ['docker','ddev']:
                f=root/'bin'/name; f.write_text('#!/bin/sh\nexit 0\n'); f.chmod(0o755)
            env=dict(os.environ,PATH=str(root/'bin')+os.pathsep+os.environ['PATH'])
            env.pop('PHPR_HTTP_PORT',None)
            command=['bash',str(root/'scripts/enable-runtime.sh')]
            subprocess.run(command,env=dict(env,PHPR_HTTP_PORT='18091'),check=True,capture_output=True)
            subprocess.run(command,env=env,check=True,capture_output=True)
            service=root/'.ddev/docker-compose.phpr.yaml'
            self.assertIn('127.0.0.1:18091:8090',service.read_text())
            service.write_text('# user owned\n')
            result=subprocess.run(command,env=env,capture_output=True)
            self.assertNotEqual(result.returncode,0)
            self.assertEqual(service.read_text(),'# user owned\n')

class HostChoiceTests(unittest.TestCase):
    def test_host_hook_uses_installer_answer_without_executing_dotenv(self):
        for answer, override, expected in [(None,None,'local'),('coder',None,'coder'),('coder','local','local')]:
            with self.subTest(answer=answer,override=override), tempfile.TemporaryDirectory() as temp:
                root=Path(temp)
                for directory in ['runtime','scripts','.ddev']:
                    (root/directory).mkdir()
                (root/'runtime/Dockerfile').touch()
                (root/'.ddev/config.yaml').touch()
                for name in ['build-runtime.sh','enable-runtime.sh']:
                    (root/'scripts'/name).write_text('exit 0\n')
                contents='UNRELATED=$(touch should-not-exist)\n'
                if answer: contents += 'DRUPALPHPRUST_TARGET="'+answer+'"\n'
                (root/'.ddev/.env').write_text(contents)
                env=dict(os.environ); env.pop('DRUPALPHPRUST_TARGET',None)
                if override: env['DRUPALPHPRUST_TARGET']=override
                result=subprocess.run(['bash',str(ROOT/'.drupalaibp/setup-runtime.sh')],cwd=root,env=env,capture_output=True,text=True)
                self.assertEqual(result.returncode,0,result.stderr)
                self.assertEqual((root/'.ddev/.runtime-target').read_text().strip(),expected)
                self.assertFalse((root/'should-not-exist').exists())

if __name__=='__main__': unittest.main()
