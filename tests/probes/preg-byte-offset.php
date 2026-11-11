<?php
foreach ([['/\s+/A', "· value",2], ['/\s+/A', "· value",-6], ['/(?<=·) /', "· value",2], ['/./A', "·",1], ['/\s+/Au', "· value",2], ['/x/A',"·x",2], ['/./A',"\xffx",1], ['/./A',"·",2], ['/$/',"·",2]] as [$pattern,$text,$offset]) {
 $result=preg_match($pattern,$text,$match,PREG_OFFSET_CAPTURE,$offset);
 echo json_encode([$result, array_map(fn($m)=>[bin2hex($m[0]),$m[1]],$match)]),"\n";
}
