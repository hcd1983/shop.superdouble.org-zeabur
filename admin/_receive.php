<?php 
ini_set('display_errors', 0);
require_once("functions.php" );
date_default_timezone_set('Asia/Taipei');

$arr=array();

if(!isset($_REQUEST['OrderNo']) ||  $_REQUEST['OrderNo']==""){
	exit("系統發生不明錯誤!");
}

//共用參數
$arr["BuysafeNo"]=$_REQUEST['BuysafeNo']; //PayNow訂單編號
$arr["OrderNo"]=$_REQUEST['OrderNo'];   //商家自訂訂單編號
$arr["TranStatus"]=$_REQUEST['TranStatus'];//交易結果 S 表交易成功；F表交易失敗
$arr["ReturnPrice"]=$_REQUEST['TotalPrice'];//交易金額
$arr["PayType"]=$_REQUEST['PayType']; //付款方式 01：信用卡 ,02：WebATM ,03：虛擬帳號



//信用卡 01

$arr["ErrDesc"]=$_REQUEST['ErrDesc']; //當  TranStatus為 F  時，將回傳錯誤訊息（失敗原因）於此

//分期信用卡
$arr["Installment"]=$_REQUEST['Installment'];


//虛擬帳戶 03 和 05 IBON
$arr["ATMNo"]=$_REQUEST['ATMNo'];//虛擬帳號號碼
$arr["IBONNO"]= $_REQUEST['IBONNO'];//ibon
$arr["BankCode"]=$_REQUEST['BankCode'];//ibon


$arr["NewDate"]=$_REQUEST['NewDate']; //虛擬帳號產生日期(繳款日)(yyyy/mm/ddhh:mm:ss)
$arr["DueDate"]=$_REQUEST['DueDate']; //虛擬帳號繳款期限(yyyy/mm/dd)

//超商條碼 10

$arr["BarCode1"] =$_REQUEST['BarCode1'];//虛擬帳號號碼
$arr["BarCode2"] =$_REQUEST['BarCode2'];//虛擬帳號號碼
$arr["BarCode3"] =$_REQUEST['BarCode3'];//虛擬帳號號碼


//echo "<pre>";
//print_r($_POST);
//print_r($arr);
//echo "</pre>";
//01 或 11

if( ($arr["PayType"] == "01" || $arr["PayType"] == "11") && !isset($_REQUEST['method'])):	
						
		insertInto($arr,["OrderNo"],$dbset['table']['orders']);
		if($arr["TranStatus"] =="S"){

			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];
			Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=false);
			
			OrderSuccessMsgSlack($arr["OrderNo"]);
			Reduce_store_for_Wordpress($arr["OrderNo"]);
		}else{
			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];
			Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=true);

			Addback_store_for_Wordpress($arr["OrderNo"]);
		}
				
endif;



if(isset($_REQUEST['method']) && $_REQUEST['method']=="paynow_return"):

	if($arr["TranStatus"] =="S" && ($arr["PayType"] == "01" || $arr["PayType"] == "11")):

		insertInto($arr,["OrderNo"],$dbset['table']['orders']);

		if($arr["TranStatus"] =="S"){
			
			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];
			Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=false);

			OrderSuccessMsgSlack($arr["OrderNo"]);
			Reduce_store_for_Wordpress($arr["OrderNo"]);
		}else{
			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];
			Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=true);

			Addback_store_for_Wordpress($arr["OrderNo"]);
		}

		exit("1");
	
	else:
		exit("0");
	endif;	

endif;


?>

<?php

//其他情況
if($arr["PayType"] != "01" && $arr["PayType"] != "11"):

//共用參數
$arr["BuysafeNo"]=explode(',',trim(urldecode($arr["BuysafeNo"])));
$arr["OrderNo"]=explode(',',trim(urldecode($arr["OrderNo"])));
$arr["TranStatus"]=explode(',',trim(urldecode($arr["TranStatus"])));
$arr["ReturnPrice"] =explode(',',trim(urldecode($arr["ReturnPrice"])));
$arr["PayType"] =explode(',',trim(urldecode($arr["PayType"])));

//虛擬帳戶 03

$arr["BankCode"]=explode(',',trim(urldecode($arr["BankCode"])));
$arr["ATMNo"]=explode(',',trim(urldecode($arr["ATMNo"])));
$arr["NewDate"]=explode(',',trim(urldecode($arr["NewDate"])));
$arr["DueDate"]=explode(',',trim(urldecode($arr["DueDate"])));

//ibon 05
$arr["IBONNO"]= explode(',',trim(urldecode($arr["IBONNO"])));

//超商條碼 10
$arr["BarCode1"] =explode(',',trim(urldecode($arr["BarCode1"])));
$arr["BarCode2"] =explode(',',trim(urldecode($arr["BarCode2"])));
$arr["BarCode3"] =explode(',',trim(urldecode($arr["BarCode3"])));






	foreach($arr["OrderNo"] as $key =>$order):
		
		
		
		$update=array();

		
		foreach($arr as $tag => $val):

			$update[$tag]=urlencode($val[$key]);

		endforeach;	

		unset($update["PayType"]);
		$update["OrderNo"]=$order;

		
		insertInto($update,["OrderNo"],$dbset['table']['orders']);

		if($update["TranStatus"] =="S"){

			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$update["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];

			Coupon_For_Wordpress($update["OrderNo"],$coupon,$fail_coupon=false);

			OrderSuccessMsgSlack($update["OrderNo"]);
			Reduce_store_for_Wordpress($update["OrderNo"]);

		
		}else{

			$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$update["OrderNo"]."'";
			$coupon=doSQLgetRow($sql)[0]["coupon"];

			Coupon_For_Wordpress($update["OrderNo"],$coupon,$fail_coupon=true);

			Addback_store_for_Wordpress($update["OrderNo"]);



		}
	
	endforeach;	


endif;


//只回傳一個
if(count($arr["OrderNo"]) == 1):


	$the_OrderNo=$_REQUEST['OrderNo'];
	
	
	
	$sql="SELECT `return_url` FROM `orders` WHERE `OrderNo` LIKE '".$the_OrderNo."'";


	if( count(doSQLgetRow($sql)) == 0 ){

		$action_url="thanks.php";
		
	}else{

		$buyerMail= new AllroverServiceMail;

		$buyerMail -> OrderNo =$the_OrderNo;

		$buyerMail -> buyMail();

		$action_url=doSQLgetRow($sql)[0]["return_url"];


	}


?>	
	<form id="form1" action="<?php echo $action_url;?>" method="post">
		<input type="hidden" name="super" value="<?php echo $_REQUEST['OrderNo']; ?>">
		<?php //echo $info_input;?>
	</form>
	
	<script type="text/javascript">
    	document.getElementById('form1').submit(); // SUBMIT FORM
	</script>
<?php

endif;
?>


