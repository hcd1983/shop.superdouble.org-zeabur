<?php
	

	class OrderListRow{
		
		public $SingleOrder=array();

		function CreateRowArray($row){

			$thisOrder=new PaynowOrder;
			$thisOrder -> orderinfo = $row;
			$thisOrder ->PaynowInfoForOrderList();

			$btn='';
	
			if(isRole(["admin","soldier"])){
				$btn.="<div style='margin:5px 0'>".'<a href="javascript:void(0)" data-href="temp/ajax.php?OrderNo='.$thisOrder -> OrderNo.'" data-lightbox="ajax"  onclick="SetActiveRow($(this))" class="editbtn btn btn-primary" data-OrderNo="'.$thisOrder -> OrderNo.'">編輯</a>'."</div>";
				
			}

			if(isRole(array("admin"))){
			
				$btn.="<div style='margin:5px 0'>".'<button type="button" class="btn btn-danger" onclick="removeThisRow($(this))" data-OrderNo="'.$thisOrder -> OrderNo.'">刪除</button>'."</div>";

			}

			$checkbox='<input id="cb-select-'.$thisOrder -> OrderNo.'" type="checkbox" name="order[]" value="'.$thisOrder -> OrderNo.'">';

			$this->SingleOrder["checkbox"]= $checkbox; 

			$this->SingleOrder["OrderNo"]= $thisOrder -> OrderNo; 
			$this->SingleOrder["BuysafeNo"]= $thisOrder -> BuysafeNo; 
			$this->SingleOrder["reg_date"]= $thisOrder -> reg_date; 
			
			$this->SingleOrder["buyerInfo"]= $thisOrder -> buyerInfo; 
			$this->SingleOrder["receiverInfo"]= $thisOrder -> receiverInfo; 
			$this->SingleOrder["receiptInfo"]= $thisOrder -> receiptInfo; 	
			$this->SingleOrder["CargoList"]= $thisOrder -> CargoList; 
			
			$row["discount"]=!$row["discount"]?0:$row["discount"];
            $row["shippingFee"]=!$row["shippingFee"]?0:$row["shippingFee"];
            $row["TotalPrice"] = !$row["TotalPrice"]?0:$row["TotalPrice"];

			$this->SingleOrder["shopping_info"]= "商品: ".number_format($row["TotalPrice"]-$row["shippingFee"]+$row["discount"])."<br>";
			$this->SingleOrder["shopping_info"].= "運費: ".number_format($row["shippingFee"])."<br>";
			
			$this->SingleOrder["shopping_info"].= "折扣: ".number_format($row["discount"]);

			$this->SingleOrder["TotalPrice"]=$thisOrder -> TotalPrice;
			
			$this->SingleOrder["PayType"]= $thisOrder -> PayType;
			
			$this->SingleOrder["PayInfo"]= "<div>".$thisOrder -> TranStatus."</div>"."<div>".$thisOrder ->ErrDesc."</div>"; 

			if($thisOrder -> isDue == true ){
				$this->SingleOrder["PayInfo"].="<div style='color:red;'>過期</div>";
			}


			$this->SingleOrder["SendStatus"]= $thisOrder -> SendStatus;
			$this->SingleOrder["ShippingNum"]= $thisOrder -> ShippingNum;
			$this->SingleOrder["Note1"]= $thisOrder -> Note1;
			$this->SingleOrder["memo"]= $thisOrder -> memo;

			$this->SingleOrder["edit"]= $btn;

			return $this->SingleOrder;

		}


	}

?>