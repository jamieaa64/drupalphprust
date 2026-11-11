<?php
// Run with the PHP oracle; intentionally limited to the named builtin set.
function typeInfo(?ReflectionType $t): array|false {
 if (!$t) return false;
 if ($t instanceof ReflectionNamedType) return ['name'=>$t->getName(),'builtin'=>$t->isBuiltin(),'nullable'=>$t->allowsNull()];
 return ['kind'=>$t instanceof ReflectionIntersectionType?'intersection':'union','types'=>array_map(typeInfo(...),$t->getTypes()),'nullable'=>$t->allowsNull()];
}
foreach (['abs','json_encode','max','min','range','is_iterable'] as $name) {
 $r=new ReflectionFunction($name);$params=[];
 foreach($r->getParameters() as $p) $params[]=['name'=>$p->getName(),'position'=>$p->getPosition(),'optional'=>$p->isOptional(),'variadic'=>$p->isVariadic(),'byref'=>$p->isPassedByReference(),'type'=>typeInfo($p->getType()),'hasDefault'=>$p->isDefaultValueAvailable(),'default'=>$p->isDefaultValueAvailable()?$p->getDefaultValue():null,'declClass'=>'','declFunc'=>$name,'defaultConstant'=>$p->isDefaultValueAvailable()&&$p->isDefaultValueConstant()?$p->getDefaultValueConstantName():false,'defaultError'=>false,'promoted'=>false];
 $info=['name'=>$name,'params'=>$params,'returnType'=>typeInfo($r->getReturnType()),'doc'=>false,'isGenerator'=>false,'byRef'=>false,'file'=>false,'startLine'=>false,'endLine'=>false];
 file_put_contents($argv[1].'/'.$name.'.ser',serialize($info));
}
