<?php
eval('function &shared_value($name, $default = []) { static $values = []; if (!isset($values[$name])) $values[$name]=$default; return $values[$name]; }');
$a =& shared_value('probe', []); $a['changed'] = 'yes';
$b =& shared_value('probe', []); echo $b['changed'], "\n";
eval('namespace Probe; function go() { $v =& shared_value("probe", []); $v["namespace"]="yes"; }');
Probe\go(); echo $a['namespace'], "\n";
eval('namespace { function &fallback_ref(){ static $x=1; return $x; } } namespace LocalProbe { $a =& fallback_ref(); $a=2; echo \\fallback_ref(), "\\n"; }');
