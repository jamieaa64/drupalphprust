--TEST--
Autoload static method classes when creating callables
--FILE--
<?php
spl_autoload_register(function($name) {
 if (in_array($name, ['ArrayCallable', 'StringCallable', 'SyntaxCallable'])) {
  eval('class '.$name.' { public static function value() { return "loaded"; } }');
 }
});
$f = Closure::fromCallable(['ArrayCallable', 'value']); echo 'array:', $f(), "\n";
$f = Closure::fromCallable('StringCallable::value'); echo 'string:', $f(), "\n";
$f = SyntaxCallable::value(...); echo 'syntax:', $f(), "\n";
--EXPECT--
array:loaded
string:loaded
syntax:loaded
