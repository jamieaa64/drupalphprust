# Named runtime routes — 2026-11-12

Runtime setup now derives the host/scheme from DDEV's `describe -j` primary URL.
It adds `php.<host>` to the web service while retaining DDEV aliases, and routes
`rust.<host>` exclusively to phpr on container port 8090. The existing loopback
port remains for Coder/private tunnels. The installer prints both named URLs;
`.ddev/.runtime-urls` is an ignored local receipt.

## Checks performed

- Eight installer/helper tests pass, including distinct service hostnames, labelled
  URL output, loopback-port persistence and protection of unowned compose files.
- Five tracker checks, repository contracts and snapshot freshness pass.
- A disposable PHP-only DDEV fixture used separate response markers to attempt a
  routing smoke test. It used standard PHP as a stand-in for the Rust-side service;
  no Drupal/runtime compatibility claim was intended.
- DDEV's actual `utility compose-config` succeeds and resolves web to
  `phprust-route-check.ddev.site,php.phprust-route-check.ddev.site` and phpr to
  `rust.phprust-route-check.ddev.site`, with 80/443 mapped to 8090 for phpr.
- Starting the fixture stopped at hostname resolution: DDEV requested administrator
  authentication to add the base test hostname. No password or hosts change was
  performed. The disposable fixture was stopped/unlisted afterwards.

## Still unverified

Live HTTP/TLS routing, browser access and Drupal trusted-host acceptance on an
installed site. The existing user's site was not changed. Coder app URLs still
need its routing integration; these DDEV hostnames are not public Coder URLs.

After publishing and updating an installed checkout, rerun
`bash scripts/enable-runtime.sh dev-release` from that installed project to apply
routes using its already-built image. DDEV may request normal hostname setup
interactively. Then open both labelled URLs and verify the runtime behind each.
