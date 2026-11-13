<?php
function extras(){echo "extras\n";return ['b','c'];}
function indexKey(){echo "key\n";return 'v';}
$a=['v'=>['a']]; var_dump(array_push($a[indexKey()],...extras())); echo json_encode($a),"\n";
foreach(['x','y'] as $key){$a[$key]=['base'];array_unshift($a[$key],...[$key]);} echo json_encode($a),"\n";
class SpreadHolder {public array $v=['a']; public static array $s=['v'=>['a']];}
$o=new SpreadHolder;var_dump(array_push($o->v,...['b']));echo json_encode($o->v),"\n";
var_dump(array_unshift(SpreadHolder::$s['v'],...['b']));echo json_encode(SpreadHolder::$s),"\n";
var_dump(array_push(SpreadHolder::$s,...['z']));echo json_encode(SpreadHolder::$s),"\n";
function holder($o){echo "holder\n";return $o;}
array_push(holder($o)->v,...['c']);echo json_encode($o->v),"\n";
try {array_push($a['v'],...['bad'=>'value']);}catch(Error $e){echo $e->getMessage(),"\n";}
echo json_encode($a['v']),"\n";

$a=['v'=>['original']];$ref=&$a['v'];
function replace(&$a){$a['v']=['replacement'];return ['extra'];}
var_dump(array_push($a['v'],...replace($a)));echo json_encode([$a,$ref]),"\n";
function bad(){throw new Exception('bad');}
foreach(['a','b'] as $key){$a[$key]=[$key];try{array_push($a[$key],...bad());}catch(Exception $e){}array_push($a[$key],...['good']);}echo json_encode($a),"\n";
function parts(){yield 'one';yield 'two';}
var_dump(array_push($a['v'],...parts()));echo json_encode($a['v']),"\n";
$a['v']=['one'];$f=new Fiber(function()use(&$a){return array_push($a['v'],...(function(){Fiber::suspend('pause');return ['two'];})());});echo $f->start(),"\n";$f->resume();var_dump($f->getReturn());echo json_encode($a['v']),"\n";
