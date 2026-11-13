# 17. Resolve runtime release gate failures

Status: pending

Destination: PHP Rust + this repository

The final 46-patch candidate completes the scoped Drupal content workflow, but
its Cargo release suite is not green: **1,756 passed, 3 failed, 2 ignored**.
This task owns the implementation needed to resolve those failures, rather than
adding a separate task merely for running tests. See the
[exact result and command](../../../docs/receipts/2026-11-13-authenticated-runtime.md).

## Work

- Correct opcode census accounting: `op_index_maps_sampled_variants_in_range`
  reports 177 versus 178. Check actual opcode coverage before changing expectations.
- Resolve `nessun_sorgente_rs_oltre_cap`: 14 source files exceed their recorded
  size limits. Follow upstream's refactoring and declared-cap process; do not
  silently raise limits to make the test pass.
- Investigate `rczval_pattern_resta_nel_funnel`: `vm/host.rs` has seven matching
  allocations versus six expected. Check the new site against the required
  `zcell()` funnel and preserve resource/reference behavior.
- Use Serena and the upstream mandatory process, including one adversarial
  reviewer. Export discrete fixes and align patches, series, Dockerfile and manifest.

## Test / finish

- [ ] Reproduce each failure and record its cause and focused regression result.
- [ ] Obtain adversarial review of the runtime fixes.
- [ ] Reconstruct the candidate from the portable patch series and verify source parity.
- [ ] Pass the full release suite using `bash scripts/test-runtime-upstream.sh`
  with the bundled PHP oracle; explain any ignored tests.
- [ ] Re-run combined comparison probes and PHPT in both compiler modes after changes.
- [ ] Confirm the Drupal create/edit/publish/view journey still works and record
  candidate identity and exact results in a new receipt.

## Upstream

Not submitted. This task does not establish fresh-installer acceptance, Coder
routing, performance or full Drupal parity. Those remain owned by tasks 09–13.

[Overall plan](README.md) · [Runtime completion](01-fiber-generators.md) ·
[Handover](../../../docs/HANDOVER.md)
