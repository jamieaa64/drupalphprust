# Developer tooling only; never copied into the PHP Rust serving image.
FROM rust:bookworm@sha256:e6f2cff35d0f309acf8433b4f05878ed77092602c5065cc81801177da0bb3a9c
ARG RUST_TOOLCHAIN=1.98.1
RUN apt-get update && apt-get install -y --no-install-recommends python3 python3-venv git clang libclang-dev pkg-config libssl-dev libgd-dev libtidy-dev libxslt1-dev cmake && rm -rf /var/lib/apt/lists/*
RUN rustup toolchain install "$RUST_TOOLCHAIN" --profile minimal --component rust-analyzer --component rust-src && rustup default "$RUST_TOOLCHAIN"
COPY requirements.txt /tmp/serena-requirements.txt
RUN python3 -m venv /opt/serena && /opt/serena/bin/pip install --no-cache-dir -r /tmp/serena-requirements.txt
ENV PATH="/opt/serena/bin:/usr/local/cargo/bin:${PATH}"
ENV HOME=/tmp/serena-home CARGO_TARGET_DIR=/tmp/serena-target
WORKDIR /var/www/html/.work/php-rust/php-rust
CMD ["sleep", "infinity"]
