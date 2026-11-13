<?php
// Candidate-only regression: this unsupported boundary must fail cleanly.
function guardedValues(): Generator {
    try { Fiber::suspend('unsupported'); yield 1; }
    finally { echo "generator-finally\n"; }
}
$f = new Fiber(function () {
    try {
        array_map(function ($n) {
            try { foreach (guardedValues() as $v) {} return $n; }
            finally { echo "callback-finally\n"; }
        }, [1]);
    } catch (Error $e) { echo $e->getMessage(), "\n"; }
    return 'done';
});
$f->start();
var_dump($f->isTerminated());
echo $f->getReturn(), "\n";
