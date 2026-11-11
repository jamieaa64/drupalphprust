<?php
require getcwd().'/vendor/autoload.php';
foreach (["hello <p>world &amp; more</p>", "héllo <p>κόσμος</p>", "abc\0def<&"] as $input) {
 $s = new \Masterminds\HTML5\Parser\Scanner($input);
 for ($i=0; $i<30; ++$i) {
  $c=$s->current();
  echo $s->position(), ':', bin2hex((string)$c), ':';
  if ($c===false) { echo "end\n"; break; }
  if ($c==='<' || $c==='&' || $c==="\0") { $s->consume(); echo "consume\n"; }
  else { echo bin2hex($s->charsUntil("<&\0")), "\n"; }
 }
}
