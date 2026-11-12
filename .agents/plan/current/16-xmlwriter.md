# 16. Implement XMLWriter for out-of-box Drupal CMS

Status: active

Destination: PHP Rust + this repository

The untouched local installation cannot serve its homepage on PHP Rust: HTTP 500,
`Class "XMLWriter" not found` in Symfony ContainerBuilder.php:416. Standard PHP
returns HTTP 200. This is an active acceptance blocker, separate from task 08's
historical deferred scope and the earlier scoped Fiber/Generator failure.

## Work

- Identify the exact Drupal/dependency call path and XMLWriter methods required;
  do not assume the triggering module from the earlier scoped experiment.
- Minimise the failure into independent Zend parity fixtures.
- Implement the required XMLWriter runtime support in the reconstructed upstream
  checkout, following its process and adversarial review requirement.
- Export a discrete patch and keep series, Dockerfile and manifest aligned.
- Do not disable modules or alter Drupal core to count this task as complete.

## Test / finish

- [x] Reproduce ordinary HTTPS homepage: Rust 500, standard PHP 200 on this installation.
- [ ] Match Zend output, escaping, state transitions and error behaviour for required APIs.
- [ ] Run upstream-required checks and relevant combined runtime regressions.
- [ ] Rebuild the candidate and verify the unchanged CMS homepage, then continue
  the full login/create/edit/view journey; record subsequent blockers separately.

## Upstream

Not submitted. Implementation has not started; this is not a patched task.
Prepare a separate contribution only after implementation, review and recorded tests.
The user handles authentication and pushes; no upstream submission yet.

[Overall plan](README.md) · [Runtime completion](01-fiber-generators.md) ·
[Installation evidence](../../../docs/receipts/2026-11-12-tracker-landing.md)
