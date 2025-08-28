<?php

require_once("../functions.php");

echo "cool"."\r\n";
//NEW HERE
date_default_timezone_set('Asia/Taipei');
$now=time();
$now=date("Y-m-d H:i:s");
$checkTime=strtotime($now. "-1hour");
$checkTime=date("Y-m-d H:i:s",$checkTime);








echo "Start Time: ".$now."\r\n";
echo "Check Time: ".$checkTime."\r\n";
echo "<hr>";

?>