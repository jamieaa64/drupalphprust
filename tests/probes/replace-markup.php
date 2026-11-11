<?php
namespace ReplaceProbe;
require getcwd().'/vendor/autoload.php';
$m=\Drupal\Core\Render\Markup::create('<strong>working</strong>');
echo 'direct:',str_replace('TOKEN',$m,'before TOKEN after'),"\n";
echo 'count:',str_replace('TOKEN',$m,'before TOKEN after',$count),':',$count,"\n";
echo 'array:',str_replace(['TOKEN'],[$m],'before TOKEN after'),"\n";
$f='str_replace';echo 'dynamic:',$f('TOKEN',$m,'before TOKEN after'),"\n";
