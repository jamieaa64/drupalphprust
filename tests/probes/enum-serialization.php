<?php
enum PureProbe { case First; }
enum BackedProbe: string { case One = 'one'; }
foreach ([PureProbe::First, BackedProbe::One, [PureProbe::First, PureProbe::First]] as $value) {
 $wire=serialize($value); echo $wire, "\n";
 $copy=unserialize($wire); echo 'same:', $copy === $value ? 'yes' : 'no', "\n";
}
spl_autoload_register(function($class) { if ($class === 'LoadedEnum') { eval('enum LoadedEnum { case Yes; }'); } });
$e=unserialize('E:14:"LoadedEnum:Yes";'); echo 'autoload:', $e === LoadedEnum::Yes ? 'yes' : 'no', "\n";
