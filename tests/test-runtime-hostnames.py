#!/usr/bin/env python3
import importlib.util
import json
import subprocess
import tempfile
import unittest
from pathlib import Path

ROOT=Path(__file__).resolve().parent.parent
spec=importlib.util.spec_from_file_location('hostnames',ROOT/'scripts/register-runtime-hostnames.py')
module=importlib.util.module_from_spec(spec);spec.loader.exec_module(module)

class HostnameTests(unittest.TestCase):
    def setUp(self):
        self.tmp=tempfile.TemporaryDirectory();self.addCleanup(self.tmp.cleanup)
        self.root=Path(self.tmp.name);(self.root/'.ddev').mkdir()
        (self.root/'.ddev/.runtime-urls').write_text('Standard PHP (Zend): https://php.custom.test:8443/\nPHP RUST: https://rust.custom.test:8443/\nProject tracker: https://tracker.custom.test:8443/\n')
        self.calls=[];self.resolved=set()
    def resolve(self,host):
        if host not in self.resolved: raise socket_error()
        return '127.0.0.1'
    def run_ddev(self,command,check):
        self.calls.append(command);self.resolved.add(command[2])
    def status(self):return json.loads((self.root/'.ddev/.runtime-network.json').read_text())['status']
    def test_registers_actual_hosts_and_second_run_is_idempotent(self):
        self.assertEqual(module.register(self.root,self.run_ddev,self.resolve),0)
        self.assertEqual(self.calls,[['ddev','hostname','php.custom.test','127.0.0.1'],['ddev','hostname','rust.custom.test','127.0.0.1'],['ddev','hostname','tracker.custom.test','127.0.0.1']])
        self.assertEqual(module.register(self.root,self.run_ddev,self.resolve),0)
        self.assertEqual(len(self.calls),3);self.assertEqual(self.status(),'ready')
    def test_coder_does_not_change_host_dns(self):
        (self.root/'.ddev/.runtime-target').write_text('coder')
        self.assertEqual(module.register(self.root,self.run_ddev,self.resolve),0)
        self.assertEqual(self.calls,[]);self.assertEqual(self.status(),'private-routing-required')
    def test_failed_registration_never_marks_ready(self):
        def fail(command,check):raise subprocess.CalledProcessError(1,command)
        self.assertEqual(module.register(self.root,fail,self.resolve),1)
        self.assertEqual(self.status(),'failed')
    def test_bad_url_never_runs_command(self):
        (self.root/'.ddev/.runtime-urls').write_text('Standard PHP (Zend): https://secret:password@php.custom.test/\nPHP RUST: https://rust.custom.test/\n')
        self.assertEqual(module.register(self.root,self.run_ddev,self.resolve),1)
        self.assertEqual(self.calls,[])
    def test_successful_command_but_wrong_dns_is_failure(self):
        self.assertEqual(module.register(self.root,lambda *a,**kw:None,lambda host:'192.0.2.1'),1)
        self.assertEqual(self.status(),'failed')

# A DNS failure, without depending on this machine's resolver.
def socket_error():return OSError('unresolved fixture hostname')

if __name__=='__main__':unittest.main()
