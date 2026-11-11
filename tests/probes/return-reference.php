<?php
class RefStore {
 public array $items=['value'=>1];
 public function &get() { return $this->items; }
 public function &forward() { return $this->get(); }
 public function &throughFinally() { try { return $this->get(); } finally { echo match (2) { 2 => "finally\n" }; } }
 public static function &staticGet() { static $items=['value'=>1];return $items; }
 public static function &staticForward() { return self::staticGet(); }
 public function value() { return [1]; }
 public function &badForward() { return $this->value(); }
}
$s=new RefStore;$a=&$s->forward();$a['value']=2;echo json_encode($s->items),"\n";
$b=&$s->throughFinally();$b['value']=3;echo json_encode($s->items),"\n";
$c=&RefStore::staticForward();$c['value']=4;echo json_encode(RefStore::staticGet()),"\n";
set_error_handler(function($n,$m){echo $m,"\n";});$copy=&$s->badForward();restore_error_handler();echo json_encode($copy),"\n";
function change(&$value){$value['passed']=true;}
change($s->get());echo json_encode($s->items),"\n";
change(RefStore::staticGet());echo json_encode(RefStore::staticGet()),"\n";
set_error_handler(function($n,$m){echo $m,"\n";});change($s->value());restore_error_handler();
function changeTwo(&$one,&$two,$label){$one['label']=$label;$two['label']=$label;}
$other=new RefStore;changeTwo($s->get(),$other->get(),match(2){2=>'two'});echo json_encode([$s->items,$other->items]),"\n";
function changeMany(&...$items){foreach($items as &$item){$item['variadic']=true;}}
changeMany($s->get(),$other->get());echo json_encode([$s->items,$other->items]),"\n";
