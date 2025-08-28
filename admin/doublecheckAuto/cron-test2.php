<?php
require_once("../functions.php");


ini_set('display_errors', "on");


echo $cbas;


//NEW HERE

$now=time();
$now=date("Y-m-d H:i:s");
$checkTime=strtotime($now. "-1hour");
$checkTime=date("Y-m-d H:i:s",$checkTime);








echo "Start Time: ".$now."<br>";
echo "Check Time: ".$checkTime."<br>";
echo "<hr>";

?>