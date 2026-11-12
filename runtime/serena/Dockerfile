# Developer tooling only; never copied into the PHP Rust serving image.
FROM php:8.5.7-cli-bookworm@sha256:1ff2cdf2754bcac61128bfda455d418639c0f45a8b1583b4b90f9873a2ba1368 AS php-oracle
FROM rust:bookworm@sha256:e6f2cff35d0f309acf8433b4f05878ed77092602c5065cc81801177da0bb3a9c
ARG RUST_TOOLCHAIN=1.98.1
RUN apt-get update && apt-get install -y --no-install-recommends python3 python3-venv git clang libclang-dev pkg-config libssl-dev libgd-dev libtidy-dev libxslt1-dev cmake && rm -rf /var/lib/apt/lists/*
RUN rustup toolchain install "$RUST_TOOLCHAIN" --profile minimal --component rust-analyzer --component rust-src && rustup default "$RUST_TOOLCHAIN"
COPY requirements.txt /tmp/serena-requirements.txt
RUN python3 -m venv /opt/serena && /opt/serena/bin/pip install --no-cache-dir -r /tmp/serena-requirements.txt
ENV PATH="/opt/serena/bin:/usr/local/cargo/bin:${PATH}"
ENV HOME=/tmp/serena-home CARGO_TARGET_DIR=/tmp/serena-target
# The upstream differential harness launches this executable directly. Keep it
# separate from DDEV web PHP and never install it into the serving image.
RUN apt-get update && apt-get install -y --no-install-recommends libargon2-1 libonig5 libreadline8 libsqlite3-0 libcurl4 libxml2 && rm -rf /var/lib/apt/lists/*
COPY --from=php-oracle /usr/local/bin/php /opt/php-oracle/php.bin
COPY --from=php-oracle /usr/src/php.tar.xz /opt/php-oracle/php-8.5.7.tar.xz
COPY php-oracle.sh /opt/php-oracle/php
RUN tar -xOf /opt/php-oracle/php-8.5.7.tar.xz php-8.5.7/LICENSE > /opt/php-oracle/LICENSE && chmod 755 /opt/php-oracle/php && /opt/php-oracle/php -r 'exit(PHP_VERSION === "8.5.7" && extension_loaded("xmlwriter") && !ini_get("opcache.enable_cli") ? 0 : 1);'
ENV PHP_ORACLE=/opt/php-oracle/php
WORKDIR /var/www/html/.work/php-rust/php-rust
CMD ["sleep", "infinity"]
