--TEST--
Nested traits preserve original spelling for case-sensitive autoloaders
--FILE--
<?php
spl_autoload_register(function ($name) {
    echo 'autoload:', $name, "\n";
    if ($name === 'Probe\\OuterTrait') {
        eval('namespace Probe; trait OuterTrait { use InnerTrait; }');
    } elseif ($name === 'Probe\\InnerTrait') {
        eval('namespace Probe; trait InnerTrait { public function value() { return "ok"; } }');
    } elseif ($name === 'Probe\\AliasTrait') {
        eval('namespace Probe; trait AliasTrait { use InnerTrait { InnerTrait::value as parentValue; } public function value() { return $this->parentValue()."-alias"; } }');
    }
});
eval('class Consumer { use \\Probe\\OuterTrait; }');
echo (new Consumer())->value(), "\n";
eval('class Aliased { use \\Probe\\AliasTrait; }');
echo (new Aliased())->value(), "\n";
--EXPECT--
autoload:Probe\OuterTrait
autoload:Probe\InnerTrait
ok
autoload:Probe\AliasTrait
ok-alias
