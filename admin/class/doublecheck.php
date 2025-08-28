<?php
class dbCheck{

	public $PaynowURL = "https://www.paynow.com.tw/paynowapi.asmx?wsdl";

	
	function Check($OrderNo){

		$URL=$this->PaynowURL;
		$WebNo=getSettingVal("paynow")["WebNo"];

		$client = new nusoap_client($URL, 'wsdl');
		$client->soap_defencoding = 'utf-8';
		$client->decode_utf8 = false;
		$client->xml_encoding = 'utf-8';
		$param = array('WebNo'=>$WebNo, 'OrderNo'=>$OrderNo);
		$result = $client->call('Sel_PaymentRespCode', $param);
		if(!$client->fault AND !$client->getError())
		{
			
			$paynow_creditcard_data = $result;
			return $paynow_creditcard_data;
		}else{

			return false;
		}

		
	}



	function Action($OrderNo){
		global $dbset;
		$resault = $this->Check($OrderNo);
		if($resault==false){
			return false;
		}else{
			$return=$resault["Sel_PaymentRespCodeResult"];
			$status=explode(",", $resault["Sel_PaymentRespCodeResult"]);
		}
		if(strlen($status[0]) == 1){

			$arr = array();
			$arr["OrderNo"] = $OrderNo;

			switch ($status[0]) {
				case '1':					
					// 1：交易成功；後為19碼paynow訂單編號以逗號分隔
					$BuysafeNo=explode("_", $status[1])[0];
					$arr["BuysafeNo"] = $BuysafeNo;
					$arr["TranStatus"] = "S";
					insertInto($arr,["OrderNo"],$dbset['table']['orders']);
					$sql = "SELECT `coupon`,`PayType` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
					$coupon = doSQLgetRow($sql)[0]["coupon"];
					$PayType = doSQLgetRow($sql)[0]["PayType"];
					Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=false);
					OrderSuccessMsgSlackDbCheck($OrderNo);
					Reduce_store_for_Wordpress($OrderNo);

//					 信用卡補寄成功信
					if ($PayType === "01" || $PayType === "11") {
						$buyerMail = new AllroverServiceMail;
						$buyerMail -> OrderNo = $OrderNo;
						$buyerMail -> buyMail();
					}
					//echo 1;

					break;

				case '2':

					$arr["TranStatus"]="F";
					$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
					$coupon=doSQLgetRow($sql)[0]["coupon"];
					Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=true);
					Addback_store_for_Wordpress($arr["OrderNo"]);

					break;	

				case '3':

					$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
					$coupon=doSQLgetRow($sql)[0]["coupon"];
					Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=true);
					Addback_store_for_Wordpress($arr["OrderNo"]);
					//3：退貨交易；逗號分隔退貨狀態(0:買家申請退貨 1:買賣家確認 2:銀行退款 3.賣家申請)

					//echo 3;

					break;

				case '4':

					$sql="SELECT `coupon` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
					$coupon=doSQLgetRow($sql)[0]["coupon"];
					Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=true);
					Addback_store_for_Wordpress($arr["OrderNo"]);
					//4：交易失敗(無交易訂單)，ex: 定單待確認，使用者可能未送出授權，無paynow交易訂單，無法確認狀態
					//echo 4;

					break;			
				
				default:
					
					

				break;
			
			}
			
		}else{

				// 其它：02, 03...(重覆交易)
				//	02 成功交易兩筆
				//	03 成功交易三筆  以此類推
				//	；後為paynow訂單編號以逗號分隔
				//	Ex：兩筆重覆交易02,5000001111146998321,5000001111146699323
				


		}

		AddIfNotExist($dbset["table"]["orders"],"SendStatus","DbChecked","OrderNo",$OrderNo);

		return $return;

	}

	function Action_CheckButNotDouble($OrderNo){

		global $dbset;

		$resault=$this->Check($OrderNo);

		if($resault==false){
			return false;
		}else{
			
			$return=$resault["Sel_PaymentRespCodeResult"];
			$status=explode(",", $resault["Sel_PaymentRespCodeResult"]);
		}

		if(strlen($status[0]) == 1){

			$arr=array();
			$arr["OrderNo"] = $OrderNo;

			switch ($status[0]) {
				case '1':					
					// 1：交易成功；後為19碼paynow訂單編號以逗號分隔

					$BuysafeNo = explode("_", $status[1])[0];
					$arr["BuysafeNo"] = $BuysafeNo;
					$arr["TranStatus"] = "S";

					insertInto($arr,["OrderNo"],$dbset['table']['orders']);

					$sql = "SELECT `coupon`,`PayType` FROM `orders` WHERE `OrderNo` LIKE '".$arr["OrderNo"]."'";
					$coupon = doSQLgetRow($sql)[0]["coupon"];
					$PayType = doSQLgetRow($sql)[0]["PayType"];
					Coupon_For_Wordpress($arr["OrderNo"],$coupon,$fail_coupon=false);
					Reduce_store_for_Wordpress($OrderNo);
					OrderSuccessMsgSlackDbCheck($OrderNo);
					//					 信用卡補寄成功信
					if ($PayType === "01" || $PayType === "11") {
						$buyerMail = new AllroverServiceMail;
						$buyerMail -> OrderNo = $OrderNo;
						$buyerMail -> buyMail();
					}

					echo "<span style='color:red'>Success</span>"."<br>";

					//echo 1;

					break;

				case '2':
					//2：交易失敗(有paynow定單，授權失敗或者未完成)


					$arr["TranStatus"]="F";
					
					//insertInto($arr,["OrderNo"],$dbset['table']['orders']);

					

					//echo 2;

					break;	

				case '3':
					//3：退貨交易；逗號分隔退貨狀態(0:買家申請退貨 1:買賣家確認 2:銀行退款 3.賣家申請)

					//echo 3;

					break;

				case '4':
					//4：交易失敗(無交易訂單)，ex: 定單待確認，使用者可能未送出授權，無paynow交易訂單，無法確認狀態



					//echo 4;

					break;			
				
				default:
					
					

				break;
			
			}
			
		}else{

				// 其它：02, 03...(重覆交易)
				//	02 成功交易兩筆
				//	03 成功交易三筆  以此類推
				//	；後為paynow訂單編號以逗號分隔
				//	Ex：兩筆重覆交易02,5000001111146998321,5000001111146699323
				


		}

		return $return;

	}


	
}


?>

