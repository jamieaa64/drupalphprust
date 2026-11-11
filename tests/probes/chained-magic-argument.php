<?php
class Field { public function __get($name) { return 'draft'; } }
class Entity { private $field; public function __construct(){ $this->field=new Field; } public function &__get($name) { return $this->field; } }
class Plugin { function state($value){ return $value; } }
class Workflow { function plugin(){ return new Plugin; } }
$n=new Entity;$w=new Workflow;
var_dump($n->moderation_state->value);
var_dump($w->plugin()->state($n->moderation_state->value));
var_dump($n->moderation_state->value ? $w->plugin()->state($n->moderation_state->value) : 'empty');
class PrivateField {private $value='hidden';public function __get($n){return 'magic';}}
class RefContainer { public $item; function __construct($v){$this->item=$v;} function &__get($n){return $this->item;} }
$p=new RefContainer(new PrivateField);var_dump($w->plugin()->state($p->field->value));
class HookField { public string $value { get => 'hook'; } }
$h=new RefContainer(new HookField);var_dump($w->plugin()->state($h->field->value));
class PublicField {public $value='public';}
$p=new RefContainer(new PublicField);var_dump($w->plugin()->state($p->field->value));
class InaccessibleField {private $value='private';}
$p=new RefContainer(new InaccessibleField);try{$w->plugin()->state($p->field->value);}catch(Error $e){echo $e->getMessage(),"\n";}
