<?php

$TransCode=array();


$TransCode["role"]=array(
		"super"=>"超級使用者",
		"admin"=>"管理員",
		"soldier"=>"工作人員"
	);

$TransCode["PayType"]=array(
		"01"=>"信用卡",
		"11"=>"信用卡分期",
		"03"=>"虛擬帳戶轉帳",
		"05"=>"I-BON",
		"10"=>"超商條碼"
	);
$TransCode["TranStatus"]=array(
		"S"=>"交易成功",
		"F"=>"交易未完成",
		""=>"未回傳"
	);
$TransCode["SendStatus"]=array(
	    "custom"=>"自訂訂單",
		"S1"=>"明細寄出",
		"TranS"=>"完成結帳",
		"DbChecked"=>"雙重確認",
		"KTJ"=>"已出貨",
		"SF"=>"已出貨",
		"ShipNum"=>"出貨單號",
		"StoreChecked"=>"扣除庫存",
	);

//$TransCode["SendStatusAdmin"]=["DbChecked"];

$TransCode["SendStatusHand"]=array(
		"custom"=>"自訂訂單",
		"S1"=>"明細寄出",
	//	"KTJ"=>"已交大榮",
		"SF"=>"已出貨",
		"DbChecked"=>"雙重確認",
	);

$TransCode["SendStatusLabel"]=array(
		"custom"=>"danger",
		"S1"=>"default",
		"TranS"=>"success",
		"KTJ"=>"primary",
		"SF"=>"primary",
		"ShipNum"=>"info",
		"DbChecked"=>"default",
		"StoreChecked"=>"default"
	);

$TransCode["process"]=array(
		//"KTJ"=>"交給大榮",
		//"RemoveKTJ"=>"未交給大榮",
		"SF"=>"已出貨",
		"RemoveSF"=>"未出貨",
		//"RemoveDbChecked"=>"取消雙重確認",
		"remove"=>"刪除"
	);



$TransCode["orderTitle"]=array(
		"checkbox"=>'<input id="cb-select-all" type="checkbox" onclick="SelectRemoveAll()">',
		"check"=>"勾選",
		"buyerInfo"=>"購買人資料",
		"receiverInfo"=>"收貨人資料",
		"OrderNo"=>"訂單編號",
		"BuysafeNo"=>"paynow單號",
		"reg_date"=>'下單日期',
		"receiptInfo"=>'統編抬頭',
		"TotalPrice"=>'交易金額',
		"edit"=>'操作',
		"Note1"=>'交易備註',
		"memo"=>'後台備註',
		"PayType"=>'付款方式',
		"PayInfo"=>'交易狀態',
		"CargoList"=>'貨品清單',
		"SendStatus"=>'訂單狀態',
		"shopping_info"=>"交易摘要",
        "bname" => "購買人",
        "bphone" => "買者電話",
        "bemail" => "買者信箱",
        "zip" => "買者郵遞區號",
        "address" => "買者地址",
        "receipt" => "統編",
        "company" => "抬頭",
        "rname" => "收件人",
        "rphone"=> "收件人電話",
        "remail" => "收件人信箱",
        "rzip" => "收件郵遞區號",
        "raddress" => "收件地址",
        "Product" => "產品",
        "shippingFee" => "運費",
        "TranStatus"=> "交易狀態",
        "isDue" => "是否逾期",
        "ShippingNum" => "物流單號"
	);
