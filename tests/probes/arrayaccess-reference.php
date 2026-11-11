<?php
class RefBag implements ArrayAccess {
 public array $data=['nested'=>['value'=>1]];
 public function offsetExists($k):bool{return isset($this->data[$k]);}
 public function &offsetGet($k):mixed{return $this->data[$k];}
 public function offsetSet($k,$v):void{$this->data[$k]=$v;}
 public function offsetUnset($k):void{unset($this->data[$k]);}
}
$b=new RefBag; $b['nested']['value']=2; $b['nested']['added']='yes';echo json_encode($b->data),"\n";
$w=new WeakMap;$key=new stdClass;$w[$key]=['value'=>1];$w[$key]['value']=2;$w[$key]['added']='yes';echo json_encode($w[$key]),"\n";
echo 'compound:',($b['nested']['value']+=3),':post:',($b['nested']['value']++),':stored:',$b['nested']['value'],"\n";
echo 'method-ref:',json_encode((new ReflectionMethod(WeakMap::class,'offsetGet'))->returnsReference()),"\n";
set_error_handler(function($n,$m){echo $m,"\n";});$copy=&$w->offsetGet($key);restore_error_handler();$copy['value']=9;echo json_encode($w[$key]),"\n";
