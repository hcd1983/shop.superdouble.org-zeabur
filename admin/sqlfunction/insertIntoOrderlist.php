<?php

$functionUrl= dirname(dirname(__FILE__))."/functions.php";

require_once($functionUrl);

$OrderNo=$_POST["OrderNo"];

if(in_array("SF", $_POST["SendStatus"])) {
    $sql = "UPDATE `orders` SET `isShipped` = 'S' WHERE `orders`.`OrderNo` = '${OrderNo}'";
    $result=mysqli_query($db_conn, $sql);
}else {
    $sql = "UPDATE `orders` SET `isShipped` = 'F' WHERE `orders`.`OrderNo` = '${OrderNo}'";
    $result=mysqli_query($db_conn, $sql);
}

if(isset($_POST["SendStatus"])){
	$_POST["SendStatus"]=join(" ",$_POST["SendStatus"]);
}

$_receiver_datas = ["rname","rphone","remail","rzip","raddress"];

$_receiver = [];
foreach ($_receiver_datas as $key => $tag) {
	
	if(isset($_POST[$tag])){
		$_receiver[$tag] = urlencode($_POST[$tag]);
		
	}

	unset($_POST[$tag]);
}




function SuperUrlencodeArray($arr){

	foreach($arr as $key => $val):
		if(is_array($val)){

			$arr[$key]=SuperUrlencodeArray($arr);

		}else{

			

			$arr[$key]=urlencode($val);

		
		}
	endforeach;	

	return $arr;
}

$_POST = SuperUrlencodeArray($_POST);

$_POST["receiver"] = serialize($_receiver);

$arr = $_POST;




insertInto($arr,["OrderNo"],$dbset['table']['orders']);

//echo "S";




$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."'";
//mysqli_query('SET CHARACTER SET utf8');
$orderinfo=doSQLgetRow($sql);
$row=$orderinfo[0];
$SingleOrder=new OrderListRow;
$SingleOrder=$SingleOrder->CreateRowArray($row);


echo json_encode($SingleOrder);






?>