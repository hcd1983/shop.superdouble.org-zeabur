<?php




class exportCSV {

    public $type;
    public $title="export.csv";
    public $delimiter=",";
    public $Tarray;


    function ktjCSV(){
    	$time=date('ymdHis');

    	$outputArray=array();


    	foreach($this->Tarray as $key => $val){



    		$ktjOutput=new PaynowOrder;
	    	$ktjOutput -> orderinfo=$val;
	    	$ktjOutput -> forCSV();
	    	$cargoList=$ktjOutput -> cargoListArray;
	    	$buyerArr=$ktjOutput -> buyerArray;
	    	$receiverArr=$ktjOutput -> receiverArray;


	    	foreach($cargoList as $key =>$val){
	    			$singleOutput=array();



	    			$singleOutput["OrderNo"]=$ktjOutput -> OrderNo;
	    			$singleOutput["BuysafeNo"]="'".$ktjOutput -> BuysafeNo;


	    			$singleOutput["bname"]=urldecode($buyerArr["bname"]);
	    			$singleOutput["bphone"]="'".urldecode($buyerArr["bphone"]);
	    			$singleOutput["bemail"]=urldecode($buyerArr["bemail"]);
	    			$singleOutput["zip"]="'".urldecode($buyerArr["zip"]);
	    			$singleOutput["address"]=urldecode($buyerArr["address"]);
	    			$singleOutput["receipt"]=urldecode($buyerArr["receipt"]);
	    			$singleOutput["company"]=urldecode($buyerArr["company"]);


	    			$singleOutput["rname"]=urldecode($receiverArr["rname"]);
	    			$singleOutput["rphone"]="'".urldecode($receiverArr["rphone"]);
	    			$singleOutput["rmail"]=urldecode($receiverArr["remail"]);
	    			$singleOutput["rzip"]="'".urldecode($receiverArr["rzip"]);
	    			$singleOutput["raddress"]=urldecode($receiverArr["raddress"]);

	    			$singleOutput["Note1"]=$ktjOutput -> Note1;


	    			$singleOutput["Product"]=urldecode($val["title"]);
	    			$singleOutput["amount"]=$val["amount"];
	    			$singleOutput["shippingFee"]=$ktjOutput -> shippingFee;
	    			$singleOutput["TotalPrice"]=$ktjOutput -> TotalPrice;
	    			$singleOutput["PayType"]=$ktjOutput -> PayType;
	    			$singleOutput["TranStatus"]=$ktjOutput -> TranStatus;

	    			$singleOutput["memo"]=$ktjOutput -> memo;

	    			if($ktjOutput -> isDue == true ){
	    				$singleOutput["isDue"]="過期";
	    			}else{
	    				$singleOutput["isDue"]="";
	    			}


	    			$singleOutput["ShippingNum"]=$ktjOutput -> ShippingNum;

	    			$outputArray[]=	$singleOutput;
	    	}


    	}


    	$this->Tarray =	$outputArray;
    	$this->title="forKTJ_".$time.".csv";
    	$this->unshiftArrayKey();




    }


    function allCSV(){
    	$time=date('ymdHis');

    	$outputArray=array();


    	foreach($this->Tarray as $key => $val){



    		$ktjOutput=new PaynowOrder;
	    	$ktjOutput -> orderinfo=$val;
	    	$ktjOutput -> forCSV();
	    	$cargoList=$ktjOutput -> cargoListArray;
	    	$buyerArr=$ktjOutput -> buyerArray;
	    	$receiverArr=$ktjOutput -> receiverArray;



	    			$singleOutput=array();



	    			$singleOutput["OrderNo"]=$ktjOutput -> OrderNo;
	    			$singleOutput["BuysafeNo"]="'".$ktjOutput -> BuysafeNo;


	    			$singleOutput["bname"]=urldecode($buyerArr["bname"]);
	    			$singleOutput["bphone"]="'".urldecode($buyerArr["bphone"]);
	    			$singleOutput["bemail"]=urldecode($buyerArr["bemail"]);

	    			$address_info_tag = ["zip","address","receipt","company"];

	    			foreach ($address_info_tag as $key => $tag) {
	    				if(isset($buyerArr[$tag])){
	    					$singleOutput[$tag] = urldecode($buyerArr[$tag]);
	    				}else{
	    					$singleOutput[$tag]= "";
	    				}
	    			}

	    			// $singleOutput["zip"]="'".urldecode($buyerArr["zip"]);
	    			// $singleOutput["address"]=urldecode($buyerArr["address"]);
	    			// $singleOutput["receipt"]=urldecode($buyerArr["receipt"]);
	    			// $singleOutput["company"]=urldecode($buyerArr["company"]);

//	    			$receiver_info_tag = ["rname","rphone","rmail","rzip","raddress"];

                    $singleOutput["rname"] = $receiverArr["rname"] ? urldecode($receiverArr["rname"]) : $singleOutput["bname"];
                    $singleOutput["rphone"] = $receiverArr["rphone"] ? "'".urldecode($receiverArr["rphone"]) : $singleOutput["bphone"];
                    $singleOutput["remail"] = $receiverArr["remail"] ? urldecode($receiverArr["remail"]) : $singleOutput["bemail"];
                    $singleOutput["rzip"] = $receiverArr["rzip"] ? urldecode($receiverArr["rzip"]) : $singleOutput["zip"];
                    $singleOutput["raddress"] = $receiverArr["raddress"] ? urldecode($receiverArr["raddress"]) : $singleOutput["address"];

//	    			foreach ($receiver_info_tag as $key => $tag) {
//	    				if(isset($receiverArr[$tag])){
//	    					$singleOutput[$tag] = urldecode($receiverArr[$tag]);
//	    				}else{
//	    					$singleOutput[$tag]= "";
//	    				}
//	    			}


	    			$singleOutput["Note1"]=$ktjOutput -> Note1;

	    			$cargo = [];
	    			if (is_array($cargoList)) {
                        foreach($cargoList as $key =>$val){
                            $cargo[] = urldecode($val["title"])."x".$val["amount"];
                            //$singleOutput["amount"]=$val["amount"];
                        }
                    } else {
                        $cargo[] = '資料庫錯誤';
                    }
	    			$singleOutput["Product"]=join("\r\n",$cargo);

	    			$singleOutput["shippingFee"]=$ktjOutput -> shippingFee;
	    			$singleOutput["TotalPrice"]=$ktjOutput -> TotalPrice;
	    			$singleOutput["PayType"]=$ktjOutput -> PayType;
	    			$singleOutput["TranStatus"]=$ktjOutput -> TranStatus;

	    			$singleOutput["memo"]=$ktjOutput -> memo;

	    			if($ktjOutput -> isDue == true ){
	    				$singleOutput["isDue"]="過期";
	    			}else{
	    				$singleOutput["isDue"]="";
	    			}


	    			$singleOutput["ShippingNum"]=$ktjOutput -> ShippingNum;

	    			$outputArray[]=	$singleOutput;



    	}


    	$this->Tarray =	$outputArray;
    	$this->title="export_".$time.".csv";
    	$this->unshiftArrayKey();




    }





    function unshiftArrayKey(){
    	global $TransCode;
    	$header=array();
    	$headerTrans=array();

    	foreach ($this->Tarray[0] as $key => $val){
    		$header[]=$key;
    		if(isset($TransCode["orderTitle"][$key])):
				$headerTrans[]=$TransCode["orderTitle"][$key];
			else:
				$headerTrans[]=$key;
			endif;

    	}


//    	array_unshift($this->Tarray , $header);
    	array_unshift($this->Tarray , $headerTrans);

    }

    function changeval($val){

		// $val=mb_convert_encoding($val, 'big5', 'UTF-8');

		return $val;
	}


    function array_to_csv_download() {
		//header("content-type:application/csv;charset=UTF-8");
		//header('Content-Disposition: attachment; filename="'.$filename.'";');

		$array=$this->Tarray;
		$delimiter=$this->delimiter;

		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$this->title);
		header("Content-Type: application/vnd.ms-excel;");
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0');
		header('Pragma:public');

		// open the "output" stream
		// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
		$f = fopen('php://output', 'w');
		echo "\xEF\xBB\xBF";
		// fwrite($f, "\xEF\xBB\xBF");
		foreach ($array as $line) {

			foreach ($line as $key => $val) {


				$line[$key]=$this->changeval($val);

			}

			fputcsv($f, $line, $delimiter);
		}


	}




}





?>
