<?php ini_set('display_errors', 0); ?>
<?php 
header("Content-Type:text/html; charset=utf-8");
if(isset($_SERVER['HTTP_ORIGIN'])){
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

}

require_once('functions.php');

if(!isset($_REQUEST["super"]) && (!isset($_REQUEST["email"]) && !isset($_REQUEST["OrderNo"]))){
	exit;
}

if(isset($_REQUEST["super"])){
    $sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$_REQUEST["super"]."' LIMIT 1";
}

if(isset($_REQUEST["email"]) && isset($_REQUEST["OrderNo"])){

  $_email=urlencode($_REQUEST["email"]);
  $_OrderNo=urlencode($_REQUEST["OrderNo"]);
  $sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE (`OrderNo` LIKE '".$_REQUEST["OrderNo"]."' OR `BuysafeNo` LIKE '".$_REQUEST["OrderNo"]."') AND `ReceiverEmail` LIKE '".$_email."' LIMIT 1";
}  


$orderinfo=doSQLgetRow($sql)[0];
    //print_r($orderinfo);
if(isset($_REQUEST["type"]) && $_REQUEST["type"]=="full_info"){
  echo json_encode($orderinfo);
  exit;
}

    //Paynow

    $BuysafeNo = $orderinfo["BuysafeNo"]==""?"未產生":$orderinfo["BuysafeNo"];
    $reg_date=date_create($orderinfo["reg_date"]);
    $PayType = $TransCode["PayType"][$orderinfo["PayType"]];
    $TotalPrice="$".number_format($orderinfo["TotalPrice"]);  
    $TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
    $TranStatus = $orderinfo["TranStatus"]=="S"? "<span style='color:green;'>".$TranStatusT."</span>":"<span style='color:red;'>".$TranStatusT."</span>";
    $ErrDesc = $orderinfo["ErrDesc"]==""?"":"<li>"."錯誤訊息"."<span >".urldecode($orderinfo["ErrDesc"])."</span>"."</li>";



   //Paynow 訊息
    $paynowInfo="<h3>交易狀態</h3>";
    $paynowInfo.="<ul class=\"orderinfo\">";
    $paynowInfo.="<li>"."單號"."<span>".$_REQUEST["super"]."</span>"."</li>";
    $paynowInfo.="<li>"."Paynow單號"."<span>".$BuysafeNo."</span>"."</li>";
    $paynowInfo.="<li>"."交易日期"."<span>".date_format($reg_date,'Y-m-d')."</span>"."</li>";
    $paynowInfo.="<li>"."Paynow付款方式"."<span>".$PayType."</span>"."</li>";
    $paynowInfo.="<li>"."交易金額"."<span>".$TotalPrice."</span>"."</li>";
    $paynowInfo.="<li>"."交易狀態".$TranStatus."</li>";
    $paynowInfo.=$ErrDesc;    
    $paynowInfo.="</ul>";

    //出貨狀態
   
   	switch ($orderinfo["CargoSentList"]) {
   		case "":
   			$SentStatus="<span style='color:red;'>未出貨</span>";
   			break;
   		case $orderinfo["CargoList"]:
   			$SentStatus="<span style='color:green;'>出貨完畢</span>";
   			break;
   		default:
   			$SentStatus="<span style='color:orange;'>部分出貨</span>";
   			break;
   	}



   	$SentInfo="<h3>出貨狀態 ".$SentStatus."</h3>";
   	$Sentli="";

   	$ShippingNum = $orderinfo["ShippingNum"]==""?"":"<li>"."託運單號"."<span>".$orderinfo["ShippingNum"]."</span>"."</li>";
   	$SentLog =$orderinfo["SentLog"]==""?"":"<li>"."出貨紀錄"."<span>".$orderinfo["SentLog"]."</span>"."</li>"; 

   	$Sentli.=$ShippingNum;
   	$Sentli.=$SentLog;

   	if(strlen($Sentli) > 0 ){
   		$SentInfo.="<ul class=\"orderinfo\">";
   		$SentInfo.=$Sentli;
   		$SentInfo.="</ul>";
   	}



    //付款資訊

    $NewDate=urldecode($orderinfo["NewDate"]);
    $NewDate=date_create($NewDate);
    $NewDate=date_format($NewDate,'Y-m-d');

    $DueDate=urldecode($orderinfo["DueDate"]);
    $DueDate=date_create($DueDate);
    $DueDate=date_format($DueDate,'Y-m-d H:i:s');



    switch ($orderinfo["PayType"]) {
    	
    	
    	case '03':
    		$payInfo="<h3>轉帳資訊</h3>";
    		$payInfo.="<ul class=\"orderinfo\">";
    		$payInfo.="<li>"."銀行代碼"."<span>".$orderinfo["BankCode"]."</span>"."</li>";
    		$payInfo.="<li>"."轉帳帳戶"."<span>".$orderinfo["ATMNo"]."</span>"."</li>";
    		$payInfo.="<li>"."交易金額"."<span>".$TotalPrice."</span>"."</li>";
    		$payInfo.="<li>"."轉帳截止日"."<span>".$DueDate."</span>"."</li>";

    		$payInfo.="</ul>";

    		$payInfo.="<p style='color:#ff5c4b;'>*金額超過 $30,000 請至臨櫃匯款。</p>";
    		break;
    	case '05':
    		$payInfo="<h3>IBON 資訊</h3>";
    		$payInfo.="<ul class=\"orderinfo\">";
    		$payInfo.="<li>"."IBON 代碼"."<span>".$orderinfo["IBONNO"]."</span>"."</li>";
    		$payInfo.="<li>"."交易金額"."<span>".$TotalPrice."</span>"."</li>";
    		$payInfo.="<li>"."交易截止日"."<span>".$DueDate."</span>"."</li>";

    		$payInfo.="</ul>";
    		break;
    	case '10':
    		$payInfo="<h3>超商付款資訊</h3>";
    		$payInfo.="<ul class=\"orderinfo\">";
    		$payInfo.="<li>"."條碼1"."<span>".$orderinfo["BarCode1"]."</span>"."</li>";
    		$payInfo.="<li>"."條碼2"."<span>".$orderinfo["BarCode2"]."</span>"."</li>";
    		$payInfo.="<li>"."條碼3"."<span>".$orderinfo["BarCode3"]."</span>"."</li>";
    		$payInfo.="<li>"."交易金額"."<span>".$TotalPrice."</span>"."</li>";
    		$payInfo.="<li>"."交易截止日"."<span>".$DueDate."</span>"."</li>";
    		$payInfo.="</ul>";
    		break;			
    	
    	default:
    		$payInfo="";
    		break;
    }

    //購物明細

    $CargoList=unserialize($orderinfo["CargoList"]);

    

    $orderlist="<h3>購物明細</h3>";
	$orderlist.="<ul class=\"orderinfo\">";

	foreach ($CargoList as $key => $val) {
		$title= urldecode($val["title"]);

		$subtotal=$val["price"]*$val["amount"];
		
		$orderlist.="<li>".$title." x ".number_format($val["amount"])."<span>".number_format($subtotal)."</span>"."</li>";
	}

	$orderlist.=$orderinfo["shippingFee"] > 0 ? "<li>"."運費"."<span>".number_format($orderinfo["shippingFee"])."</span>"."</li>":"";

	$orderlist.="</ul>";


	//訂購人
	$buyer=unserialize($orderinfo["buyer"]);
	$buyer=urldecodeArray($buyer);

	//print_r($buyer);
	$buyerInfo="<h3>訂購人</h3>";
  $buyerInfo.="<ul class=\"orderinfo\">";
  $buyerInfo.="<li>"."姓名"."<span>".$buyer["bname"]."</span>"."</li>";
  $buyerInfo.="<li>"."電話"."<span>".$buyer["bphone"]."</span>"."</li>";
  $buyerInfo.="<li>"."E-Mail"."<span>".$buyer["bemail"]."</span>"."</li>";
  $buyerInfo.=$buyer["zip"]!=""?"<li>"."郵遞區號"."<span>".$buyer["zip"]."</span>"."</li>":"";
  $buyerInfo.="<li>"."地址"."<span>".$buyer["address"]."</span>"."</li>";
  $buyerInfo.=$buyer["receipt"]!=""?"<li>"."統一編號"."<span>".$buyer["receipt"]."</span>"."</li>":"";
  $buyerInfo.=$buyer["company"]!=""?"<li>"."公司抬頭"."<span>".$buyer["company"]."</span>"."</li>":"";
  $buyerInfo.="</ul>";

    //收件人
  $receiver=unserialize($orderinfo["receiver"]);
	$receiver=urldecodeArray($receiver);
	//print_r($receiver);
	$receiverInfo="<h3>收件人</h3>";
	if(count($receiver)==0){
		$receiverInfo.="<h4>同購買人</h4>";
	}else{
		$receiverInfo.="<ul class=\"orderinfo\">";
		$receiverInfo.="<li>"."姓名"."<span>".$receiver["rname"]."</span>"."</li>";
		$receiverInfo.="<li>"."電話"."<span>".$receiver["rphone"]."</span>"."</li>";
		$receiverInfo.="<li>"."E-Mail"."<span>".$receiver["remail"]."</span>"."</li>";
		$receiverInfo.=$receiver["rzip"]!=""?"<li>"."郵遞區號"."<span>".$receiver["rzip"]."</span>"."</li>":"";
		$receiverInfo.="<li>"."地址"."<span>".$receiver["raddress"]."</span>"."</li>";
		$receiverInfo.="</ul>";
	}

    $output=array();
    $output["paynow"]=urlencode($paynowInfo);
    $output["buyer"]=urlencode($buyerInfo);
    $output["receiver"]=urlencode($receiverInfo);
    $output["orderlist"]=urlencode($orderlist);
    $output["payInfo"]=urlencode($payInfo);
    $output["status"]=array(
      "PayType"=>$orderinfo["PayType"],
      "TranStatus"=>$orderinfo["TranStatus"],
    );
    

echo json_encode($output);
