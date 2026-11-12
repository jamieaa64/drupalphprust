#!/usr/bin/env python3
import importlib.util,json,os,shutil,subprocess,sys,tempfile,unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parent.parent
spec=importlib.util.spec_from_file_location('cleanup',ROOT/'scripts/cleanup-project.py')
module=importlib.util.module_from_spec(spec);spec.loader.exec_module(module)

class CleanupTests(unittest.TestCase):
    def test_only_owned_aliases_removed_and_retry_is_safe(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp).resolve();(root/'.ddev').mkdir();calls=[]
            manifest=root/'.ddev/.runtime-owned-hostnames.json'
            manifest.write_text(json.dumps({'root':str(root),'hosts':['php.mine.test','rust.mine.test']}))
            def run(args,check):calls.append(args)
            module.cleanup(root,run);module.cleanup(root,run)
            self.assertEqual(calls,[['ddev','hostname','-r','php.mine.test','127.0.0.1'],['ddev','hostname','-r','rust.mine.test','127.0.0.1']])
    def test_foreign_receipt_refused(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp);(root/'.ddev').mkdir()
            (root/'.ddev/.runtime-owned-hostnames.json').write_text(json.dumps({'root':'/other/project','hosts':['other.test']}))
            with self.assertRaises(ValueError):module.cleanup(root,lambda *a,**k:self.fail('must not execute'))
    def test_failed_removal_keeps_receipt_for_retry(self):
        with tempfile.TemporaryDirectory() as tmp:
            root=Path(tmp).resolve();(root/'.ddev').mkdir();p=root/'.ddev/.runtime-owned-hostnames.json'
            p.write_text(json.dumps({'root':str(root),'hosts':['mine.test']}))
            def fail(args,check):raise subprocess.CalledProcessError(1,args)
            with self.assertRaises(subprocess.CalledProcessError):module.cleanup(root,fail)
            self.assertEqual(json.loads(p.read_text())['hosts'],['mine.test'])
    def test_tracker_lifecycle_is_scoped_to_checkout(self):
        with tempfile.TemporaryDirectory() as tmp:
            processes=[];roots=[]
            try:
                for name in ['one','two']:
                    root=Path(tmp)/name;(root/'scripts').mkdir(parents=True);(root/'.ddev').mkdir()
                    shutil.copy2(ROOT/'scripts/tracker.py',root/'scripts/tracker.py')
                    proc=subprocess.Popen([sys.executable,str(root/'scripts/tracker.py'),'--serve','--port','0'],stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True)
                    processes.append(proc);roots.append(root)
                    self.assertIn('Tracker:',proc.stdout.readline())
                module.cleanup(roots[0],lambda *a,**k:self.fail('no owned aliases'))
                self.assertEqual(processes[0].wait(timeout=5),0)
                self.assertIsNone(processes[1].poll())
                shutil.rmtree(roots[1])
                self.assertEqual(processes[1].wait(timeout=5),0)
            finally:
                for proc in processes:
                    if proc.poll() is None:proc.terminate();proc.wait(timeout=5)
                    proc.stdout.close();proc.stderr.close()

if __name__=='__main__':unittest.main()
