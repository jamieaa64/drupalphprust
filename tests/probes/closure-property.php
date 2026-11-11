<?php
class Hooks { public function load(array $entities, string $type): void { echo 'type:', $type, "\n"; } }
class Handler { public function invoke($cb) { $cb([new Hooks(), 'load'], 'module'); } }
class BaseStorage {
 protected $type;
 public function __construct() { $this->type = 'node'; }
 public function run() { $entities=[]; (new Handler())->invoke(function (callable $hook, string $module) use (&$entities) { echo 'direct:', $this->type, "\n"; $hook($entities, $this->type); }); }
}
class Storage extends BaseStorage {}
(new Storage())->run();
class RefHooks {
 public function change(&$v) { $v = 'updated'; }
 public function __invoke(&$v) { $v = 'invoked'; }
 public function __call($name, $args) { echo 'magic:', $args[0], "\n"; }
}
$hooks = new RefHooks();
$cb = [$hooks, 'change']; $value='initial'; $cb($value); echo 'ref:', $value, "\n";
$hooks($value); echo 'invoke:', $value, "\n";
$cb = [$hooks, 'missing']; $a=['key'=>'present']; $cb($a['key']);
