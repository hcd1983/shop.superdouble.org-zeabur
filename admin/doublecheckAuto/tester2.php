<?php
require_once("../functions.php");


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');
header("Content-Type:text/html; charset=utf-8");


$OrderNo=$_GET["OrderNo"];
$TimeOut=$_GET["TimeOut"];

$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' AND `SendStatus` NOT LIKE '%DbChecked%' AND `TranStatus` NOT LIKE 'S'";

$orderinfo=doSQLgetRow($sql);

if(count($orderinfo) ==0 ){
	exit();
}


//$row=$orderinfo[0];



//ini_set('max_execution_time', 10800);
// Ignore user aborts and allow the script to run forever
ignore_user_abort(true);
 
// disable php time limit
set_time_limit(0);



$time = time() + $TimeOut;
while (time() < $time) {
    if ($t < time()) {
        echo date("h:i:sa")."<br>";
        ob_flush();
        flush();
    }
    $t = time();
}

//sleep($TimeOut);

$abc=new dbCheck;
$abc-> Action($OrderNo);

echo "done";


//echo $OrderNo;
?>