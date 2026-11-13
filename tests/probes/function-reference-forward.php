<?php
namespace { function &forwardStore($key='default',...$rest) { static $s=[]; return $s[$key]; }
function &directForward(){return forwardStore();}
function &namedForward(){return forwardStore(key:'named');}
function &spreadForward(){return forwardStore(...['spread',1,2]);}
function plainValue(){echo "value\n";return ['copy'];}
function &badForward(){return plainValue();}
function &dynamicForward($f){return $f();}
}
namespace ForwardTest {class Wrapper {public function &get(){return forwardStore('batch');}}}
namespace {
$a=&directForward();$a='direct';echo forwardStore(),"\n";
$b=&namedForward();$b='named';echo forwardStore('named'),"\n";
$c=&spreadForward();$c='spread';echo forwardStore('spread'),"\n";
$o=new \ForwardTest\Wrapper;$d=&$o->get();$d=['yes'];echo json_encode(forwardStore('batch')),"\n";
$e=&dynamicForward('directForward');$e='dynamic';echo forwardStore(),"\n";
set_error_handler(function($n,$m){echo 'notice:',$m,"\n";});$copy=&badForward();echo json_encode($copy),"\n";restore_error_handler();
eval('function &lateStore(){static $s=[];return $s;}');
eval('namespace LateForward; function &get(){try {return lateStore();}finally{echo "finally\\n";}}');
$f=&\LateForward\get();$f=['late'];echo json_encode(lateStore()),"\n";
}

namespace ForwardReview {
use Fiber;
use Exception;
function &forward($f){return $f();}
class R {public $v='initial';public function &get(){return $this->v;}public function &__invoke(){return $this->v;}}
$o=new R;
foreach([[$o,'get'],$o,$o->get(...)] as $f){$v=&forward($f);$v.='!';echo $o->v,"\n";}
$x='start';$c=function &()use(&$x){return $x;};$v=&forward($c);$v='closure';echo $x,"\n";
function &late(){Fiber::suspend('pause');static $a='before';return $a;}
$f=new Fiber(function(){ $a=&forward(__NAMESPACE__ . '\\late');$a='after';return $a;});echo $f->start(),"\n";$f->resume();echo $f->getReturn(),"\n";
function &bad(){return strlen('abc');}set_error_handler(function($n,$s){echo $s,"\n";throw new Exception('notice');});try{$b=&bad();}catch(Exception $e){echo $e->getMessage(),"\n";}

}
