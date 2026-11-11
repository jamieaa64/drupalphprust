<?php
$loader=require getcwd().'/vendor/autoload.php';chdir('web');
$request=\Symfony\Component\HttpFoundation\Request::create('http://127.0.0.1:18090/user/login');
$kernel=\Drupal\Core\DrupalKernel::createFromRequest($request,$loader,'prod');
$response=$kernel->handle($request);echo 'status:',$response->getStatusCode(),"\n",substr($response->getContent(),0,500),"\n";
$kernel->shutdown();
