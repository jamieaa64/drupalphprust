--TEST--
Stringable replacement maps preserve conversion and diagnostic order
--FILE--
<?php
namespace Probe;
class Text { public function __construct(private string $value) {} public function __toString(): string { echo 'convert:', $this->value, "\n"; return $this->value; } }
$v=new Text('replacement');
echo strtr('key/key2', ['key'=>$v, 'key2'=>$v]), "\n";
echo strtr('', ['key'=>$v]), "\n";
echo strtr(new Text('key'), ['key'=>$v]), "\n";
echo strtr('unchanged', ['absent'=>$v]), "\n";
echo strtr(0, ['0'=>$v]), "\n";
set_error_handler(function() { echo "WARN\n"; return true; });
echo strtr('x', [''=>'', 'x'=>$v]), "\n";
set_error_handler(function() { throw new \RuntimeException('warning-stop'); });
try { strtr('x', [''=>'', 'x'=>$v]); } catch (\RuntimeException $e) { echo $e->getMessage(), "\n"; }
restore_error_handler(); restore_error_handler();
try { strtr('a', ['a'=>new \stdClass(), 'b'=>$v]); } catch (\Error $e) { echo "conversion:Error\n"; }
--EXPECT--
convert:replacement
convert:replacement
replacement/replacement

convert:key
convert:replacement
replacement
convert:replacement
unchanged
convert:replacement
replacement
WARN
convert:replacement
replacement
warning-stop
conversion:Error
