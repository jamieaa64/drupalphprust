--TEST--
Nested render Fibers suspend through a Generator without losing state
--FILE--
<?php
function render($callback) {
 $f=new Fiber(static fn()=>$callback());$f->start();
 for($i=0;!$f->isTerminated() && $i<3;$i++){
  echo 'state:',(int)$f->isStarted(),(int)$f->isSuspended(),(int)$f->isRunning(),':',(int)($f===Fiber::getCurrent()),"\n";
  if($f->isSuspended()){if(Fiber::getCurrent())Fiber::suspend();$f->resume();}
 }
 if($f->isTerminated())return $f->getReturn();
 echo "STUCK\n";
}
function generator(){yield render(function(){return 'ok';});}
echo render(function(){foreach(generator() as $v)echo $v;return 'done';}),"\n";
function suspendedGenerator(){yield render(function(){Fiber::suspend();return 'ok';});}
echo render(function(){foreach(suspendedGenerator() as $v)echo $v;return 'done';}),"\n";
--EXPECT--
okdone
state:110:0
state:110:0
okdone
