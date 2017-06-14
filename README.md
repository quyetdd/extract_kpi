# extract_kpi
extract and loading transform db

awk '{ sub(/2016-02-01$/, "2017-02-01", $3) }1' {print "$1  $2  $3"} file.log > output.log
