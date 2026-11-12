--TEST--
DOM namespace-axis selection used by HTML serialization
--FILE--
<?php
$doc = new DOMDocument();
$doc->loadXML('<html xmlns="http://www.w3.org/1999/xhtml"><body><p>text</p></body></html>');
$xpath = new DOMXPath($doc);
foreach ([$doc->documentElement, $doc->documentElement->firstChild] as $node) {
    $result = $xpath->query('namespace::*[not(.=../../namespace::*)]', $node);
    echo $node->nodeName, ':', $result === false ? 'false' : $result->length, "\n";
}
--EXPECT--
html:2
body:0
