<?php
$doc=new DOMDocument(); $root=$doc->createElement('root'); $doc->appendChild($root);
try { $root->setAttributeNS('urn:a','label','v'); throw new RuntimeException('Silently accepted automatic prefix'); } catch (Error $e) { echo "automatic-prefix:rejected\n"; }
$root->setAttributeNS('urn:a','p:a','first');
try { $root->setAttributeNS('urn:b','p:b','second'); throw new RuntimeException('Silently accepted rebinding'); } catch (Error $e) { echo "prefix-rebinding:rejected\n"; }
if ($root->getAttributeNS('urn:a','a') !== 'first') { throw new RuntimeException('Existing namespace corrupted'); }
echo "existing-namespace:preserved\n";
