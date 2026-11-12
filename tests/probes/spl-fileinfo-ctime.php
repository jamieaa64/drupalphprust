<?php
$dir = sys_get_temp_dir() . '/phpr-ctime-' . getmypid();
mkdir($dir);
$file = $dir . '/file';
file_put_contents($file, 'test');
try {
 $info = new SplFileInfo($file);
 var_dump(is_int($info->getCTime()), $info->getCTime() === filectime($file));
 $directory = new SplFileInfo($dir);
 var_dump($directory->getCTime() === filectime($dir));
 $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
 var_dump($it->getCTime() === filectime($file));
 try { (new SplFileInfo($dir.'/absent'))->getCTime(); }
 catch (RuntimeException $e) { echo str_replace($dir, 'FIXTURE', $e->getMessage()), "\n"; }
} finally { unlink($file); rmdir($dir); }
