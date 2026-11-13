--TEST--
Generator collection preserves nested Fiber suspension and cleanup
--FILE--
<?php
function pieces() {
    echo "begin\n";
    $sent = Fiber::suspend('before');
    yield 'x' => $sent;
    echo "between\n";
    Fiber::suspend('middle');
    yield 'x' => 'second';
    yield 8 => 'third';
    echo "end\n";
}
foreach([true,false] as $keys) {
    $f=new Fiber(fn()=>iterator_to_array(pieces(),$keys));
    echo $f->start(),"\n";
    echo $f->resume('first'),"\n";
    $f->resume();
    echo json_encode($f->getReturn()),"\n";
}
function nestedParts() {
    yield 'a';
    $v=iterator_to_array((function(){Fiber::suspend('nested'); yield 'b';})());
    yield implode('', $v);
}
$f=new Fiber(fn()=>implode('',iterator_to_array(nestedParts())));
echo $f->start(),"\n"; $f->resume(); echo $f->getReturn(),"\n";
$f=new Fiber(function(){try {return iterator_to_array((function(){try {yield 'a'; Fiber::suspend('throw'); throw new Exception('broken');} finally {echo "finally\n";}})());} catch(Exception $e){return $e->getMessage();}});
echo $f->start(),"\n"; $f->resume(); echo $f->getReturn(),"\n";
foreach(['fresh','current','advanced','done'] as $state) {
    $g=(function(){yield 'a'; yield 'b';})();
    if($state==='current') $g->current();
    if($state==='advanced') $g->next();
    if($state==='done') {foreach($g as $v){}}
    try {echo $state,':',json_encode(iterator_to_array($g)),"\n";} catch(Throwable $e){echo $e->getMessage(),"\n";}
}

class D {function __construct(public $n){} function __destruct(){echo 'drop:',$this->n,"\n";}}
$f=new Fiber(function(){return iterator_to_array((function(){yield 'x'=>new D('first');yield 'x'=>'second';Fiber::suspend('pause');yield 'last';})());});
echo $f->start(),"\n";gc_collect_cycles();$f->resume();echo json_encode($f->getReturn()),"\n";
$g=(function(){yield [] => 'bad';})();try{var_dump(iterator_to_array($g));}catch(Throwable $e){echo get_class($e),':',$e->getMessage(),"\n";}

--EXPECT--
begin
before
between
middle
end
{"x":"second","8":"third"}
begin
before
between
middle
end
["first","second","third"]
nested
ab
throw
finally
broken
fresh:["a","b"]
current:["a","b"]
advanced:Cannot rewind a generator that was already run
done:Cannot traverse an already closed generator
drop:first
pause
{"x":"second","0":"last"}
TypeError:Cannot access offset of type array on array
