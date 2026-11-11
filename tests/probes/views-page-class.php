<?php
$loader=require getcwd().'/vendor/autoload.php';chdir('web');
$request=\Symfony\Component\HttpFoundation\Request::create('http://localhost/');
$kernel=\Drupal\Core\DrupalKernel::createFromRequest($request,$loader,'prod');$kernel->boot();
var_dump(class_exists(\Drupal\views\Plugin\views\display\Page::class));
