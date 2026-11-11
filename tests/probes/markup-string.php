<?php
require getcwd().'/vendor/autoload.php';
$m=\Drupal\Core\Render\Markup::create('<strong>working</strong>');
echo 'method:', $m->__toString(), "\n";
echo 'cast:', (string)$m, "\n";
echo 'echo:', $m, "\n";
echo 'concat:', 'prefix'.$m, "\n";
echo 'implode:', implode('',[$m]), "\n";
$out=''; $out.=$m; echo 'append:', $out, "\n";
function chunks($m) { yield $m; }
$out=''; foreach(chunks($m) as $chunk) { $out.=$chunk; } echo 'yield-append:', $out, "\n";
$a=['nested'=>['value'=>'before']]; $a['nested']['value'].=$m; echo 'array-append:', $a['nested']['value'], "\n";
$o=(object)['items'=>['value'=>'before']]; $o->items['value'].=$m; echo 'property-array-append:', $o->items['value'], "\n";
