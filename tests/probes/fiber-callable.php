<?php
function run(callable $callable) {
 $fiber = new Fiber(static fn () => $callable());
 $fiber->start();
 echo 'return:', $fiber->getReturn(), "\n";
}
run(fn() => 'closure');
class Render { public function go() { return 'method'; } }
run([new Render(), 'go']);
