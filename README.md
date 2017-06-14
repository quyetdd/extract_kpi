# extract_kpi
extract and loading transform db

sed '{ sub(/2016-02-01$/, "2017-02-01", $3) }1' file.log > output.log
