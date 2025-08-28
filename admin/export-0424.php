<?php require_once("functions.php"); ?>
<?php
notLogin("login.php");


$sql=$_POST["sql"];
$type=$_GET["type"];




$SENTED="AA04137Gh AA04160V8 AA0418qr5 AA0419199 AA04193C6 AA04196ob AA0419b8b AA0419bKb AA0419CWH AA0419JET AA0419OO5 AA0419Q4U AA0419rRo AA0419V4y AA0419Y75 AA04205Eh AA042084e AA0420RNf";
$NOTIN=explode(" ", $SENTED);


$sql="SELECT * FROM `orders` WHERE `ShippingNum` LIKE '' AND `TranStatus` LIKE '%S%' AND `OrderNo` NOT IN ( '" . implode($NOTIN, "', '") . "' ) ORDER BY `id`  DESC LIMIT 5000";    


$export=new exportCSV;

switch ($type) {
	case 'ktj':
		//$sql="SELECT * FROM `orders` WHERE `ShippingNum` LIKE '' AND `TranStatus` LIKE '%S%' ORDER BY `id`  DESC LIMIT 5000";
		function CallBack(){
			global $export;
			$export -> ktjCSV();
		}
		
		break;	
	default:

		function CallBack(){
			global $export;
			$export -> ktjCSV();
		}
		
		break;
}


$orderinfo=doSQLgetRow($sql);
$AllOrders=doSQLgetRow($sql);

//print_r($AllOrders);

$export -> Tarray =$AllOrders;
//$export -> ktjCSV();
CallBack();
$export ->array_to_csv_download();

exit();







?>