<?php
ini_set('display_errors', 1);
require_once("functions.php" );

$defaultSetting = [
    "start_at" => "2021-06-22",
];
$start_at = isset($_GET["start_at"]) && $_GET["start_at"] ? $_GET["start_at"] : $defaultSetting["start_at"];

$OrderNo = 'SD21071977';
//$sql = "SELECT * FROM `orders` WHERE `OrderNo` LIKE '{$OrderNo}'";
//$data = unserialize(doSQLgetRow($sql)[0]['CargoList']);
//echo "<pre>";
//var_dump($data);
//echo "</pre>";
$sql = "SELECT * FROM `orders` WHERE `reg_date` >= '{$start_at} 00:00:00' AND `TranStatus` LIKE 'S' AND `ShippingNum` NOT LIKE '' ORDER BY `id` DESC LIMIT 999";
$data = doSQLgetRow($sql);
$_data = array_map( function($d){
    return $d['OrderNo'];
},$data);
echo "<pre>";
echo json_encode($_data);
//var_dump($_data);
echo "</pre>";