# syntax=docker/dockerfile:1
# Build once per architecture; workspace restores only copy the resulting binaries.
FROM rust:bookworm@sha256:e6f2cff35d0f309acf8433b4f05878ed77092602c5065cc81801177da0bb3a9c AS build
ARG PHPR_REF=72f688c3183b79858c01d250ce3999d86e143544
ARG CARGO_BUILD_JOBS=2
RUN apt-get update && apt-get install -y --no-install-recommends clang libclang-dev pkg-config libssl-dev libgd-dev libtidy-dev libxslt1-dev libxml2-dev cmake && rm -rf /var/lib/apt/lists/*
WORKDIR /src
RUN git init && git remote add origin https://github.com/francescotinti/php-rust.git && git fetch --depth=1 origin "$PHPR_REF" && git checkout --detach FETCH_HEAD
RUN cd /src/php-rust && rustup show active-toolchain
COPY patches/pdo-mysql.patch /tmp/pdo-mysql.patch
RUN git apply --check /tmp/pdo-mysql.patch && git apply /tmp/pdo-mysql.patch
COPY patches/recursive-callback-iterator.patch /tmp/iterator.patch
RUN git apply --check /tmp/iterator.patch && git apply /tmp/iterator.patch
COPY patches/trait-autoload-case.patch /tmp/trait.patch
RUN git apply --check /tmp/trait.patch && git apply /tmp/trait.patch
COPY patches/trait-diamond.patch /tmp/diamond.patch
RUN git apply --check /tmp/diamond.patch && git apply /tmp/diamond.patch
COPY patches/unserialize-private-fields.patch /tmp/unserialize.patch
RUN git apply --check /tmp/unserialize.patch && git apply /tmp/unserialize.patch
COPY patches/builtin-object-dispatch.patch /tmp/builtin.patch
RUN git apply --check /tmp/builtin.patch && git apply /tmp/builtin.patch
COPY patches/callable-method-arguments.patch /tmp/callable.patch
RUN git apply --check /tmp/callable.patch && git apply /tmp/callable.patch
COPY patches/callable-autoload.patch /tmp/callable-autoload.patch
RUN git apply --check /tmp/callable-autoload.patch && git apply /tmp/callable-autoload.patch
COPY patches/cross-unit-reference-call.patch /tmp/reference-call.patch
RUN git apply --check /tmp/reference-call.patch && git apply /tmp/reference-call.patch
COPY patches/fiber-static-dispatch.patch /tmp/fiber.patch
RUN git apply --check /tmp/fiber.patch && git apply /tmp/fiber.patch
COPY patches/fiber-callback-trampoline.patch /tmp/trampoline.patch
RUN git apply --check /tmp/trampoline.patch && git apply /tmp/trampoline.patch
COPY patches/dom-doctype.patch /tmp/dom-doctype.patch
RUN git apply --check /tmp/dom-doctype.patch && git apply /tmp/dom-doctype.patch
COPY patches/strtr-stringable-map.patch /tmp/strtr.patch
RUN git apply --check /tmp/strtr.patch && git apply /tmp/strtr.patch
COPY patches/concat-stringable.patch /tmp/concat-stringable.patch
RUN git apply --check /tmp/concat-stringable.patch && git apply /tmp/concat-stringable.patch
COPY patches/dynamic-reference-builtin.patch /tmp/dynamic-reference-builtin.patch
RUN git apply --check /tmp/dynamic-reference-builtin.patch && git apply /tmp/dynamic-reference-builtin.patch
COPY patches/enum-serialization.patch /tmp/enum-serialization.patch
RUN git apply --check /tmp/enum-serialization.patch && git apply /tmp/enum-serialization.patch
COPY patches/static-reference.patch /tmp/static-reference.patch
RUN git apply --check /tmp/static-reference.patch && git apply /tmp/static-reference.patch
COPY patches/field-concat-stringable.patch /tmp/field-concat.patch
RUN git apply --check /tmp/field-concat.patch && git apply /tmp/field-concat.patch
COPY patches/arrayaccess-reference.patch /tmp/arrayaccess-reference.patch
RUN git apply --check /tmp/arrayaccess-reference.patch && git apply /tmp/arrayaccess-reference.patch
COPY patches/builtin-reflection.patch /tmp/builtin-reflection.patch
RUN git apply --check /tmp/builtin-reflection.patch && git apply /tmp/builtin-reflection.patch
COPY patches/return-reference.patch /tmp/return-reference.patch
RUN git apply --check /tmp/return-reference.patch && git apply /tmp/return-reference.patch
COPY patches/touch-directory.patch /tmp/touch-directory.patch
RUN git apply --check /tmp/touch-directory.patch && git apply /tmp/touch-directory.patch
COPY patches/empty-class-autoload.patch /tmp/empty-class-autoload.patch
RUN git apply --check /tmp/empty-class-autoload.patch && git apply /tmp/empty-class-autoload.patch
COPY patches/server-preconnect.patch /tmp/server-preconnect.patch
RUN git apply --check /tmp/server-preconnect.patch && git apply /tmp/server-preconnect.patch
COPY patches/object-vars-snapshot.patch /tmp/object-vars-snapshot.patch
RUN git apply --check /tmp/object-vars-snapshot.patch && git apply /tmp/object-vars-snapshot.patch
COPY patches/stream-constants.patch /tmp/stream-constants.patch
RUN git apply --check /tmp/stream-constants.patch && git apply /tmp/stream-constants.patch
COPY patches/session-regenerate-cookie.patch /tmp/session-regenerate-cookie.patch
RUN git apply --check /tmp/session-regenerate-cookie.patch && git apply /tmp/session-regenerate-cookie.patch
COPY patches/preg-byte-offset.patch /tmp/preg-byte-offset.patch
RUN git apply --check /tmp/preg-byte-offset.patch && git apply /tmp/preg-byte-offset.patch
COPY patches/chained-magic-argument.patch /tmp/chained-magic-argument.patch
RUN git apply --check /tmp/chained-magic-argument.patch && git apply /tmp/chained-magic-argument.patch
COPY patches/static-property-reference.patch /tmp/static-property-reference.patch
RUN git apply --check /tmp/static-property-reference.patch && git apply /tmp/static-property-reference.patch
COPY patches/spl-fileinfo-ctime.patch /tmp/spl-fileinfo-ctime.patch
RUN git apply --check /tmp/spl-fileinfo-ctime.patch && git apply /tmp/spl-fileinfo-ctime.patch
COPY patches/xmlwriter-memory.patch /tmp/xmlwriter-memory.patch
RUN git apply --check /tmp/xmlwriter-memory.patch && git apply /tmp/xmlwriter-memory.patch
WORKDIR /src/php-rust
ARG RUNTIME_PROFILE=release
RUN --mount=type=cache,target=/usr/local/cargo/registry --mount=type=cache,target=/usr/local/cargo/git --mount=type=cache,target=/out CARGO_TARGET_DIR=/out cargo build --locked --profile "$RUNTIME_PROFILE" -p php-cli -p php-server && mkdir /dist && cp "/out/$RUNTIME_PROFILE/phpr" "/out/$RUNTIME_PROFILE/php-server" /dist/
FROM debian:bookworm-slim@sha256:734a72b3d6142a71993b4bc580ca01d8ca687f77a2c422d96a961d3376b179bc
RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates libssl3 libgd3 libtidy5deb1 libxslt1.1 libxml2 && mkdir -p /opt/phpr/lib && cp -L /usr/lib/*/libtidy.so.5deb1 /opt/phpr/lib/ && rm -rf /var/lib/apt/lists/*
COPY --from=build /dist/phpr /dist/php-server /usr/local/bin/
COPY --from=build /src/LICENSE /usr/share/doc/php-rust/LICENSE
LABEL org.opencontainers.image.source="https://github.com/francescotinti/php-rust"
