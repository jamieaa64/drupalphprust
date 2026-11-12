#!/usr/bin/env python3
"""Check linked plans, fresh Markdown reads and safe standalone embedding."""
import importlib.util, json, re, tempfile, unittest
from pathlib import Path
ROOT=Path(__file__).resolve().parent.parent
spec=importlib.util.spec_from_file_location('tracker',ROOT/'scripts/tracker.py')
tracker=importlib.util.module_from_spec(spec);spec.loader.exec_module(tracker)

class TrackerTests(unittest.TestCase):
    def test_each_step_has_work_tests_and_resolving_links(self):
        data=tracker.snapshot()
        self.assertEqual(len(data['steps']),10)
        for step in data['steps']:
            text=data['pages'][step['path']]
            self.assertIn('## Work',text)
            self.assertIn('## Test / finish',text)
            self.assertLess(len(text.split()),160)
            for link in re.findall(r'\]\(([^)]+)\)',text):
                self.assertTrue((ROOT/step['path']).parent.joinpath(link).resolve().is_file(),link)
    def test_script_closing_text_remains_inert_json(self):
        data={'pages':{'test.md':'</script><script>alert(1)</script>'},'steps':[]}
        rendered=tracker.html(data)
        embedded=re.search(r'<script id="snapshot" type="application/json">(.*?)</script>',rendered,re.S)[1]
        self.assertNotIn('<',embedded)
        self.assertEqual(json.loads(embedded),data)
    def test_snapshot_reads_current_files_and_excludes_private_paths(self):
        with tempfile.TemporaryDirectory() as temp:
            root=Path(temp); folder=root/'.agents/plan/current';folder.mkdir(parents=True)
            for n in range(10): (folder/f'{n:02d}-step.md').write_text('# Step\nStatus: pending\n')
            (root/'PROGRESS.md').write_text('Before')
            (root/'.env').write_text('private canary')
            self.assertEqual(tracker.snapshot(root)['pages']['PROGRESS.md'],'Before')
            (root/'PROGRESS.md').write_text('After')
            data=tracker.snapshot(root)
            self.assertEqual(data['pages']['PROGRESS.md'],'After')
            self.assertNotIn('.env',data['pages'])

if __name__=='__main__':unittest.main()
