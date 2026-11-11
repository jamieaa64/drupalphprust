<?php
require getcwd().'/vendor/autoload.php';
$m=\Drupal\Core\Render\Markup::create('<strong>working</strong>');
foreach ([false,true] as $useYield) {
 $twig=new \Twig\Environment(new \Twig\Loader\ArrayLoader(['test'=>'before {{ value }} after']), ['use_yield'=>$useYield]);
 echo 'twig:', json_encode($useYield), ':', $twig->render('test',['value'=>$m]), "\n";
}
$s=serialize($m); echo 'serialized:',$s,"\n"; echo 'roundtrip:',unserialize($s),"\n";
$f=new Fiber(function() use($m) { $out=''; foreach((function() use($m){yield $m;})() as $c){$out.=$c;} return $out; });$f->start();echo 'fiber:', $f->getReturn(),"\n";
