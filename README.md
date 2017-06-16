# extract_kpi

error 403 --> setenforece 0
extract and loading transform db

sed '{ sub(/2016-02-01$/, "2017-02-01", $3) }1' file.log > output.log


1. Tại sao cần thiết có 1 ứng dụng riêng cho việc phân tích KPI 
	Ngoài việc sử dụng các phần mềm hệ thống có sẵn cho việc phân tích KPI và các báo các chuyên dụng phục vụ cho mục đích phân tích và đánh giá các event phát sinh trong game
	thì có 1 số yếu tố nữa cần xem xét cho việc xử dụng hệ thống tự viết riêng cho các game mà nhà sản xuất tự phát hành
	- tự control được các event và thống kê cần thiết 
	- cải thiện hiệu năng và bug nếu việc tracking của bên thứ 3 có phát sinh lỗi
	- Tự control được các thành phần
2. Để xây dựng KPi server cần các yếu tố sau
	- Server
	- Client (ở đây được coi là SDK cho việc tích hợp hệ thống vì sử dụng như vậy ta mới sử dụng là chung cho các ứng dụng [trong đó có game])
	
	- server được cấu hình có thể log tất cả các event khi client được gọi đến server (bao gồm cả thành công, lẫn không thành công)
		+ log tất cả hay thế nào
		+ yêu cầu memory foot print nhỏ
		+ chỉ log những phần được gọi là event tracking (vì đôi khi game chạy sau 1 năm những data mà chúng ta không log lại rất cần thiết cho việc phân tích ....)
	- client cần viết 1 SDK (để không sau này dễ control và sửa chữa để không ảnh hưởng đến việc nâng cấp client)
	- SDK chỉ việc nâng cấp các version để fixbug hoặc thêm mới các tính năng (như các nhà cung cấp khác thường làm tapjoy, google)
	- yêu cầu tính bảo mật như thế nào?
	- yêu cầu về message truyền tải (cần nhỏ và dễ phân tích [ví dụ như sử dụng mspack]  - nên sử dụng phương thức nào tcp. MQTT hay UDP .....
		(ví dụ độ dài khi dùng tcp tốc độ truyền , nhận sẽ khác cách sử dụng MQTT)
	
	
- Từ các nhận xét và tìm hiểu trên thì ta cần thiết kế server cho hệ thống như thế nào
 + hệ thống là quản lý tập chung tất cả các game quản lý hay mỗi hệ thống tự phải xây dựng 1 server riêng để tracking
 + hệ thống cần handle bao nhiêu request (event/second) 
 + hệ thống cần hiển thị thời gian thực hay không
 + hệ thống có cần phân quyền hoặc có mục đích sử dụng việc phân cấp khi xử dụng hay không
 + client SDK cần viết cho 1 nền tảng (unity) hay nhiều nền tảng 
 + đối với game thì việc phân tích tracking thời gian chơi (có yêu cầu đó là socket hay không hay sử dụng đơn giản là http keep-alive có timeout)


DAU - All users who have launched the App on that day

MAU - All users who have launched the App at least once in that month

DAU/MAU - shall give the stickiness of your App.


with messi log data we need
--------------------------------------
MonggoDB
--------------------------------------

ETL (cái này là 1 phần kỹ thuật mô tả trong business intelligent)
	E: Extract data from any resource --> understand data (json only)
		awk --field-separator="\\t" '{print "{\"event\":\""$2"\",","\"data\":"$3"}"}' kpi.log > kpi.tsv

awk --field-separator="\\t" '{print "{\"event\":\""$2"\",","\"data\":"$3"}"}' all_messi_kpi.2016020100.log > kpi.tsv

	T : transform data to json row
		mongoimport --db kpi --collection kpi --type json   /opt/kpi/kpi.tsv
	L : loading to database engine (mongodb)
		query here
		db.kpi.find({"event":"KpiLogin","data.character_id":397451}).count()


	
	



Active User

var dateForm="2016-02-01"
var dateto="2016-02-02"
getUsersForPeriod = 
db.kpi.aggregate([
                    { $match: {'event':"KpiLogin",'data.login_time': {$gte: "2016-02-01", $lt: "2016-02-02" }}}, 
					{ $group: { _id: '$data.character_id', total: { $sum: 1 }}},
					{ $sort: { total: -1 }}
                   ]).toArray().length
				   


DAU/MAU

var today = new Date();
var yesterday = new Date();
var monthAgo = new Date();
monthAgo.setDate(monthAgo.getDate()-30);
yesterday.setDate(yesterday.getDate()-1);
.getUsersForPeriod(yesterday, today, function(err, dauUsers){
    if (err) { return res.json({ error: err }); }
    User.getUsersForPeriod(monthAgo, today, function(err, mauUsers){
        if (err) { res.json({ error: err }); }
        res.json({ daumau: dauUsers.length/mauUsers.length * 100 });
    });
});

---------------------------------------
MySQL
---------------------------------------

root
Tabot@016/
 

	
  CREATE TABLE kpi(
id BigINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
timestamp datetime,
event VARCHAR(500) NOT NULL,
data JSON
)ENGINE=MyIsam DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE utf8mb4;



chý ý khi import vào mysql thì 1 số ký tự không được valid  ta phải sửa lại trước khi import vào mysql (cấu trúc json)
\n
(và có thể có 1 số ký tự khác) nên việc ghi log trước tiên cần ghi đúng cấu trúc phù hợp cho việc import vào engine nào đó

cần search và thay thế ký tự \ bằng  -> \\

sed -i 's/\\/\\\\/g' kpi.log  (5 second)



LOAD DATA LOCAL INFILE '/opt/kpi/kpi.log' 
INTO TABLE kpi.kpi CHARACTER SET utf8mb4
FIELDS TERMINATED BY '\t'  LINES TERMINATED BY '\n' ( timestamp,event,data)


query :

SELECT COUNT(*) as DAU FROM  (
select count(*) from kpi where  event='KpiLogin'  and    timestamp BETWEEN '2016-02-01' AND '2016-02-02'  group by  data->"$.character_id" 
) groups

2M - 13s
23M - 3m42s

