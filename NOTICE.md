# Provenance and licenses

PHP Rust patch files modify code from https://github.com/francescotinti/php-rust
at the pinned revision in runtime/manifest.json. Preserve its copyright and PHP
License 3.01 notices; a copy is at runtime/licenses/PHP-Rust-LICENSE. Runtime Docker
images also copy upstream LICENSE into their image documentation.

No Drupal core/vendor tree, One Line Installer implementation, database or private
site files are vendored here. They are acquired by their respective setup tools
and retain their own licenses.

No separate license has yet been selected for the original glue and documentation
in this repository. Do not assume MIT/Apache terms or relabel upstream-derived
code when preparing contributions. Confirm licensing with the owner before a
broader release.
