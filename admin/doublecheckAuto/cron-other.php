<?php
ini_set('display_errors',1);  
set_time_limit(0);
require_once("../functions.php");
//include('../soap/nusoap.php');



$fp = fopen (dirname(__FILE__) . '/update_other.txt', 'w+');
fwrite($fp,date('Y-m-d h:i:s'));
fclose($fp);


/*

$OrderNo=$_GET["OrderNo"];

$abc=new dbCheck;
$abc-> Action($OrderNo);

*/



//NEW HERE

$time = time();
$now=date("Y-m-d H:i:s");
$checkTime=strtotime($now);
$checkTime=date("Y-m-d H:i:s",$checkTime);

$offsetTime = 7 * 24 * 60 *60;
$startTime = date("Y-m-d H:i:s",$time - $offsetTime);

echo "<h2>First Check</h2>"."<br>";

$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `SendStatus` NOT LIKE '%DbChecked%' AND (`PayType` NOT LIKE '01' AND `PayType` NOT LIKE '11') AND `reg_date` > '$startTime' LIMIT 5";


$orderinfo=doSQLgetRow($sql);


if(count($orderinfo)==0){
	exit("no datas");
}

foreach($orderinfo as $key => $row){
	
	echo $row["OrderNo"]."<br>";	
	$DueDate=urldecode($row["DueDate"]);
	
	if (!$DueDate) {
	   continue;
	}
	
	echo $DueDate."<br>";

// 	$abc=new dbCheck();
// 	$abc-> Action_CheckButNotDouble($row["OrderNo"]);
		
echo "--------------------------------------------------------------"."<br>";
	
}



echo "<h2>Double Check</h2>"."<br>";


$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `SendStatus` NOT LIKE '%DbChecked%' AND `PayType` NOT LIKE '01' AND `reg_date` > '$startTime' LIMIT 5";


$orderinfo=doSQLgetRow($sql);


if(count($orderinfo)==0){
	exit("no datas");
}


echo "Start Time: ".$now."<br>";
echo "---------------------------------"."<br>";

foreach($orderinfo as $key => $row){
	
	echo $row["OrderNo"]."<br>";	
	$DueDate=urldecode($row["DueDate"]);
	
	echo $DueDate."<br>";
	
//	echo $now."<br>";


$nowdate=strtotime($now);

if($row["PayType"]=="05"){
	$pastdate=strtotime($DueDate.' +24 hour');
}else{
	$pastdate=strtotime($DueDate.' +8 hour');
}

	

if($nowdate > $pastdate){
	$abc=new dbCheck;
	$abc-> Action($row["OrderNo"]);
	echo "<span style='color:red'>checked</span>"."<br>";
}

	
echo "--------------------------------------------------------------"."<br>";
	
}


echo "All Fixed";