#!/usr/bin/env sh
# Upstream parity oracle: no site ini/extensions and no CLI OPcache/JIT.
exec /opt/php-oracle/php.bin -n -d opcache.enable_cli=0 -d opcache.jit=0 "$@"
