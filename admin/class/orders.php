<?php




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
     public $buyerArray;	

     public $receiverInfo;
     public $receiverMail;
     public $receipverArray;

     public $receiptInfo;


     public $Note1;

     //forCSV
     public $cargoListArray;
     public $memo;


    function Common(){
    	global $TransCode,$dbset;
    	$orderinfo=$this->orderinfo;
    	$this->OrderNo = $orderinfo["OrderNo"];
		$this->BuysafeNo = $orderinfo["BuysafeNo"];
		$this->Note1=nl2br(urldecode($orderinfo["Note1"]));


		$this->reg_date=date_format(date_create($orderinfo["reg_date"]),'Y-m-d');
		
		if(!$orderinfo["PayType"]){
			$orderinfo["PayType"] = "01";
		}

		$this->PayType = $TransCode["PayType"][$orderinfo["PayType"]];
		$this ->shippingFee=$orderinfo["shippingFee"] > 0 ? number_format($orderinfo["shippingFee"]):"0";

		$orderinfo["TotalPrice"]=isset($orderinfo["TotalPrice"])&&$orderinfo["TotalPrice"]!=""?$orderinfo["TotalPrice"]:0;
		
		$this->TotalPrice = number_format($orderinfo["TotalPrice"]); 
	    $TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
	    $this->TranStatus = $orderinfo["TranStatus"]=="S"? "<span style='color:green;'>".$TranStatusT."</span>":"<span style='color:red;'>".$TranStatusT."</span>";	   	    
	    $this->ErrDesc = "<div style='color:red;font-weight:bold;'>".urldecode($orderinfo["ErrDesc"])."</div>";	

	    $DueDate=urldecode($orderinfo["DueDate"]);
	    $DueDate=date_create($DueDate);	   
	    $now=date("Y-m-d H:i:s");	    

	    $this->DueDate=$orderinfo["DueDate"]==""?"":date_format($DueDate,'Y-m-d H:i:s'); 

	    $this -> cargoListArray = $CargoList=unserialize($orderinfo["CargoList"]);


	    if($this->DueDate !="" &&
	     strtotime($now) >  strtotime($this->DueDate) && 
	     $orderinfo["TranStatus"] !="S"  ){
	    	$this->DueDateCheck= $this->DueDate ."<br><span style='color:red'>過期</span>";
	    	$this->isDue=true;
	    	//$this->PayType= $this->PayType ."<br><span style='color:red'>過期</span>";
	    }

	    $this->receiverArray=unserialize($orderinfo["receiver"]);
	    $this->buyerArray=unserialize($orderinfo["buyer"]);
	    $this->ShippingNum = urldecode($orderinfo["ShippingNum"]);

	    $this->memo=nl2br(urldecode($orderinfo["memo"]));

    }

    function forCSV(){
    	$this->common();
		global $TransCode,$dbset;
    	$orderinfo=$this->orderinfo;
    	$this->TotalPrice =$orderinfo["TotalPrice"]; 
    	$this->TranStatus = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
    	$this ->shippingFee=$orderinfo["shippingFee"] > 0 ? $orderinfo["shippingFee"]:"0";
    	$this->Note1=urldecode($orderinfo["Note1"]);
    	$this->memo=urldecode($orderinfo["memo"]);
	    
    }
     

	function PaynowInfo() {
		$this->common();
		global $TransCode,$dbset;
		$payInfo = "";
    	$orderinfo=$this->orderinfo;


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

	   	
   		$this->SentLog =$orderinfo["SentLog"];

	   

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
			$orderlist.="<li>".$title." x ".number_format($val["amount"])." <span style=\"margin-right:15px;float:right\">$".number_format($subtotal)."</span>"."</li>";
		}
		$orderlist.="</ul>";
	    $this -> CargoList = $orderlist;

	    
	    
	    //訂購人
		$buyer=unserialize($orderinfo["buyer"]);
		$buyer=urldecodeArray($buyer);

		//print_r($buyer);
	   
	    $buyerInfo="<strong style='margin-right:20px;'>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</strong> "."<span>".$buyer["bname"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'></div>";
	    $buyerInfo.="<strong style='margin-right:20px;'>電&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;話</strong> "."<span>".$buyer["bphone"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'></div>";
	    $buyerInfo.="<strong style='margin-right:20px;'>電子信箱</strong> "."<span>".$buyer["bemail"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'>";
	    $buyerInfo.=$buyer["zip"]!=""?"<strong style='margin-right:20px;'>郵遞區號</strong> "."<span>".$buyer["zip"]."</span>":"";
	    $buyerInfo.="<div style='margin-top:10px'></div>";
	    $buyerInfo.="<strong style='margin-right:20px;'>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</strong> "."<span>".$buyer["address"]."</span>";
	    $buyerInfo.="<div style='margin-top:10px'></div>";
	    $buyerInfo.=$buyer["receipt"]!=""?"<strong style='margin-right:20px;'>統一編號</strong> "."<span>".$buyer["receipt"]."</span>":"";
	    $buyerInfo.="<div style='margin-top:10px'></div>";
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
		    $receiverInfo.="<div style='margin-top:10px'></div>";
		    $receiverInfo.="<strong style='margin-right:20px;'>電&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;話</strong> "."<span>".$receiver["rphone"]."</span>";
		    $receiverInfo.="<div style='margin-top:10px'></div>";
		    $receiverInfo.="<strong style='margin-right:20px;'>電子信箱</strong> "."<span>".$receiver["remail"]."</span>";
		    $receiverInfo.="<div style='margin-top:10px'></div>";
		    $receiverInfo.=$receiver["rzip"]!=""?"<strong style='margin-right:20px;'>郵遞區號</strong> "."<span>".$receiver["rzip"]."</span>":"";
		    $receiverInfo.="<div style='margin-top:10px'></div>";
		    $receiverInfo.="<strong style='margin-right:20px;'>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</strong> "."<span>".$receiver["raddress"]."</span>";

		    $this->receiverMail=$receiver["remail"];
		}

		$this -> receiverInfo = $receiverInfo;  
	}


	function PaynowInfoForOrderList() {
		$this->common();
		global $TransCode,$dbset;
		$orderinfo=$this->orderinfo;
		
		

		$SendStatusArray=explode(" ", urldecode($orderinfo["SendStatus"]));
		$SendStatusLabel=array();


		if($this->orderinfo["ShippingNum"] !=""){
			$SendStatusArray[]="ShipNum";
		}

		if($this->orderinfo["TranStatus"] =="S"){
			$SendStatusArray[]="TranS";
		}

/*
		foreach($SendStatusArray as $key => $val){
			
			$trans=isset($TransCode["SendStatus"][$val]) ? $TransCode["SendStatus"][$val]:$val;

			$labelstyle=isset($TransCode["SendStatusLabel"][$val])? $TransCode["SendStatusLabel"][$val]:"default";

			$SendStatusLabel[] ='<span class="label label-'.$labelstyle.'">'.$trans.'</span>';


		}
*/
        if ($this->orderinfo["isShipped"] === 'S' ) {
            $SendStatusArray[] = 'SF';
        }

        foreach($TransCode["SendStatus"] as $key => $val){
			
			/*
			if(in_array($key, $TransCode["SendStatusAdmin"]) && isRole(["super"])==false){
				continue;
			}
			*/
			if(in_array($key, $SendStatusArray)){
				$trans=$val;
				$labelstyle=isset($TransCode["SendStatusLabel"][$key])? $TransCode["SendStatusLabel"][$key]:"default";
				$SendStatusLabel[] ='<span class="label label-'.$labelstyle.'">'.$trans.'</span>';
			}
		}



			
		

		$this->SendStatus = join("<div style='height:5px;'></div>",$SendStatusLabel);	



		
		//$this->SendStatus = $orderinfo["SendStatus"];
	
		
	    


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


	   	$shippingNumTrans=explode(" ",urldecode($orderinfo["ShippingNum"]));

	   	$this->ShippingNum="";

        if($this -> orderinfo['shippingCompany']) {
            $this->ShippingNum.="<div>".$this -> orderinfo['shippingCompany']."</div>";
        }

        foreach($shippingNumTrans as $key => $val){
	   		$this->ShippingNum.="<div>".$val."</div>";
	   	}

   		$this->SentLog =$orderinfo["SentLog"];
        
   		$payInfo = "";
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


	    if($orderinfo["CargoList"]!=""){
	    	 $CargoList=unserialize($orderinfo["CargoList"]);
	    }else{
	    	$CargoList=array();
	    }

	   	if(!is_array($CargoList)){
	   		$orderlist="";
	   	}else{
	   		 $orderlist="<ul class=\"orderinfo\">";
		    foreach ($CargoList as $key => $val) {
				$title= urldecode($val["title"]);
				$subtotal=$val["price"]*$val["amount"];			
				$orderlist.="<li>".$title." x ".number_format($val["amount"])."</li>";

			}
			$orderlist.="</ul>";

	   	}

	   
		
	    $this -> CargoList = $orderlist;

	    $this ->shippingFee=$orderinfo["shippingFee"] > 0 ? number_format($orderinfo["shippingFee"]):"0";
	    
	    //訂購人
		$buyer=unserialize($orderinfo["buyer"]);
		//$buyer=urldecodeArray($buyer);

		if(is_array($buyer) && count($buyer) > 0){
	    	$buyer=urldecodeArray($buyer);
	    }else{
	    	$buyer=array();
	    }

		//print_r($buyer);
	   
	    $buyerInfo="<div>".$buyer["bname"]."</div>";
	    $buyerInfo.="<div>".$buyer["bphone"]."</div>";
	    $buyerInfo.="<div>".$buyer["bemail"]."</div>";

	    if(!isset($buyer["zip"])){
	    	$buyer["zip"] = "";
	    }

	    $zip=$buyer["zip"]!=""?$buyer["zip"].", ":"";

	    if(!isset($buyer["address"])){
	    	$buyer["address"] = "";
	    }
	    
	    //$buyerInfo.="<div>".$buyer["zip"]."</div>";
	    $buyerInfo.="<div>".$zip.$buyer["address"]."</div>";

	    if(!isset($buyer["receipt"])){
	    	$buyer["receipt"] = "";
	    }
	   
	    $receiptInfo=$buyer["receipt"]!=""?"<div>".$buyer["receipt"]."</div>":"";

	     if(!isset($buyer["company"])){
	    	$buyer["company"] = "";
	    }
	   
	    $receiptInfo.=$buyer["company"]!=""?"<div>".$buyer["company"]."</div>":"";

	    $this->receiptInfo=$receiptInfo;
	  
	    $this->buyerMail=$buyer["bemail"];
	    $this->buyerName=$buyer["bname"];


	    $this -> buyerInfo = $buyerInfo;  

	     //收件人

	    if(isset($orderinfo["receiver"]) && $orderinfo["receiver"]!=""){
	    	 $receiver=unserialize($orderinfo["receiver"]);
	    }else{
	    	 $receiver=array();
	    }

	    if(is_array($receiver) && count($receiver) > 0){
	    	$receiver=urldecodeArray($receiver);
	    }else{
	    	$receiver=array();
	    }
		
		//print_r($receiver);
		$receiverInfo="<h3>收件人</h3>";
		if(count($receiver)==0){
			$receiverInfo="<h4>同購買人</h4>";
		}else{
		

			$receiverInfo="<div>".$receiver["rname"]."</div>";		   
		    $receiverInfo.="<div>".$receiver["rphone"]."</div>";
		    $receiverInfo.="<div>".$receiver["remail"]."</div>";

		    $zip=$receiver["rzip"]!=""?$receiver["rzip"].", ":"";
	    
		    //$buyerInfo.="<div>".$buyer["zip"]."</div>";
		    $receiverInfo.="<div>".$zip.$receiver["raddress"]."</div>";

		    //$receiverInfo.=$receiver["rzip"]!=""?"<div>".$receiver["rzip"]."</div>":"";		  
		    //$receiverInfo.="<div>".$receiver["raddress"]."</div>";

		    $this->receiverMail=$receiver["remail"];
		}

		$this -> receiverInfo = $receiverInfo;  
	}


		


	


}





?>