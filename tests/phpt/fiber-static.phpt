--TEST--
Native Fiber static operations through dynamic and instance calls
--FILE--
<?php
$class = Fiber::class;
$f = new Fiber(function() use ($class) {
 echo 'current:', ($class::getCurrent() instanceof Fiber ? 'yes' : 'no'), "\n";
 $x = $class::suspend('paused'); echo 'resumed:', $x, "\n";
 $method='suspend'; $x=$class::$method(...['spread']); echo 'spread:', $x, "\n";
 $x=$class::suspend(value: 'named'); echo 'named:', $x, "\n";
 return 'done';
});
echo 'start:', $f->start(), "\n";
echo 'next:', $f->resume('one'), "\n";
echo 'next:', $f->resume('two'), "\n";
$f->resume('three'); echo 'return:', $f->getReturn(), "\n";
foreach ([fn() => $class::getCurrent(1), fn() => $class::getCurrent(nope: 1), fn() => $class::suspend(1, 2)] as $bad) {
 $check = new Fiber(function() use ($bad) { try { $bad(); } catch (Throwable $e) { echo get_class($e), "\n"; } });
 $check->start();
}
$f = new Fiber(function() { $current = Fiber::getCurrent(); echo 'instance:', $current->suspend('object'), "\n"; });
echo 'object-start:', $f->start(), "\n"; $f->resume('continued');
--EXPECT--
start:current:yes
paused
next:resumed:one
spread
next:spread:two
named
named:three
return:done
ArgumentCountError
Error
ArgumentCountError
object-start:instance:object
continued
