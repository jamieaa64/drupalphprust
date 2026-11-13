--TEST--
dom namespace inheritance
--FILE--
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
--EXPECT--
r
["DOMNameSpaceNode","xmlns:xml","http:\/\/www.w3.org\/XML\/1998\/namespace","xml","xml","http:\/\/www.w3.org\/XML\/1998\/namespace",18,"r",false]
["DOMNameSpaceNode","xmlns:b","urn:b","b","b","urn:b",18,"r",false]
["DOMNameSpaceNode","xmlns:a","urn:a","a","a","urn:a",18,"r",false]
["DOMNameSpaceNode","xmlns","urn:default","xmlns","","urn:default",18,"r",false]
child
["DOMNameSpaceNode","xmlns:xml","http:\/\/www.w3.org\/XML\/1998\/namespace","xml","xml","http:\/\/www.w3.org\/XML\/1998\/namespace",18,"child",false]
["DOMNameSpaceNode","xmlns:b","urn:b","b","b","urn:b",18,"child",false]
["DOMNameSpaceNode","xmlns",null,"xmlns","","",18,"child",false]
["DOMNameSpaceNode","xmlns:a","urn:other","a","a","urn:other",18,"child",false]
leaf
["DOMNameSpaceNode","xmlns:xml","http:\/\/www.w3.org\/XML\/1998\/namespace","xml","xml","http:\/\/www.w3.org\/XML\/1998\/namespace",18,"leaf",false]
["DOMNameSpaceNode","xmlns:b","urn:b","b","b","urn:b",18,"leaf",false]
["DOMNameSpaceNode","xmlns",null,"xmlns","","",18,"leaf",false]
["DOMNameSpaceNode","xmlns:a","urn:other","a","a","urn:other",18,"leaf",false]
namespace::*[not(.=../../namespace::*)]:2
namespace::a:1
namespace::*[name()="a"]:1
namespace::*/..:1
child::node():1
self::node():1
string(namespace::a):urn:other
name(namespace::a):a
local-name(namespace::a):a
namespace-uri(namespace::a):
owner:r
Error:Cannot modify readonly property DOMNameSpaceNode::$nodeValue
["xmlns:xml",true,true,true]
["xmlns:b",true,true,true]
["xmlns",true,false,true]
["xmlns:a",true,true,true]
simplexml:[]
bool(false)
int(0)
string(5) "urn:r"
attributes:0
