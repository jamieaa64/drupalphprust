--TEST--
Reuse the same trait method through diamond composition
--FILE--
<?php
trait Origin { public function value() { return 'origin'; } }
trait Left { use Origin; }
trait Right { use Origin; }
class Diamond { use Left, Right, Origin; }
echo (new Diamond())->value(), "\n";
trait Aliased { use Origin { value as renamed; } }
class DiamondAlias { use Aliased, Origin; }
echo (new DiamondAlias())->renamed(), ':', (new DiamondAlias())->value(), "\n";
--EXPECT--
origin
origin:origin
