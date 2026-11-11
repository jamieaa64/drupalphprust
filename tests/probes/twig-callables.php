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
$m=\Drupal\Core\Render\Markup::create('<strong>working</strong>');
$twig=\Drupal::service('twig');
echo 'drupal-twig:', $twig->createTemplate('before {{ value }} after')->render(['value'=>$m]), "\n";
$renderer=\Drupal::service('renderer');
$build=['#markup'=>'<strong>working</strong>'];
echo 'renderer:', $renderer->renderInIsolation($build), "\n";
foreach (['getFilters','getFunctions','getTests'] as $kind) {
 foreach ($twig->$kind() as $entry) { if (!$entry->getCallable()) continue; try {new \Twig\Util\ReflectionCallable($entry);} catch (\Throwable $e) {echo $kind, ':', $entry->getName(), ':', $e->getMessage(), "\n";} }
}
$kernel->shutdown();
