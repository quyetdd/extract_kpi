<?php
$dir    = "/home/phucbp/";
$file_event = fopen('/home/phucbp/event.txt','r');
$size = filesize('/home/phucbp/event.txt');
$event=array();
while(!feof($file_event))
{
    $event[] = fgets($file_event);
}
fclose($file_event);
for ($i=0; $i < count($event)-1; $i++) { 
        $event[$i]=rtrim($event[$i]);
}
$files = array();
$Filename = array();
foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot() || !$fileInfo->isFile()) continue;
    $Filename[] = $fileInfo->getFilename();
}
foreach ($Filename as $value) {
    if (substr($value, strrpos($value, '.') + 1) == 'log')
    $files[] = $value;
}
for ($i=0; $i<count($files); $i++){
    $exp = explode(".",$files[$i]);
    for ($j=0; $j < sizeof($event); $j++) { 
        $str1='awk --field-separator="\\t" \'$2 == "'.$event[$j].'" {print "{\"event\":\""$2"\",","\"data\":"$3"}"}\' /home/phucbp/'.$exp[0].'.'.$exp[1].'.'.$exp[2].' > /home/phucbp/'.$event[$j].'.'.$exp[1].'.tsv';
        exec($str1);
        $coll=substr($exp[1], 0,strlen($exp[1])-2);
        // $str2='mongoimport --db kpi --collection '.$event[$j].'_'.$coll.' --type json /home/phucbp/'.$event[$j].'.'.$exp[1].'.tsv';
         $str2='nohup mongoimport --db kpi --collection '.$event[$j].'_'.$coll.' --type json /home/phucbp/'.$event[$j].'.'.$exp[1].'.tsv > /dev/null 2>&1 &';
        exec($str2);
        // $del2='rm -f /home/phucbp/'.$event[$j].'.'.$exp[1].'.tsv';
        // exec($del2); 
    }
    // $del1='rm -f '.$exp[0].'.'.$exp[1].'.'.$exp[2];
    // exec($del1);
    
    
}
?>