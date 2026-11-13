--TEST--
Regular expression replacement preserves binary PHP strings
--FILE--
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

--EXPECT--
6a20ef5c4d4dff2059
313320ef4d4e6f76ff2032303236
3133204e6f762032303236
ff6100ff6100:2
6161:0
ff610061:1
ffc3a9ffc3a9:2
c3a9c3a9:0
ffc3a9c3a9:1
fe80feff:2
80ff:0
fe80ff:1
61ff62:1
6162:0
61ff62:1
78:0
78:0
78:0
first:8080
7:80
6
NULL
int(1)
int(4)
