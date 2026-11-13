--TEST--
multisort stringable
--FILE--
<?php
class SortLabel {
    public function __construct(public string $value) {}
    public function __toString(): string { return $this->value; }
}
foreach ([SORT_STRING, SORT_NATURAL | SORT_FLAG_CASE] as $flags) {
    $labels = ['last' => new SortLabel('Item10'), 'first' => new SortLabel('item2'), 'middle' => new SortLabel('Item1')];
    $data = [10, 2, 1];
    var_dump(array_multisort($labels, SORT_ASC, $flags, $data));
    echo json_encode(array_map(fn($x)=>(string)$x,$labels)),':',json_encode($data),"\n";
}
class ThrowingSortLabel {
    public function __toString(): string { throw new RuntimeException('sort conversion'); }
}
$labels = [new ThrowingSortLabel(), new ThrowingSortLabel()];
$data = [2, 1];
try { array_multisort($labels, SORT_STRING, $data); }
catch (RuntimeException $e) { echo $e->getMessage(), ':', json_encode($data), ':', count($labels), "\n"; }
--EXPECT--
bool(true)
{"middle":"Item1","last":"Item10","first":"item2"}:[1,10,2]
bool(true)
{"middle":"Item1","first":"item2","last":"Item10"}:[1,2,10]
sort conversion:[2,1]:2
