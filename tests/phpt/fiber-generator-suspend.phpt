--TEST--
Fiber suspension inside a generator preserves both continuations
--FILE--
<?php
function suspended_values() {
    echo "generator:start\n";
    $value = Fiber::suspend('waiting');
    yield 'first' => $value;
    yield 'second' => 'tail';
    return 'generator:done';
}
$fiber = new Fiber(function () {
    $generator = suspended_values();
    foreach ($generator as $key => $value) {
        echo $key, ':', $value, "\n";
    }
    echo $generator->getReturn(), "\n";
    return 'fiber:done';
});
echo 'suspended:', $fiber->start(), "\n";
$fiber->resume('resumed');
echo $fiber->getReturn(), "\n";

--EXPECT--
suspended:generator:start
waiting
first:resumed
second:tail
generator:done
fiber:done
