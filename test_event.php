<?php
$dir    = "/opt/kpi/test/";
$file_event = fopen('/opt/kpi/event.txt','r');
$size = filesize('/opt/kpi/event.txt');
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
$get_Filename = array();
foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot() || !$fileInfo->isFile()) continue;
    $files[] = $fileInfo->getFilename();
}
for ($i=0; $i<count($files); $i++){
	$exp = explode(".",$files[$i]);
    for ($j=0; $j < sizeof($event); $j++) { 
    	$str1='awk --field-separator="\\t" \'$2 == "'.$event[$j].'" {print "{\"event\":\""$2"\",","\"data\":"$3"}"}\' /opt/kpi/test/'.$exp[0].'.'.$exp[1].'.'.$exp[2].' > /opt/kpi/test/'.$event[$j].'.'.$exp[1].'.tsv';
    	exec($str1);
    	$str2='mongoimport --db kpi --collection '.$event[$j].'_'.rtrim(rtrim($exp[1])).' --type json /opt/kpi/test/'.$event[$j].'.'.$exp[1].'.tsv';
		exec($str2);
    }
	
	// $del1='rm -f '.$exp[0].'.'.$exp[1].'.'.$exp[2];
	// exec($del1);
	// $del2='rm -f '.$exp[0].'.'.$exp[1].'.tsv';
	// exec($del2);	
}
?>