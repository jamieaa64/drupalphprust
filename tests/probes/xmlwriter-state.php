<?php
// Exercise native ownership and memory-writer state independently of Drupal.
function result($name, $callback) {
    echo $name, ': ';
    try { var_dump($callback()); }
    catch (Throwable $error) { echo get_class($error), ': ', $error->getMessage(), "\n"; }
}
$w = new XMLWriter();
result('uninitialized', fn() => $w->outputMemory());
$w->openMemory();
result('bad-name', fn() => $w->startElement('bad name'));
result('empty-name', fn() => $w->writeElement(''));
result('orphan-end', fn() => $w->endElement());
// Start a fresh document after the invalid transition.
$w->openMemory();
$w->setIndent(true);
$w->setIndentString('  ');
$w->startDocument('1.0', 'UTF-8', 'yes');
$w->startElementNs('p', 'root', 'urn:probe');
$w->writeAttributeNs('a', 'label', 'urn:attribute', 'x & " y');
$w->startAttribute('stream'); $w->text('<&>'); $w->endAttribute();
$w->writeElementNs(null, 'child', 'urn:child', 'café');
$w->startElement('expanded'); $w->fullEndElement();
$w->startCdata(); $w->text('<raw>'); $w->endCdata();
$w->writeCdata('second');
$w->startComment(); $w->text('note'); $w->endComment();
$w->startPi('target'); $w->text('value'); $w->endPi();
$w->writeRaw('<raw/>');
$w->endDocument();
$copy = $w->flush(false);
var_dump($copy === $w->outputMemory(false));
echo $w->flush();
var_dump($w->outputMemory());
result('clone', fn() => clone $w);
result('serialize', fn() => serialize($w));
$restored = unserialize(serialize($w));
result('restored', fn() => $restored->text('no'));
// Separate objects, reused handles and re-opened writers cannot share native state.
for ($i = 0; $i < 6; $i++) {
    $a = new XMLWriter(); $a->openMemory();
    $b = new XMLWriter(); $b->openMemory();
    $a->writeElement('a', (string) $i);
    $b->writeElement('b', 'other');
    echo $a->outputMemory(), $b->outputMemory(), "\n";
    unset($a, $b);
}
$w->openMemory();
$w->startElement("root\0ignored"); $w->text("a\0ignored"); $w->endElement();
echo $w->outputMemory(), "\n";
