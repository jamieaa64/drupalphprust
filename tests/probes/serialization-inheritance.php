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
if (is_file(getcwd() . '/vendor/autoload.php')) {
    require getcwd() . '/vendor/autoload.php';
    $d = new Symfony\Component\DependencyInjection\ChildDefinition('parent');
    $d->setClass('ProbeClass');
    echo 'definition:', json_encode(unserialize(serialize($d))->getClass()), "\n";
}
