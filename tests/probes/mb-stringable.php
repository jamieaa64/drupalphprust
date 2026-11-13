<?php
class MbLabel {
    public function __construct(private string $value) {}
    public function __toString(): string { echo 'cast:',bin2hex($this->value),"\n";return $this->value; }
}
echo mb_strtolower(new MbLabel('UTILITY PAGE'),new MbLabel('UTF-8')),"\n";
echo mb_strtoupper(new MbLabel('utility page')),"\n";
echo mb_strtolower(new MbLabel('ÉCOLE')),"\n";
class MbFailure {public function __toString(): string {throw new Exception('conversion stopped');}}
try {mb_strtolower(new MbFailure());}catch(Exception $e){echo $e->getMessage(),"\n";}
