<?php
$d=new DOMDocument();$d->loadXML('<r xmlns="urn:default" xmlns:a="urn:a" xmlns:b="urn:b"><child xmlns:a="urn:other" xmlns=""><leaf/></child></r>');$x=new DOMXPath($d);
foreach([$d->documentElement,$d->documentElement->firstChild,$d->documentElement->firstChild->firstChild] as $element){
 echo $element->nodeName,"\n";
 foreach($x->query('namespace::*',$element) as $n) {echo json_encode([get_class($n),$n->nodeName,$n->nodeValue,$n->localName,$n->prefix,$n->namespaceURI,$n->nodeType,$n->parentNode->nodeName,$n instanceof DOMNode]),"\n";}
}
foreach (['namespace::*[not(.=../../namespace::*)]', 'namespace::a', 'namespace::*[name()="a"]', 'namespace::*/..', 'child::node()', 'self::node()'] as $query) {
    $nodes = $x->query($query, $d->documentElement->firstChild);
    echo $query, ':', $nodes->length, "\n";
}
foreach (['string(namespace::a)', 'name(namespace::a)', 'local-name(namespace::a)', 'namespace-uri(namespace::a)'] as $query) {
    echo $query, ':', $x->evaluate($query, $d->documentElement->firstChild), "\n";
}
$n = $x->query('namespace::a', $d->documentElement)->item(0);
echo 'owner:', $n->ownerDocument->documentElement->nodeName, "\n";
try { $n->nodeValue = 'changed'; } catch (Throwable $e) { echo get_class($e), ':', $e->getMessage(), "\n"; }
foreach ($x->query('namespace::*', $d->documentElement->firstChild) as $ns) {
    echo json_encode([$ns->nodeName, isset($ns->namespaceURI), isset($ns->nodeValue), isset($ns->parentNode)]), "\n";
}
echo 'simplexml:', json_encode(simplexml_load_string('<r xmlns:a="urn:a"/>')->xpath('namespace::*')), "\n";
$only = new DOMDocument();
$only->loadXML('<r xmlns="urn:r" xmlns:a="urn:a"/>');
var_dump($only->documentElement->hasAttributes(), $only->documentElement->attributes->length, $only->documentElement->getAttribute('xmlns'));
echo 'attributes:', (new DOMXPath($only))->query('//@*')->length, "\n";
