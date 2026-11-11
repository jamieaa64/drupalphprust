--TEST--
Namespaced and dynamic builtins honor Countable object dispatch
--FILE--
<?php
namespace Probe;
class Counter implements \Countable { public function count(): int { return 7; } }
$v = new Counter();
echo 'instance:', ($v instanceof \Countable ? 'yes' : 'no'), "\n";
foreach (['qualified' => fn() => \count($v), 'fallback' => fn() => count($v), 'size' => fn() => sizeof($v), 'case' => fn() => COUNT($v), 'spread' => fn() => \count(...[$v]), 'callback' => fn() => \call_user_func('count', $v)] as $name => $f) {
    try { echo $name, ':', $f(), "\n"; } catch (\Throwable $e) { echo get_class($e), ':', $e->getMessage(), "\n"; }
}
--EXPECT--
instance:yes
qualified:7
fallback:7
size:7
case:7
spread:7
callback:7
