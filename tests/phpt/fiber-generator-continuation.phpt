--TEST--
fiber generator continuation
--FILE--
<?php
function pumpContinuation(Fiber $fiber): void {
    echo 'start:', json_encode($fiber->start()), "\n";
    for ($i = 0; !$fiber->isTerminated() && $i < 8; $i++) {
        if (!$fiber->isSuspended()) { echo "invalid-state\n"; return; }
        echo 'resume:', json_encode($fiber->resume('resume-' . $i)), "\n";
    }
    echo 'return:', json_encode($fiber->getReturn()), "\n";
}
function continuationValues(array &$events): Generator {
    try {
        $events[] = 'before';
        $first = Fiber::suspend('first');
        $events[] = $first;
        yield 'a' => 10;
        $sent = yield 'b' => Fiber::suspend('second');
        $events[] = $sent;
        return 'generator-return';
    }
    finally { $events[] = 'finally'; }
}
$events = [];
pumpContinuation(new Fiber(function () use (&$events) {
    $out = [];
    foreach (continuationValues($events) as $key => $value) { $out[$key] = $value; }
    return $out;
}));
echo json_encode($events), "\n";
function continuationDelegate(array &$events): Generator {
    return yield from continuationValues($events);
}
$events = [];
pumpContinuation(new Fiber(function () use (&$events) {
    $g = continuationDelegate($events);
    echo 'current:', json_encode($g->current()), ':', $g->key(), "\n";
    echo 'send:', json_encode($g->send('sent-one')), "\n";
    $g->send('sent-two');
    return $g->getReturn();
}));
echo json_encode($events), "\n";
$events = [];
pumpContinuation(new Fiber(function () use (&$events) {
    $g = continuationValues($events);
    $g->next();
    echo 'next:', json_encode($g->current()), "\n";
    $g->next();
    return $g->getReturn();
}));
echo json_encode($events), "\n";
function continuationThrow(): Generator {
    try { Fiber::suspend('throw'); throw new RuntimeException('expected'); yield; }
    finally { echo "exception-finally\n"; }
}
pumpContinuation(new Fiber(function () {
    try { foreach (continuationThrow() as $value) {} }
    catch (RuntimeException $e) { return $e->getMessage(); }
}));
function continuationDepth(Fiber $f) { return $f->resume('different-depth'); }
$events = [];
$f = new Fiber(function () use (&$events) { foreach (continuationValues($events) as $v) {} return 'done'; });
echo 'depth-start:', $f->start(), "\n";
echo 'depth-next:', continuationDepth($f), "\n";
echo 'depth-end:', json_encode(continuationDepth($f)), ':', $f->getReturn(), "\n";
// A nested Fiber owns independent suspension state even under a native callback.
$outer = new Fiber(function () {
    return array_map(function ($n) {
        $inner = new Fiber(function () {
            foreach ((function () { Fiber::suspend('inner'); yield 5; })() as $v) { return $v; }
        });
        echo 'nested:', $inner->start(), "\n";
        $inner->resume();
        return $inner->getReturn();
    }, [1]);
});
$outer->start();
echo 'nested-return:', json_encode($outer->getReturn()), "\n";
--EXPECT--
start:"first"
resume:"second"
resume:null
return:{"a":10,"b":"resume-1"}
["before","resume-0",null,"finally"]
start:current:"first"
resume:10:a
send:"second"
resume:"resume-1"
null
return:"generator-return"
["before","resume-0","sent-two","finally"]
start:"first"
resume:"second"
resume:next:"resume-1"
null
return:"generator-return"
["before","resume-0",null,"finally"]
start:"throw"
resume:exception-finally
null
return:"expected"
depth-start:first
depth-next:second
depth-end:null:done
nested:inner
nested-return:[5]
