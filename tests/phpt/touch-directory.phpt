--TEST--
Touch directories and existing files without opening them for write
--FILE--
<?php
$dir=sys_get_temp_dir().'/phpr-touch-probe-'.getmypid();mkdir($dir);
try {echo json_encode(touch($dir,1234567890)),"\n";clearstatcache();echo filemtime($dir),"\n";file_put_contents($dir.'/file','kept');echo json_encode(touch($dir.'/file',1234567891)),':',file_get_contents($dir.'/file'),"\n";unlink($dir.'/file');}finally{rmdir($dir);}
--EXPECT--
true
1234567890
true:kept
