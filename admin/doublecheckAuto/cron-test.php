<?php
require_once("../functions.php");

//NEW HERE

$now=time();
$now=date("Y-m-d H:i:s");
$checkTime=strtotime($now. "-1hour");
$checkTime=date("Y-m-d H:i:s",$checkTime);








$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `SendStatus` NOT LIKE '%DbChecked%' AND `PayType`='01' AND `reg_date` < '".$checkTime."'";


$orderinfo=doSQLgetRow($sql);



if(count($orderinfo) != 0){
	
	print_r($orderinfo);
		
}else{
	exit("no datas");
}


echo "Start Time: ".$now."<br>";
echo "Check Time: ".$checkTime."<br>";
echo "<hr>";

?>