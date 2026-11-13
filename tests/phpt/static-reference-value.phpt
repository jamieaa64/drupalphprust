--TEST--
Static calls copy reference returns in value context
--FILE--
<?php
class StaticReferenceValue {
    public static function &get(&$value) { return $value; }
    public static function &forward(&$value) { return self::get($value); }
}
$source=['key'=>'original'];
for($i=0;$i<2;$i++) {
    $copy=StaticReferenceValue::get($source);
    echo isset($copy['key']) ? 'present' : 'missing',"\n";
    $copy['key']='copy';
    echo $source['key'],"\n";
}
$class=StaticReferenceValue::class;$method='get';
foreach (['named','dynamic-class','dynamic-method','both-dynamic'] as $mode) {
    $source=['key'=>$mode];
    $copy=match($mode) {
        'named'=>StaticReferenceValue::get(value:$source),
        'dynamic-class'=>$class::get($source),
        'dynamic-method'=>StaticReferenceValue::$method($source),
        default=>$class::$method($source),
    };
    $copy['key']='copy';echo $source['key'],"\n";
}
$alias=&StaticReferenceValue::get($source);$alias['key']='alias';echo $source['key'],"\n";
$alias=&$class::$method($source);$alias['key']='dynamic-alias';echo $source['key'],"\n";
$alias=&StaticReferenceValue::forward($source);$alias['key']='forward';echo $source['key'],"\n";
$copy=StaticReferenceValue::forward($source);$copy['key']='copy';echo $source['key'],"\n";

class R {static $a=['x'=>'original'];static function &get($unused=0){echo "get\n";return self::$a;}static function &forward(){return self::get();}static function plain(){echo "plain\n";return 1;}}
function cls(){echo "class\n";return R::class;}
function meth(){echo "method\n";return 'get';}
function targetKey(){echo "key\n";return 'ref';}
$c=R::class;$m='get';
foreach([0,1,2,3] as $i){if($i===0)$v=R::get(unused:1);if($i===1)$v=$c::$m(...[1]);if($i===2)$v=R::forward();if($i===3)$v=cls()::{meth()}();$v['x']='copy';echo R::$a['x'],"\n";}
$a=[];$a[targetKey()]=&cls()::{meth()}(unused:2);$a['ref']['x']='alias';echo R::$a['x'],"\n";
$v=&$c::$m(...[3]);$v['x']='dynamic';echo R::$a['x'],"\n";
$v=&R::forward();$v['x']='forward';echo R::$a['x'],"\n";
set_error_handler(function($n,$s){echo $s,"\n";});$v=&R::plain();var_dump($v);

--EXPECT--
present
original
present
original
named
dynamic-class
dynamic-method
both-dynamic
alias
dynamic-alias
forward
forward
get
original
get
original
get
original
class
method
get
original
key
class
method
get
alias
get
dynamic
get
forward
plain
Only variables should be assigned by reference
int(1)
