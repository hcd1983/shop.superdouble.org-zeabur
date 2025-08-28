<?php
header("Content-Type:text/html; charset=utf-8");



class PaynowOrder {
     
     public $orderinfo;	
     public $OrderNo;
     public $BuysafeNo;
     public $reg_date;
     public $PayType;
     public $TotalPrice;
     public $TranStatus;
     public $ErrDesc;
     public $SendStatus;
     public $ShippingNum; 
     public $SentLog;
     public $PayInfo;

     public $DueDate;
     public	$DueDateCheck;
     public $isDue=false;

     public $CargoList;
     public $shippingFee;
     public $buyerInfo;
     public $buyerMail;
     public $buyerName;
     public $receiverInfo;
     public $receiverMail;
     public $receiptInfo;

     public $Note1;


    function Common(){
    	global $TransCode,$dbset;
    	$orderinfo=$this->orderinfo;

    }
     

	function PaynowInfo() {
		
		$this->common();
		$this->OrderNo = $orderinfo["OrderNo"];
		$this->BuysafeNo = $orderinfo["BuysafeNo"]==""?false:$orderinfo["BuysafeNo"];
		$this->SendStatus = $orderinfo["SendStatus"];
		$this->Note1=nl2br(urldecode($orderinfo["Note1"]));

		$this->reg_date=date_format(date_create($orderinfo["reg_date"]),'Y-m-d');

		$this->PayType = $TransCode["PayType"][$orderinfo["PayType"]];
	    $this->TotalPrice = number_format($orderinfo["TotalPrice"]); 
	    $TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
	    $this->TranStatus = $orderinfo["TranStatus"]=="S"? "<span style='color:green;'>".$TranStatusT."</span>":"<span style='color:red;'>".$TranStatusT."</span>";	   	    
	    $this->ErrDesc = urldecode($orderinfo["ErrDesc"]);	 


	    switch ($orderinfo["CargoSentList"]) {
	   		case "":
	   			$this->SentStatus="<span style='color:red;'>未出貨</span>";
	   			break;
	   		case $orderinfo["CargoList"]:
	   			$this->SentStatus="<span style='color:green;'>出貨完畢</span>";
	   			break;
	   		default:
	   			$this->SentStatus="<span style='color:orange;'>部分出貨</span>";
	   			break;
	   	}

	   	$this->ShippingNum = $orderinfo["ShippingNum"];
   		$this->SentLog =$orderinfo["SentLog"];

	    $DueDate=urldecode($orderinfo["DueDate"]);
	    $DueDate=date_create($DueDate);
	    $now=date("Y-m-d H:i:s");	    

	    $this->DueDate=$orderinfo["DueDate"]==""?"":date_format($DueDate,'Y-m-d H:i:s'); 

	    if($this->DueDate !="" && strtotime($now) >  strtotime($DueDate)  ){
	    	$this->DueDateCheck= $this->DueDate ."<br><span style='color:red'>過期</span>";
	    	$this->isDue=true;
	    }

   		switch ($orderinfo["PayType"]) {
    	    	
	    	case '03':
	    		
	    		$payInfo.="<strong>銀行代碼</strong> <br>"."<span>".$orderinfo["BankCode"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>轉帳帳戶</strong> <br>"."<span>".$orderinfo["ATMNo"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";	    		
	  	    	

	    		$payInfo.="<p style='color:#ff5c4b;'>*金額超過 $30,000 請至臨櫃匯款。</p>";
	    		break;
	    	case '05':
	    		
	    		$payInfo.="<strong>IBON 代碼</strong> <br>"."<span>".$orderinfo["IBONNO"]."</span>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";
	    		break;
	    	case '10':	    		
	    		$payInfo.="<strong>條碼1</strong> <br>"."<span>".$orderinfo["BarCode1"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>條碼2</strong> <br>"."<span>".$orderinfo["BarCode2"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>條碼3</strong> <br>"."<span>".$orderinfo["BarCode3"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";	
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";    		
	    		break;			
	    	
	    	default:
	    		$payInfo="";
	    		break;

	    }

	    $this -> PayInfo = $payInfo;

	    $CargoList=unserialize($orderinfo["CargoList"]);
	    $orderlist="<ul class=\"orderinfo\" style='width:450px;max-width:100%;'>";
	    foreach ($CargoList as $key => $val) {
			$title= urldecode($val["title"]);
			$subtotal=$val["price"]*$val["amount"];			
			$orderlist.="<li>".$title." x ".number_format($val["amount"])."<span style='float:right'>".number_format($subtotal)."</span>"."</li>";
		}
		$orderlist.="</ul>";
	    $this -> CargoList = $orderlist;

	    $this ->shippingFee=$orderinfo["shippingFee"] > 0 ? number_format($orderinfo["shippingFee"]):"0";
	    
	    //訂購人
		$buyer=unserialize($orderinfo["buyer"]);
		$buyer=urldecodeArray($buyer);

		//print_r($buyer);
	   
	    $buyerInfo="<strong style='margin-right:20px;'>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</strong> "."<span>".$buyer["bname"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.="<strong style='margin-right:20px;'>電&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;話</strong> "."<span>".$buyer["bphone"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.="<strong style='margin-right:20px;'>電子信箱</strong> "."<span>".$buyer["bemail"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.=$buyer["zip"]!=""?"<strong style='margin-right:20px;'>郵遞區號</strong> "."<span>".$buyer["zip"]."</span>":"";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.="<strong style='margin-right:20px;'>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</strong> "."<span>".$buyer["address"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.=$buyer["receipt"]!=""?"<strong style='margin-right:20px;'>統一編號</strong> "."<span>".$buyer["receipt"]."</span>":"";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.=$buyer["company"]!=""?"<strong style='margin-right:20px;'>公司抬頭</strong> "."<span>".$buyer["company"]."</span>":"";
	  
	    $this->buyerMail=$buyer["bemail"];
	    $this->buyerName=$buyer["bname"];


	    $this -> buyerInfo = $buyerInfo;  

	     //收件人
	    $receiver=unserialize($orderinfo["receiver"]);
		$receiver=urldecodeArray($receiver);
		//print_r($receiver);
		$receiverInfo="<h3>收件人</h3>";
		if(count($receiver)==0){
			$receiverInfo="<h4>同購買人</h4>";
		}else{
			$receiverInfo.="<ul class=\"orderinfo\" style='max-width:100%;'>";
			$receiverInfo.="<li>"."姓名"."<span style='float:right'>".$receiver["rname"]."</span>"."</li>";
			$receiverInfo.="<li>"."電話"."<span style='float:right'>".$receiver["rphone"]."</span>"."</li>";
			$receiverInfo.="<li>"."E-Mail"."<span style='float:right'>".$receiver["remail"]."</span>"."</li>";
			$receiverInfo.=$receiver["rzip"]!=""?"<li>"."郵遞區號"."<span style='float:right'>".$receiver["rzip"]."</span>"."</li>":"";
			$receiverInfo.="<li>"."地址"."<span style='float:right'>".$receiver["raddress"]."</span>"."</li>";
			$receiverInfo.="</ul>";

			$receiverInfo="<strong style='margin-right:20px;'>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</strong> "."<span>".$receiver["rname"]."</span>";
		    $receiverInfo.="<div style='margin-top:10px'>";
		    $receiverInfo.="<strong style='margin-right:20px;'>電&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;話</strong> "."<span>".$receiver["rphone"]."</span>";
		    $receiverInfo.="<div style='margin-top:10px'>";
		    $receiverInfo.="<strong style='margin-right:20px;'>電子信箱</strong> "."<span>".$receiver["remail"]."</span>";
		    $receiverInfo.="<div style='margin-top:10px'>";
		    $receiverInfo.=$receiver["rzip"]!=""?"<strong style='margin-right:20px;'>郵遞區號</strong> "."<span>".$receiver["rzip"]."</span>":"";
		    $receiverInfo.="<div style='margin-top:10px'>";
		    $receiverInfo.="<strong style='margin-right:20px;'>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</strong> "."<span>".$receiver["raddress"]."</span>";

		    $this->receiverMail=$receiver["remail"];
		}

		$this -> receiverInfo = $receiverInfo;  
	}


	function PaynowInfoForOrderList() {
		global $TransCode,$dbset;
		//$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$this->OrderNo."' LIMIT 1";
		//$orderinfo=doSQLgetRow($sql)[0];
		$orderinfo=$this->orderinfo;
		$this->OrderNo = $orderinfo["OrderNo"];
		$this->BuysafeNo = $orderinfo["BuysafeNo"]==""?false:$orderinfo["BuysafeNo"];
		$this->SendStatus = $orderinfo["SendStatus"];
		$this->Note1=nl2br(urldecode($orderinfo["Note1"]));
		$this->reg_date=date_format(date_create($orderinfo["reg_date"]),'Y-m-d');

		$this->PayType = $TransCode["PayType"][$orderinfo["PayType"]];
	    $this->TotalPrice = number_format($orderinfo["TotalPrice"]); 
	    $TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
	    $this->TranStatus = $orderinfo["TranStatus"]=="S"? "<span style='color:green;'>".$TranStatusT."</span>":"<span style='color:red;'>".$TranStatusT."</span>";	   	    
	    $this->ErrDesc = "<div style='color:red;font-weight:bold;'>".urldecode($orderinfo["ErrDesc"])."</div>";	 


	    switch ($orderinfo["CargoSentList"]) {
	   		case "":
	   			$this->SentStatus="<span style='color:red;'>未出貨</span>";
	   			break;
	   		case $orderinfo["CargoList"]:
	   			$this->SentStatus="<span style='color:green;'>出貨完畢</span>";
	   			break;
	   		default:
	   			$this->SentStatus="<span style='color:orange;'>部分出貨</span>";
	   			break;
	   	}

	   	$this->ShippingNum = $orderinfo["ShippingNum"];
   		$this->SentLog =$orderinfo["SentLog"];

	    $DueDate=urldecode($orderinfo["DueDate"]);
	    $DueDate=date_create($DueDate);
	    $now=date("Y-m-d H:i:s");	    

	    $this->DueDate=$orderinfo["DueDate"]==""?"":date_format($DueDate,'Y-m-d H:i:s'); 

	    if($this->DueDate !="" && strtotime($now) >  strtotime($DueDate)  ){
	    	$this->DueDateCheck= $this->DueDate ."<br><span style='color:red'>過期</span>";
	    	$this->isDue=true;
	    	//$this->PayType= $this->PayType ."<br><span style='color:red'>過期</span>";
	    }

   		switch ($orderinfo["PayType"]) {
    	    	
	    	case '03':
	    		
	    		$payInfo.="<strong>銀行代碼</strong> <br>"."<span>".$orderinfo["BankCode"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>轉帳帳戶</strong> <br>"."<span>".$orderinfo["ATMNo"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";	    		
	  	    	

	    		$payInfo.="<p style='color:#ff5c4b;'>*金額超過 $30,000 請至臨櫃匯款。</p>";
	    		break;
	    	case '05':
	    		
	    		$payInfo.="<strong>IBON 代碼</strong> <br>"."<span>".$orderinfo["IBONNO"]."</span>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";
	    		break;
	    	case '10':	    		
	    		$payInfo.="<strong>條碼1</strong> <br>"."<span>".$orderinfo["BarCode1"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>條碼2</strong> <br>"."<span>".$orderinfo["BarCode2"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";
	    		$payInfo.="<strong>條碼3</strong> <br>"."<span>".$orderinfo["BarCode3"]."</span><br>";
	    		$payInfo.="<div style='margin-top:10px'>";	
	    		$payInfo.="<strong>截止日期</strong> <br>"."<span>".$this->DueDate."</span><br>";    		
	    		break;			
	    	
	    	default:
	    		$payInfo="";
	    		break;

	    }

	    $this -> PayInfo = $payInfo;

	    $CargoList=unserialize($orderinfo["CargoList"]);
	    $orderlist="<ul class=\"orderinfo\">";
	    foreach ($CargoList as $key => $val) {
			$title= urldecode($val["title"]);
			$subtotal=$val["price"]*$val["amount"];			
			$orderlist.="<li>".$title." x ".number_format($val["amount"])."</li>";

		}
		$orderlist.="</ul>";
	    $this -> CargoList = $orderlist;

	    $this ->shippingFee=$orderinfo["shippingFee"] > 0 ? number_format($orderinfo["shippingFee"]):"0";
	    
	    //訂購人
		$buyer=unserialize($orderinfo["buyer"]);
		$buyer=urldecodeArray($buyer);

		//print_r($buyer);
	   
	    $buyerInfo="<div>".$buyer["bname"]."</div>";
	    $buyerInfo.="<div>".$buyer["bphone"]."</div>";
	    $buyerInfo.="<div>".$buyer["bmail"]."</div>";
	    $buyerInfo.="<div>".$buyer["zip"]."</div>";
	    $buyerInfo.="<div>".$buyer["address"]."</div>";


	   
	    $receiptInfo=$buyer["receipt"]!=""?"<div>".$buyer["receipt"]."</div>":"";
	   
	    $receiptInfo.=$buyer["company"]!=""?"<div>".$buyer["company"]."</div>":"";

	    $this->receiptInfo=$receiptInfo;
	  
	    $this->buyerMail=$buyer["bemail"];
	    $this->buyerName=$buyer["bname"];


	    $this -> buyerInfo = $buyerInfo;  

	     //收件人
	    $receiver=unserialize($orderinfo["receiver"]);
		$receiver=urldecodeArray($receiver);
		//print_r($receiver);
		$receiverInfo="<h3>收件人</h3>";
		if(count($receiver)==0){
			$receiverInfo="<h4>同購買人</h4>";
		}else{
		

			$receiverInfo="<div>".$receiver["rname"]."</div>";		   
		    $receiverInfo.="<div>".$receiver["rphone"]."</div>";
		    $receiverInfo.="<div>".$receiver["remail"]."</div>";
		    $receiverInfo.=$receiver["rzip"]!=""?"<div>".$receiver["rzip"]."</div>":"";		  
		    $receiverInfo.="<div>".$receiver["raddress"]."</div>";

		    $this->receiverMail=$receiver["remail"];
		}

		$this -> receiverInfo = $receiverInfo;  
	}


		


	


}





?>