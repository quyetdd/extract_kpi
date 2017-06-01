<?php
$dir    = "/opt/kpi/";
$files = array();
$get_Filename = array();
foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot() || !$fileInfo->isFile()) continue;
    $files[] = $fileInfo->getFilename();
}
for ($i=0; $i<count($files); $i++){
	$exp = explode(".",$files[$i]);
	//var_dump(count($exp));
		// $str1='awk --field-separator="\\\\t" '{print "{\\"event\\":\\""$2"\\",","\\"data\\":"$3"}"}' '.$exp[0].'.'.$exp[1].'.'.$exp[2].' > '.$exp[0].'.'.$exp[1].'.tsv';
		$str1='awk --field-separator="\\\\t" \\'{print "{\\"event\\":\\""$2"\\",","\\"data\\":"$3"}"}\\' '.$exp[0].'.'.$exp[1].'.'.$exp[2].' > '.$exp[0].'.'.$exp[1].'.tsv';
		$str2='mongoimport --db kpi --collection kpi --type json   /opt/kpi/'.$exp[0].'.'.$exp[1].'.tsv';
		exec($str1);
		exec($str2);
		$del1='rm -f '.$exp[0].'.'.$exp[1].'.'.$exp[2];
		exec($del1);
		$del2='rm -f '.$exp[0].'.'.$exp[1].'.tsv';
		exec($del2);
		// echo $str1."<br>";
		// die();
	
}
?>