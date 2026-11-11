<?php
$impl=new DOMImplementation();
foreach ([['html','',''],['html','-//EXAMPLE//DTD TEST//EN','test.dtd']] as $args) {
 $dt=$impl->createDocumentType(...$args);
 echo $dt->name, ':', $dt->publicId, ':', $dt->systemId, ':', ($dt->ownerDocument===null ? 'detached' : 'owned'), "\n";
 $doc=$impl->createDocument(null, '', $dt);
 $doc->appendChild($doc->createElement('html'));
 echo $doc->saveXML();
 echo 'attached:', $doc->doctype->isSameNode($dt) ? 'yes' : 'no', "\n";
 try { $impl->createDocument(null, '', $dt); } catch (DOMException $e) { echo 'reuse:', $e->getCode(), "\n"; }
}
try { $impl->createDocumentType(''); } catch (ValueError $e) { echo 'empty:ValueError', "\n"; }
$doc=$impl->createDocument('urn:example', 'x:root');
$root=$doc->documentElement;
$root->setAttributeNS('urn:attribute', 'a:label', 'value');
echo 'namespace:', $root->namespaceURI, ':', $root->prefix, ':', $root->localName, "\n";
echo 'attribute:', $root->getAttributeNS('urn:attribute','label'), "\n";
echo $doc->saveXML();
try { $doc->createElementNS(null, 'x:bad'); } catch (DOMException $e) { echo 'namespace-error:', $e->getCode(), "\n"; }
