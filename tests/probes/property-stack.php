<?php
class ParserStack {
 private array $targets=[];
 public function walk($depth) { $index=count($this->targets);$this->targets[]=null;try{if($depth)$this->walk($depth-1);echo $index,':',json_encode($this->targets),':',json_encode($this->targets[$index]),"\n";}finally{array_pop($this->targets);} }
}
$p=new ParserStack;$p->walk(2);$p->walk(1);
