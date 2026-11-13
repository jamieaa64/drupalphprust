<?php
class WriteProbe {
    public $context;
    public static array $contents=[];
    private string $path='';
    public function stream_open($path,$mode,$options,&$opened): bool {
        $this->path=$path;
        echo 'open:',$mode,':',$options,"\n";
        if ($mode[0]==='w') self::$contents[$path]='';
        return true;
    }
    public function stream_write($data): int {
        echo 'write:',strlen($data),"\n";
        self::$contents[$this->path]=(self::$contents[$this->path]??'').$data;
        return strlen($data);
    }
    public function stream_flush(): bool {echo "flush\n";return true;}
    public function stream_close(): void {echo "close\n";}
}
stream_wrapper_register('writeprobe',WriteProbe::class);
var_dump(file_put_contents('writeprobe://one','abc'));
var_dump(file_put_contents('writeprobe://one',['d','ef'],FILE_APPEND));
var_dump(file_put_contents('writeprobe://empty',''));
echo json_encode(WriteProbe::$contents),"\n";
class PartialWriteProbe extends WriteProbe {
    public static int $remaining=99;
    public function stream_write($data): int {
        if (self::$remaining-- <= 0) { return 0; }
        return parent::stream_write(substr($data,0,2));
    }
}
stream_wrapper_register('partialwrite',PartialWriteProbe::class);
var_dump(file_put_contents('partialwrite://one','abcdef'));
PartialWriteProbe::$remaining=0;
set_error_handler(function($level,$message){echo 'warning:',$message,"\n";});
var_dump(file_put_contents('partialwrite://stopped','abc'));
var_dump(file_put_contents('writeprobe://locked','abc',LOCK_EX));
restore_error_handler();
class WriteContextProbe extends WriteProbe {
    public function stream_open($path,$mode,$options,&$opened): bool {
        echo 'context:',json_encode(stream_context_get_options($this->context)),"\n";
        return parent::stream_open($path,$mode,$options,$opened);
    }
}
stream_wrapper_register('contextwrite',WriteContextProbe::class);
var_dump(file_put_contents('contextwrite://one','x',FILE_USE_INCLUDE_PATH,stream_context_create(['contextwrite'=>['test'=>true]])));
class WriteString {
    public function __toString(): string {echo "stringify\n";return 'object';}
}
var_dump(file_put_contents('writeprobe://object',new WriteString()));
class WriteThrows {
    public function __toString(): string {throw new RuntimeException('conversion failed');}
}
try {file_put_contents('writeprobe://throw',new WriteThrows());}
catch(RuntimeException $e){echo $e->getMessage(),"\n";}
$input=fopen('php://memory','w+');fwrite($input,'resource');rewind($input);
var_dump(file_put_contents('writeprobe://resource',$input));fclose($input);

class ReviewWrite {
 public $context;
 static $result=0;
 public function stream_open($p,$m,$o,&$x){echo "open\n";return true;}
 public function stream_write($d){echo 'write:',strlen($d),"\n";return self::$result;}
 public function stream_flush(){echo "flush\n";return true;}
 public function stream_close(){echo "close\n";}
}
stream_wrapper_register('revwrite',ReviewWrite::class);
set_error_handler(function($n,$m){echo 'warning:',$m,"\n";});
foreach([0,false,-1,5] as $r){
 ReviewWrite::$result=$r;
 foreach(['abc',['abc','def']] as $data){
  echo 'case:',json_encode($r),':',is_array($data)?'array':'scalar',"\n";
  var_dump(file_put_contents('revwrite://a',$data));
 }
}
