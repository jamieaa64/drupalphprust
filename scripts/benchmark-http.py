#!/usr/bin/env python3
"""Interleaved, sequential HTTP comparison; only successful matching pages count."""
import argparse
import json
import statistics
import time
import urllib.request
from pathlib import Path

p = argparse.ArgumentParser(description=__doc__)
p.add_argument('--zend', required=True)
p.add_argument('--rust', required=True)
p.add_argument('--path', default='/')
p.add_argument('--contains', required=True, help='Text required in every response')
p.add_argument('--rounds', type=int, default=20)
p.add_argument('--output', type=Path, required=True)
a = p.parse_args()
if a.rounds < 2:
    p.error('At least two rounds required')
results = {'zend': [], 'rust': []}
first = {}
for round_no in range(a.rounds + 1):
    for name, base in [('zend', a.zend), ('rust', a.rust)]:
        started = time.perf_counter()
        with urllib.request.urlopen(base.rstrip('/') + a.path, timeout=60) as r:
            body = r.read()
            status = r.status
        elapsed = time.perf_counter() - started
        if status != 200 or a.contains.encode() not in body:
            raise RuntimeError(f'{name}: invalid response, status {status}; no comparison produced')
        sample = {'seconds': elapsed, 'bytes': len(body), 'status': status}
        if round_no == 0:
            first[name] = sample
        else:
            results[name].append(sample)
summary = {}
for name, samples in results.items():
    seconds = sorted(s['seconds'] for s in samples)
    summary[name] = {'median_seconds': statistics.median(seconds),
                     'p95_seconds': seconds[max(0, (95 * len(seconds) + 99) // 100 - 1)]}
report = {'method': 'Sequential ABAB HTTP GET, first observation excluded from warm samples',
          'scope': 'Whole HTTP stack including web server and Drupal cache; not isolated engine speed',
          'path': a.path, 'rounds': a.rounds, 'first_observation': first,
          'summary': summary, 'samples': results,
          'rust_over_zend_median': summary['rust']['median_seconds'] / summary['zend']['median_seconds']}
a.output.parent.mkdir(parents=True, exist_ok=True)
a.output.write_text(json.dumps(report, indent=2) + '\n')
print(json.dumps({k: report[k] for k in ['scope', 'summary', 'rust_over_zend_median']}, indent=2))
