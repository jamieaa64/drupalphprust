<?php
$loader = require getcwd() . '/vendor/autoload.php';
$loader->addPsr4('Drupal\\canvas\\', getcwd() . '/web/modules/contrib/canvas/src');
$id = 'Drupal\\canvas\\ComponentSource\\ComponentSourceManager';
$regex = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*+)++$/';
echo 'match:', json_encode(preg_match($regex, $id)), ':', preg_last_error_msg(), "\n";
echo 'exists:', json_encode(class_exists($id)), "\n";
$c = new Symfony\Component\DependencyInjection\ContainerBuilder();
$c->setDefinition('default_plugin_manager', (new Symfony\Component\DependencyInjection\Definition())->setAbstract(true));
$d = new Symfony\Component\DependencyInjection\ChildDefinition('default_plugin_manager');
$c->setDefinition($id, $d);
(new Symfony\Component\DependencyInjection\Compiler\ResolveClassPass())->process($c);
echo 'class:', json_encode($d->getClass()), "\n";
(new Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass())->process($c);
echo 'resolved:', json_encode($c->getDefinition($id)->getClass()), "\n";
