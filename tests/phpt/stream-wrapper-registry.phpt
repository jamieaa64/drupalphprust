--TEST--
Registered stream wrappers appear in insertion order across remove and re-register
--FILE--
<?php
class RegistryProbe { public $context; }
function listed() { echo json_encode(array_values(array_filter(stream_get_wrappers(),fn($s)=>str_starts_with($s,'registryprobe')))),"\n"; }
listed();
var_dump(stream_wrapper_register('registryprobez', RegistryProbe::class));
var_dump(stream_wrapper_register('registryprobea', RegistryProbe::class));
listed();
var_dump(stream_wrapper_unregister('registryprobez'));
listed();
var_dump(stream_wrapper_register('registryprobez', RegistryProbe::class));
listed();
foreach (['registryprobea','registryprobez'] as $s) {
 if(in_array($s,stream_get_wrappers(),true)) stream_wrapper_unregister($s);
 var_dump(stream_wrapper_register($s,RegistryProbe::class));
}
listed();

--EXPECT--
[]
bool(true)
bool(true)
["registryprobez","registryprobea"]
bool(true)
["registryprobea"]
bool(true)
["registryprobea","registryprobez"]
bool(true)
bool(true)
["registryprobea","registryprobez"]
