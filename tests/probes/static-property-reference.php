<?php
class RefPage {
 protected static $render;
 public static function &set(?array &$element = null) {
  if ($element !== null) { static::$render = &$element; }
  return static::$render;
 }
 public static function &get() { return static::$render; }
}
$a=['n'=>1];$ref=&RefPage::set($a);$a['n']=2;
echo json_encode(RefPage::get()),"\n";
$ref['n']=3;echo json_encode($a),"\n";
$b=['n'=>4];RefPage::set($b);$a['n']=5;$b['n']=6;
echo json_encode([RefPage::get(),$ref,$a,$b]),"\n";
class PublicRefs {
 public static $slot;
 static function bind(&$v) { return self::$slot =& $v; }
 static function change(){self::$slot += 2; ++self::$slot;}
}
$n=10;echo PublicRefs::bind($n),"\n";PublicRefs::change();echo $n,"\n";
PublicRefs::$slot=20;echo $n,"\n";
class ChildRefs extends PublicRefs {}
class OwnRefs extends PublicRefs { public static $slot; }
$c=30;$d=40;ChildRefs::$slot=&$c;OwnRefs::$slot=&$d;$c=31;$d=41;
echo json_encode([PublicRefs::$slot,ChildRefs::$slot,OwnRefs::$slot,$n]),"\n";
class HiddenRefs { private static $slot; }
try { HiddenRefs::$slot =& $n; } catch(Error $e) { echo $e->getMessage(),"\n"; }
$path=sys_get_temp_dir().'/phpr-static-ref-'.getmypid().'.php';
file_put_contents($path,'<?php class DeferredRefPage extends RefPage { public static function &bind(&$a) { static::$render = &$a; return static::$render; } }');
try { require $path;$a=['deferred'=>1];$r=&DeferredRefPage::bind($a);$r['deferred']=2;echo json_encode($a),"\n"; } finally {unlink($path);}

class FinallyRefs {
 public static $slot = 1;
 static function &get() { try { return self::$slot; } finally { echo "finally\n"; } }
}
$finallyRef =& FinallyRefs::get(); $finallyRef = 2;
echo FinallyRefs::$slot, "\n";
