<?php
class Piece {
 public function __construct(public string $value) {}
 public function __toString(): string { echo 'convert:', $this->value, "\n"; return $this->value; }
}
$a=['nested'=>['value'=>'start']];
$a['nested']['value'].=new Piece('right');
echo $a['nested']['value'],"\n";
$a['value']=new Piece('left'); $a['value'].=new Piece('right'); echo $a['value'],"\n";
$r=&$a['nested']['value']; $a['nested']['value'].=new Piece('tail'); echo $r,"\n";
$o=(object)['items'=>['value'=>'start']]; $o->items['value'].=new Piece('right');echo $o->items['value'],"\n";
class MutatingPiece { public function __toString(): string { global $a; $a['sibling']='kept'; return 'change'; } }
$a['nested']['value'].=new MutatingPiece; echo json_encode($a),"\n";
class ThrowingPiece { public function __toString(): string { throw new RuntimeException('stop'); } }
try { $a['nested']['value'].=new ThrowingPiece; } catch (RuntimeException $e) { echo $e->getMessage(),':',$a['nested']['value'],"\n"; }
$a=['x'=>'a']; $copy=[];
$v=new class {function __toString(){global $a,$copy; $copy=$a;return 'b';}};
$a['x'].=$v;echo json_encode([$a,$copy]), "\n"; $a['x']='later';echo json_encode([$a,$copy]), "\n";
$a=['x'=>'a'];$v=new class {function __toString(){global $a;$a=['y'=>'changed'];return 'b';}};
$a['x'].=$v;echo json_encode($a), "\n";
class OffsetBag implements ArrayAccess {
 private array $data=['x'=>'a'];
 public function offsetExists($k):bool{return isset($this->data[$k]);}
 public function offsetGet($k):mixed{echo 'get:', $k, "\n";return $this->data[$k];}
 public function offsetSet($k,$v):void{echo 'set:',$k,':',$v,"\n";$this->data[$k]=$v;}
 public function offsetUnset($k):void{unset($this->data[$k]);}
}
$bag=new OffsetBag;$bag['x'].='b';$bag['x'].=new Piece('c');
$a=['x'=>'a']; $copy=[];
$v=new class {function __toString(){global $a,$copy; echo 'internal-ref:',json_encode(ReflectionReference::fromArrayElement($a,'x')!==null),"\n"; $copy=$a; $copy['x']='copy'; return 'b';}};
$a['x'].=$v;echo json_encode([$a,$copy]),"\n";echo 'after-ref:',json_encode(ReflectionReference::fromArrayElement($a,'x')!==null),"\n";
