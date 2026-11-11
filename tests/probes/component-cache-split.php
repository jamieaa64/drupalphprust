<?php
$loader = require getcwd() . '/vendor/autoload.php';
chdir('web');
$request = \Symfony\Component\HttpFoundation\Request::create('http://localhost/');
$kernel = \Drupal\Core\DrupalKernel::createFromRequest($request, $loader, 'prod');
$kernel->boot();
$kernel->preHandle($request);
if ((int) \Drupal::database()->query('SELECT 1')->fetchField() !== 1) { throw new RuntimeException('Database failed'); }
echo json_encode(['drupal' => \Drupal::VERSION, 'database_driver' => \Drupal::database()->driver(),
    'site_name' => \Drupal::config('system.site')->get('name'), 'theme' => \Drupal::config('system.theme')->get('default')]), "\n";
$raw=\Drupal::database()->query("SELECT data FROM {cache_discovery} WHERE cid = :cid", [':cid'=>'component_plugins'])->fetchField();
echo 'raw:', get_debug_type($raw), ':', strlen($raw), ':', hash('sha256',$raw), "\n";
echo substr($raw,0,160), "\n";
restore_error_handler();
$value=unserialize($raw);
echo 'decoded:',get_debug_type($value),"\n";
$path=getcwd().'/../.ddev/component-fixtures.json';
if (is_array($value)) { $fixtures=[]; foreach($value as $key=>$definition) { $fixtures[$key]=base64_encode(serialize($definition)); } file_put_contents($path,json_encode($fixtures)); }
else { foreach(json_decode(file_get_contents($path),true) as $key=>$serialized) { $v=@unserialize(base64_decode($serialized)); echo $key, ':',get_debug_type($v),"\n"; } }
$kernel->shutdown();
