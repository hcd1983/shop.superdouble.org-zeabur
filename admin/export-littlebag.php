<?php 
ini_set('display_errors', 0);
require_once("functions.php");

$sql="SELECT * FROM `orders`  WHERE `OrderInfo` LIKE '%".urlencode("保溫款")."%' AND `TranStatus` LIKE 'S' ORDER BY `id`  DESC LIMIT 5000";

function CallBack(){
	global $export;
	$export -> allCSV();
}



//$orderinfo=doSQLgetRow($sql);
$AllOrders=doSQLgetRow($sql);








//print_r($AllOrders);
$export=new exportCSV;
$export -> Tarray =$AllOrders;
//$export -> ktjCSV();
CallBack();
$export ->array_to_csv_download();

exit();