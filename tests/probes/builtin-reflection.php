<?php
foreach (['abs','json_encode','max','min','range','is_iterable'] as $name) {
 foreach ([$name,Closure::fromCallable($name)] as $value) {
  $r=new ReflectionFunction($value);$params=[];
  foreach($r->getParameters() as $p) $params[]=[$p->getName(),$p->isOptional(),$p->isVariadic(),(string)$p->getType(),$p->isDefaultValueAvailable()?$p->getDefaultValue():'none'];
  echo json_encode([$r->getName(),$r->isInternal(),$r->getNumberOfRequiredParameters(),(string)$r->getReturnType(),$params]),"\n";
 }
}
