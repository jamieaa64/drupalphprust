<?php
$a=['z'=>2,'a'=>1]; $f='ksort'; var_export($f($a)); echo ':', json_encode($a), "\n";
$o=(object)['items'=>[3,1,2]]; $f='sort'; $f($o->items); echo json_encode($o->items), "\n";
$a=['items'=>[1,2]]; $f='array_pop'; echo $f($a['items']), ':', json_encode($a), "\n";
$f='KSORT'; $a=['b'=>1,'a'=>2]; $f($a); echo json_encode($a), "\n";
