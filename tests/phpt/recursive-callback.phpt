--TEST--
Recursive callback filtering preserves callbacks and subclasses
--FILE--
<?php
function emit($name, $value) { echo $name, ':', json_encode($value), "\n"; }
$data = ['keep' => ['a' => 1, 'b' => 2, 'nested' => ['c' => 3]], 'skip' => ['hidden' => 4], 'z' => 5];
$callback = function ($value, $key, $inner) {
    if (!$inner instanceof RecursiveIterator) { throw new RuntimeException('Callback received wrong iterator'); }
    return $inner->hasChildren() ? $key !== 'skip' : $value % 2 === 1;
};
$it = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator($data), $callback);
emit('inheritance', [$it instanceof CallbackFilterIterator, $it instanceof RecursiveIterator]);
foreach ([RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CHILD_FIRST] as $mode) {
    $out = [];
    foreach (new RecursiveIteratorIterator($it, $mode) as $k => $v) { $out[] = [$k, is_array($v) ? 'branch' : $v]; }
    emit('walk', $out);
}
$it->rewind();
emit('inner', get_class($it->getInnerIterator()));
emit('children', [$it->hasChildren(), get_class($it->getChildren()), iterator_to_array($it->getChildren())]);
class ProbeChild extends RecursiveCallbackFilterIterator {}
$sub = new ProbeChild(new RecursiveArrayIterator([[1,2]]), $callback);
$sub->rewind(); emit('subclass', get_class($sub->getChildren()));
$empty = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([]), $callback);
emit('empty', iterator_to_array($empty));
$allRejected = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator($data), fn() => false);
emit('rejected', iterator_to_array(new RecursiveIteratorIterator($allRejected)));
try { new RecursiveCallbackFilterIterator(new ArrayIterator([]), $callback); } catch (TypeError $e) { emit('bad_iterator', get_class($e)); }
try { new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([]), 42); } catch (TypeError $e) { emit('bad_callback', get_class($e)); }
$throws = new RecursiveCallbackFilterIterator(new RecursiveArrayIterator([1]), function () { throw new RuntimeException('callback failure'); });
try { $throws->rewind(); } catch (RuntimeException $e) { emit('exception', $e->getMessage()); }
--EXPECT--
inheritance:[true,true]
walk:[["a",1],["c",3],["z",5]]
walk:[["keep","branch"],["a",1],["nested","branch"],["c",3],["z",5]]
walk:[["a",1],["c",3],["nested","branch"],["keep","branch"],["z",5]]
inner:"RecursiveArrayIterator"
children:[true,"RecursiveCallbackFilterIterator",{"a":1,"nested":{"c":3}}]
subclass:"ProbeChild"
empty:[]
rejected:[]
bad_iterator:"TypeError"
bad_callback:"TypeError"
exception:"callback failure"
