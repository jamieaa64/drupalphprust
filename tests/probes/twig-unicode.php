<?php
require getcwd().'/vendor/autoload.php';
$twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader());
foreach (["<b>·</b>{{ value }}", "{{ first }} · {{ second }}", "{{ 'Camps'|upper }}", "·{{ camps_link }}"] as $text) {
 try { $tokens=$twig->tokenize(new \Twig\Source($text, 'probe')); echo "ok\n"; }
 catch (Throwable $e) { echo $e->getMessage(), "\n"; }
}
