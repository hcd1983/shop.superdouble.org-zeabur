<?php
require_once("../functions.php");
//include('../soap/nusoap.php');





/*

$OrderNo=$_GET["OrderNo"];

$abc=new dbCheck;
$abc-> Action($OrderNo);

*/



//NEW HERE


$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `SendStatus` NOT LIKE '%DbChecked%'";


$exportSql=$sql;
$orderinfo=doSQLgetRow($sql);


foreach($orderinfo as $key => $row){
	
	echo $row["OrderNo"]."<br>";

	$abc=new dbCheck;
	echo $abc-> Action($row["OrderNo"]);

	echo "<hr>";
}





/*

$URL = "http://www.paynow.com.tw/paynowapi.asmx?wsdl";
$client = new nusoap_client($URL, 'wsdl');
$client->soap_defencoding = 'utf-8';
$client->decode_utf8 = false;
$client->xml_encoding = 'utf-8';
$param = array('WebNo'=>'24751852', 'OrderNo'=>'AA041920t');
$result = $client->call('Sel_PaymentRespCode', $param);
if(!$client->fault AND !$client->getError())
{
	$paynow_creditcard_data = $result;
}
*/



//print_r($paynow_creditcard_data);




?>

