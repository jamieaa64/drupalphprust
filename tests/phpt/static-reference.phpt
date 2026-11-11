--TEST--
Bind static method reference returns including forwarding and dynamic calls
--FILE--
<?php
class RefParent {
    public static array $data = ['nested' => ['keep' => 1, 'remove' => 2]];
    public static function &value(&$data, $keys, &$exists = null) {
        $ref = &$data;
        foreach ($keys as $key) { $ref = &$ref[$key]; }
        $exists = true;
        return $ref;
    }
    public static function remove(&$data) {
        $ref = &self::value($data, ['nested'], $exists);
        unset($ref['remove']);
        echo 'exists:', json_encode($exists), '\n';
    }
    public static function &storage($label = null) { static $data = []; return $data; }
    public static function copy() { return ['copy']; }
}
class RefChild extends RefParent {
    public static function check() {
        $a = &parent::storage(); $a['parent'] = true;
        $b = &static::storage(); $b['static'] = true;
        $m = 'storage'; $c = &self::$m(); $c['dynamic'] = true;
    }
}
$storage = &RefParent::$data;
RefParent::remove($storage);
RefChild::check();
$class = RefChild::class; $method = 'storage';
$r = &$class::$method(); $r['runtime'] = true;
$t = &RefChild::storage(label: 'named'); $t['named'] = true;
$u = &RefChild::storage(...['spread']); $u['spread'] = true;
echo json_encode(RefParent::$data), "\n";
echo json_encode(RefParent::storage()), "\n";
set_error_handler(function($n, $m) { echo $m, "\n"; });
$copy = &RefParent::copy(); $copy[] = 'changed';
restore_error_handler();
echo json_encode($copy), "\n";
--EXPECT--
exists:true\n{"nested":{"keep":1}}
{"parent":true,"static":true,"dynamic":true,"runtime":true,"named":true,"spread":true}
Only variables should be assigned by reference
["copy","changed"]
