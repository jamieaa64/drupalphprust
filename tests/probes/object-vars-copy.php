<?php
class SavedState {
 private array $values=[null];
 public function test() {
  array_pop($this->values); $this->values[]=null;
  $saved=get_object_vars($this); $this->values=[];
  echo json_encode($saved['values']),"\n";
  $alias=&$this->values; $saved=get_object_vars($this); $this->values=['shared'];
  echo json_encode($saved['values']),"\n";
 }
}
(new SavedState)->test();
