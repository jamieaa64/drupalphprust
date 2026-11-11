--TEST--
Reflect signatures of builtin functions and their Closure wrappers
--FILE--
<?php
foreach (['abs','json_encode','max','min','range','is_iterable'] as $name) {
 foreach ([$name,Closure::fromCallable($name)] as $value) {
  $r=new ReflectionFunction($value);$params=[];
  foreach($r->getParameters() as $p) $params[]=[$p->getName(),$p->isOptional(),$p->isVariadic(),(string)$p->getType(),$p->isDefaultValueAvailable()?$p->getDefaultValue():'none'];
  echo json_encode([$r->getName(),$r->isInternal(),$r->getNumberOfRequiredParameters(),(string)$r->getReturnType(),$params]),"\n";
 }
}
--EXPECT--
["abs",true,1,"int|float",[["num",false,false,"int|float","none"]]]
["abs",true,1,"int|float",[["num",false,false,"int|float","none"]]]
["json_encode",true,1,"string|false",[["value",false,false,"mixed","none"],["flags",true,false,"int",0],["depth",true,false,"int",512]]]
["json_encode",true,1,"string|false",[["value",false,false,"mixed","none"],["flags",true,false,"int",0],["depth",true,false,"int",512]]]
["max",true,1,"mixed",[["value",false,false,"mixed","none"],["values",true,true,"mixed","none"]]]
["max",true,1,"mixed",[["value",false,false,"mixed","none"],["values",true,true,"mixed","none"]]]
["min",true,1,"mixed",[["value",false,false,"mixed","none"],["values",true,true,"mixed","none"]]]
["min",true,1,"mixed",[["value",false,false,"mixed","none"],["values",true,true,"mixed","none"]]]
["range",true,2,"array",[["start",false,false,"string|int|float","none"],["end",false,false,"string|int|float","none"],["step",true,false,"int|float",1]]]
["range",true,2,"array",[["start",false,false,"string|int|float","none"],["end",false,false,"string|int|float","none"],["step",true,false,"int|float",1]]]
["is_iterable",true,1,"bool",[["value",false,false,"mixed","none"]]]
["is_iterable",true,1,"bool",[["value",false,false,"mixed","none"]]]
