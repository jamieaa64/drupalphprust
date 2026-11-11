--TEST--
Suspend and resume through user callback trampolines while preserving references
--FILE--
<?php
$f = new Fiber(function() { return call_user_func_array(function() { Fiber::suspend('callback'); return 'callback-done'; }, []); });
echo 'callback-start:', $f->start(), "\n"; $f->resume(); echo $f->getReturn(), "\n";
foreach ([
 'direct' => fn($cb) => call_user_func($cb),
 'dynamic' => function($cb) { $name='call_user_func_array'; return $name($cb, []); },
 'spread' => fn($cb) => call_user_func_array(...[$cb, []]),
] as $label => $invoke) {
 $f = new Fiber(fn() => $invoke(function() { $v=Fiber::suspend('pause'); return $v; }));
 echo $label, ':', $f->start(), ':'; $f->resume('resumed'); echo $f->getReturn(), "\n";
}
eval('namespace TrampolineProbe; function run($cb) { return call_user_func_array($cb, []); }');
$f = new Fiber(fn() => TrampolineProbe\run(function() { Fiber::suspend('namespace'); return 'ok'; }));
echo $f->start(), ':'; $f->resume(); echo $f->getReturn(), "\n";
$value='before';
$f = new Fiber(function() use (&$value) { return call_user_func_array(function(&$v) { Fiber::suspend('ref'); $v='after'; return $v; }, [&$value]); });
echo $f->start(), ':'; $f->resume(); echo $value, ':', $f->getReturn(), "\n";
function &shared_trampoline() { static $v = 1; return $v; }
$a =& call_user_func_array('shared_trampoline', []); $a=2; echo 'return-ref:', shared_trampoline(), "\n";
--EXPECT--
callback-start:callback
callback-done
direct:pause:resumed
dynamic:pause:resumed
spread:pause:resumed
namespace:ok
ref:after:after
return-ref:2
