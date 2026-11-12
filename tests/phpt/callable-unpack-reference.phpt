--TEST--
Callable unpacking preserves reference arguments and array-copy isolation
--FILE--
<?php
namespace {
class UnpackHook {
    public function preprocess(array &$value): void { $value['path'] = 'theme'; }
}
$hook = new UnpackHook();
function invokeUnpackHook($listener, array $args) {
    $listeners = [$listener];
    return reset($listeners)(...$args);
}
foreach ([$hook->preprocess(...), [$hook, 'preprocess'], function (&$value) { $value['path'] = 'theme'; }] as $listener) {
    $value = []; $listener($value); echo json_encode($value), "\n";
    $value = []; $args = [&$value]; $listener(...$args); echo json_encode($value), "\n";
    $value = []; call_user_func_array($listener, [&$value]); echo json_encode($value), "\n";
    $value = []; invokeUnpackHook($listener, [&$value]); echo json_encode($value), "\n";
    $value = [];
    $outer = function () use (&$value, $listener) { invokeUnpackHook($listener, [&$value]); };
    $outer(); echo json_encode($value), "\n";
}
$byValue = function ($value) { $value['path'] = 'local'; };
$value = ['path' => 'original']; $args = [&$value]; $byValue(...$args);
echo json_encode($value), "\n";
$variadic = function (&...$values) { foreach ($values as &$value) { $value += 10; } };
$a = 1; $b = 2; $args = [&$a, &$b]; $variadic(...$args); echo "$a,$b\n";
$sort = 'sort'; $value = [3, 1, 2]; $args = [&$value]; $sort(...$args);
echo json_encode($value), "\n";
$length = 'strlen'; $value = 'abc'; $args = [&$value]; echo $length(...$args), ':', $value, "\n";
$alias = 1; $source = [&$alias]; $copy = [...$source]; $copy[0] = 9;
echo $alias, ':', $source[0], "\n";
$source = [1]; $copy = [...$source]; $copy[0] = 9;
echo $source[0], ':', $copy[0], "\n";
$alias = 1; $source = ['a' => &$alias]; $copy = [...$source]; $copy['a'] = 9;
echo $alias, ':', $source['a'], "\n";
$source = [1]; $alias = &$source[0]; unset($alias); $copy = [...$source]; $copy[0] = 2;
echo $source[0], ':', $copy[0], "\n";
$source = [1]; $alias = &$source[0]; unset($alias);
$set = function (&$value) { $value = 3; }; $set(...$source); echo $source[0], "\n";
eval('function unpackFallbackMutation(&$value) { $value = "fallback"; }');
}
namespace UnpackReferenceFixture {
$value = 'before'; $args = [&$value]; unpackFallbackMutation(...$args); echo $value, "\n";
}
--EXPECT--
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"theme"}
{"path":"original"}
11,12
[1,2,3]
3:abc
9:9
1:9
9:9
1:2
3
fallback
