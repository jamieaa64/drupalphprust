--TEST--
Restore plain serialized keys into declared private and protected slots
--FILE--
<?php
class ParentPrivate {
    private $value = 'default';
    public function set($v) { $this->value = $v; }
    public function get() { return $this->value; }
}
class ChildPrivate extends ParentPrivate { private $other = 2; }
$v = new ChildPrivate(); $v->set('changed');
echo 'before:', $v->get(), "\n";
$serialized = serialize($v); echo 'wire:', bin2hex($serialized), "\n";
$copy = unserialize($serialized); echo 'after:', $copy->get(), "\n";
class PackedChild extends ParentPrivate {
    public function __serialize(): array { return ['value' => 'packed']; }
}
echo 'plain_private:', unserialize(serialize(new PackedChild()))->get(), "\n";
class ProtectedPacked {
    protected $value = 'default';
    public function __serialize(): array { return ['value' => 'protected']; }
    public function get() { return $this->value; }
}
echo 'plain_protected:', unserialize(serialize(new ProtectedPacked()))->get(), "\n";
--EXPECT--
before:changed
wire:4f3a31323a224368696c6450726976617465223a323a7b733a32303a2200506172656e74507269766174650076616c7565223b733a373a226368616e676564223b733a31393a22004368696c6450726976617465006f74686572223b693a323b7d
after:changed
plain_private:packed
plain_protected:protected
