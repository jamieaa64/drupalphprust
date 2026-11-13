<?php
$format="j M Y";
$format = preg_replace(['/\\\\\\\\/', '/(?<!\\\\)([SAaeDlMTF])/'], ["\xEF\\\\\\\\\xFF", "\xEF\\\\\$1\$1\xFF"], $format);
echo bin2hex($format),"\n";
$d=new DateTimeImmutable("2026-11-13",new DateTimeZone("UTC"));$r=$d->format($format);echo bin2hex($r),"\n";
echo bin2hex(preg_replace_callback('/\xEF([SAaeDlMTF]?)(.*?)\xFF/',fn($m)=>$m[2],$r)),"\n";
foreach ([['/(a)/',"\xFF$1\x00",'aa'],['/(é)/u',"\xFF$1",'éé'],['/(.)/',"\xFE$1", "\x80\xFF"],['/(?<=a)(b)/',"\xFF$1",'ab'],['/a/',"\x00\xEF\xFF",'x']] as [$p,$replacement,$subject]) {
    foreach ([-1,0,1] as $limit) {
        $count=0;$result=preg_replace($p,$replacement,$subject,$limit,$count);
        echo bin2hex($result),':',$count,"\n";
    }
}
$count=0;$result=preg_replace(['/a/','/\xFF/'],["\xFF","\x80"],['first'=>'aa',7=>'a'],-1,$count);
foreach($result as $key=>$value)echo $key,':',bin2hex($value),"\n";echo $count,"\n";
$count=0;$result=preg_replace(['/a/','/./u'],["\xFF",'b'],'a',-1,$count);var_dump($result,$count,preg_last_error());
