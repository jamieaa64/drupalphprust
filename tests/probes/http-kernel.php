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
try {
$response = $kernel->handle($request, 1, FALSE);
echo 'status:', $response->getStatusCode(), "\n";
} catch (\Throwable $e) { echo $e->getMessage(), "\n", $e->getTraceAsString(), "\n"; }
$kernel->shutdown();
