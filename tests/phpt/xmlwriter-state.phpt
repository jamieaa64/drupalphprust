--TEST--
XMLWriter memory API: xmlwriter-state (PHP 8.4.27 / libxml 2.9.14 oracle)
--FILE--
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
--EXPECT--
uninitialized: Error: Invalid or uninitialized XMLWriter object
bad-name: ValueError: XMLWriter::startElement(): Argument #2 must be a valid element name, "bad name" given
empty-name: ValueError: XMLWriter::writeElement(): Argument #2 ($content) must be a valid element name, "" given
orphan-end: bool(false)
bool(true)
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<p:root a:label="x &amp; &quot; y" stream="&lt;&amp;&gt;" xmlns:a="urn:attribute" xmlns:p="urn:probe">
  <child xmlns="urn:child">café</child>
  <expanded></expanded>
<![CDATA[<raw>]]><![CDATA[second]]>  <!--note-->
<?target value?>
<raw/></p:root>
string(0) ""
clone: Error: Trying to clone an uncloneable object of class XMLWriter
serialize: string(20) "O:9:"XMLWriter":0:{}"
restored: Error: Invalid or uninitialized XMLWriter object
<a>0</a><b>other</b>
<a>1</a><b>other</b>
<a>2</a><b>other</b>
<a>3</a><b>other</b>
<a>4</a><b>other</b>
<a>5</a><b>other</b>
<root>a</root>
