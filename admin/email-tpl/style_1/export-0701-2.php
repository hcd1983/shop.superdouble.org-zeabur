<?php require_once("functions.php"); 

if(isset($_REQUEST["cargos"]) && $_REQUEST["cargos"] !=""){
	$cargos=$_REQUEST["cargos"];
}else{
	exit("CargoName?");
}


$cargos=explode(",", $cargos);

notLogin("login.php");

$emails=array();
foreach ($cargos as $key => $cargo) {
	$val=urlencode($cargo);
	$val=str_replace("[","\[",$val);
	$val=str_replace("_","\_",$val);
	$val=str_replace("%","\%",$val);
	$sql="SELECT `bemail`,CONVERT(SUBSTRING_INDEX(`TotalPrice`,'-',-1),UNSIGNED INTEGER) AS `mynum` FROM `orders` WHERE (`OrderInfo` LIKE '%".$val."%') AND  (`TranStatus` LIKE 'S') ORDER BY `mynum` DESC";  
	$orderinfo=doSQLgetRow($sql);
	$emails=array_merge($emails,$orderinfo);

}
echo count($emails);
echo "<pre>";
var_dump($emails);
echo "</pre>";
exit;



  

//echo $sql;
//exit;
$export=new exportCSV;

function CallBack(){
	global $export,$cargo;
	$export -> customCSV("有買".$cargo);
}


$orderinfo=doSQLgetRow($sql);
$AllOrders=doSQLgetRow($sql);

//echo count($orderinfo);
//exit;

//print_r($AllOrders);

$export -> Tarray =$AllOrders;
//$export -> ktjCSV();
CallBack();
$export ->array_to_csv_download();

exit();