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
$manager=\Drupal::service('plugin.manager.sdc');
try { $defs=$manager->getDefinitions(); echo 'definitions:', get_debug_type($defs), ':', is_array($defs) ? count($defs) : 'not-array', "\n"; } catch (\Throwable $e) { echo $e->getMessage(), "\n", $e->getTraceAsString(), "\n"; }
$kernel->shutdown();
