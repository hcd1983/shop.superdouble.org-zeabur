<?php 
ini_set('display_errors', 0);
require_once("functions.php");
notLogin("login.php");

$type=$_GET["type"];

$sql=urldecode($_POST["sql"]);
if(isset($_POST["order"])){
	$orders=$_POST["order"];	
}else{
	$orders= "";
}


$export=new exportCSV;

switch ($type) {

	case 'all':
		$sql="SELECT * FROM `orders`  ORDER BY `id`  DESC LIMIT 5000";

		function CallBack(){
			global $export;
			$export -> allCSV();
		}
		
		break;	

	case 'ktj':
		$sql="SELECT * FROM `orders` WHERE `ShippingNum` LIKE '' AND `SendStatus` NOT LIKE '%KTJ%' AND `TranStatus` LIKE '%S%' ORDER BY `id`  DESC LIMIT 5000";
		function CallBack(){
			global $export;
			$export -> ktjCSV();
		}
		
		break;

	case 'now':
		function CallBack(){

			global $export;
			$export -> allCSV();
		}
		
		break;	

		
	default:

		function CallBack(){
			global $export;
			$export -> ktjCSV();
		}
		
		break;
}


//$orderinfo=doSQLgetRow($sql);
$AllOrders=doSQLgetRow($sql);


if(is_array($orders) && count($orders) > 0){

	

	$AllOrders=array_filter($AllOrders, function ($val, $key) use ($orders) { // N.b. $val, $key not $key, $val
			        
			        return in_array($val["OrderNo"], $orders);
			    });

	    
}





//print_r($AllOrders);

$export -> Tarray =$AllOrders;
//$export -> ktjCSV();
CallBack();
$export ->array_to_csv_download();

exit();