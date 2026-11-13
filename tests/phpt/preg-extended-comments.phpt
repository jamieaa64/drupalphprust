--TEST--
preg extended comments
--FILE--
<?php
$pattern='/
      \[             # [ - pattern start
      ([^\s\[\]:]+)  # match $type not containing whitespace : [ or ]
      :              # : - separator
      ([^\[\]|]+)    # match $name not containing [ or ] or |
      \]             # ] - pattern end
      /x';
var_dump(preg_match_all($pattern, '[site:name] [node:title]', $matches));
echo json_encode($matches), "\n";
$results=[];
for ($i=0;$i<count($matches[2]);$i++) {$results[$matches[1][$i]][$matches[2][$i]]=$matches[0][$i];}
echo json_encode($results),"\n";
foreach ([
    ["/a # [ $ ignored\nb/x", 'ab'],
    ['/^[ #]+$/x', ' # '],
    ['/a\#b/x', 'a#b'],
    ['/[]# ]+/x', '] #'],
    ['/[^ #]+/x', 'word'],
    ['/a#b/', 'a#b'],
] as [$regex, $subject]) {
    echo json_encode([$regex, preg_match($regex, $subject, $m), $m]), "\n";
}
foreach ([['/a(?-x:#)b/x','a#b'],['/[[:alpha:] #]/x','#'],['/\Q#[$]\E/x','#[$]'],['/\Q#[$]\E/x','unrelated']] as [$regex,$subject]) {
    echo json_encode([$regex,preg_match($regex,$subject,$m),$m]),"\n";
}
foreach ([['/\c#/x','c'],["/[^^] # [comment\na/x",'ba']] as [$regex,$subject]) {
    echo json_encode([$regex,preg_match($regex,$subject,$m),$m]),"\n";
}
--EXPECT--
int(2)
[["[site:name]","[node:title]"],["site","node"],["name","title"]]
{"site":{"name":"[site:name]"},"node":{"title":"[node:title]"}}
["\/a # [ $ ignored\nb\/x",1,["ab"]]
["\/^[ #]+$\/x",1,[" # "]]
["\/a\\#b\/x",1,["a#b"]]
["\/[]# ]+\/x",1,["] #"]]
["\/[^ #]+\/x",1,["word"]]
["\/a#b\/",1,["a#b"]]
["\/a(?-x:#)b\/x",1,["a#b"]]
["\/[[:alpha:] #]\/x",1,["#"]]
["\/\\Q#[$]\\E\/x",1,["#[$]"]]
["\/\\Q#[$]\\E\/x",0,[]]
["\/\\c#\/x",1,["c"]]
["\/[^^] # [comment\na\/x",1,["ba"]]
