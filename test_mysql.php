<?php
$dir    = "/opt/kpi/test/";
$files = array();
$get_Filename = array();
foreach (new DirectoryIterator($dir) as $fileInfo) {
    if($fileInfo->isDot() || !$fileInfo->isFile()) continue;
    $files[] = $fileInfo->getFilename();
}
for ($i=0; $i<count($files); $i++){
	$str1='sed -i \'s/\\\/\\\\\\\/g\' test/'.$files[$i];
	exec($str1);
	$filequery = fopen("/opt/kpi/testquery.txt",'w');
	$str1='LOAD DATA LOCAL INFILE \'/opt/kpi/test/'.$files[$i].'\' 
INTO TABLE kpi.kpi CHARACTER SET utf8mb4
FIELDS TERMINATED BY \'\t\'  LINES TERMINATED BY \'\n\' ( timestamp,event,data)';
	fwrite($filequery,$str1);
	fclose($filequery);
	$str3='mysql -hlocalhost -uroot -pTabot@016/ kpi < testquery.txt';
	exec($str3);
	$del1='rm -f test/'.$files[$i];
	exec($del1);
}

?>