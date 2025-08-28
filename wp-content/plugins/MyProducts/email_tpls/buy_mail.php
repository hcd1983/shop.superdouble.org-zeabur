<?php

$status=array(
	"status"=>"F",
);

if(!isset($_POST["OrderInfo"])){
	echo json_encode($status);
	exit;
}

$mysetting=get_option('shop_mail');   
$msg=get_option('shop_mail_msg');

if(!isset($mysetting["logo"])){
	$logo="";
}else{
	$logo=$mysetting["logo"];
}


$OrderInfo = array(
	"OrderNo"=>"",
	"BuysafeNo"=>"",
	"name"=>"",
	"reg_date"=>"",
	"shippingFee"=>0,
	"TotalPrice"=>"",
	"PayType"=>"",
	"CargoList"=>"",
	"buymsg"=>"",
	"Err"=>"",
	"TranStatus"=>"",
	"buyerInfo"=>"",
	"receiverInfo"=>"",
	"Note1"=>""
);


$OrderInfo=array_merge($OrderInfo,$_POST["OrderInfo"]);

	$image_attributes = wp_get_attachment_image_src( $logo, $size="full" )[0];

	$OrderInfo_content='<table width="700" border="1" cellpadding="8" cellspacing="0" style="margin:30px auto;">
							<tbody>
								<tr> 
									<td bgcolor="#000" colspan="2" align="left">
										<font style="color:#FFF;">您的訂單資訊如下:</font>
									</td>
								</tr>
								<tr> 
									<td width="85" align="center" bgcolor="#ffffff" nowrap="">
										<font  class="m_938273667354807077text12">訂 單 編 號</font>
									</td>
									<td width="577" bgcolor="#ffffff">
										<font>'.$OrderInfo["OrderNo"].'</font>
									</td>
								</tr>
								<tr> 
									<td width="85" align="center" bgcolor="#ffffff" nowrap="">
										<font  class="m_938273667354807077text12">Paynow<br> 編 號</font>
									</td>
									<td width="577" bgcolor="#ffffff">
										<font>'.$OrderInfo["BuysafeNo"].'</font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center">
										<font >訂 購 日 期</font>
									</td>
									<td height="20" class="m_938273667354807077text12">
										<font >'.$OrderInfo["reg_date"].'</font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >訂 單 明 細</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OrderInfo["CargoList"].' </font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >運 費</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OrderInfo["shippingFee"].' </font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >交 易 金 額</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OrderInfo["TotalPrice"].' </font>
									</td>
								</tr>					
								<tr bgcolor="#ffffff"> 
									<td width="85" height="65" align="center" bgcolor="#ffffff" class="m_938273667354807077text12">
										<font >付 款 方 式</font>
									</td>
									<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
										<font >'.$OrderInfo["PayType"].' </font> 
									</td>
								</tr>
								'.$OrderInfo["buymsg"].'
								
								<tr bgcolor="#ffffff"> 
									<td width="85" height="65" align="center" class="m_938273667354807077text12">
										<font >付 款 狀 態</font>
									</td>
									<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
										<font >'.$OrderInfo["TranStatus"].' </font> 
									</td>
								</tr>
								'.$OrderInfo["Err"].'
								
				
							
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >訂購人資料</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OrderInfo["buyerInfo"].'
									</td>
								</tr>

								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font>收貨人資料</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OrderInfo["receiverInfo"].'
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font>備註</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OrderInfo["Note1"].'
									</td>
								</tr>
				
							</tbody></table>';

	if(!isset($mysetting["subject"])){
		$subject="";
	}else{
		$subject=$mysetting["subject"];	
	}
	
	$subject = str_replace("[OrderNo]",$OrderInfo["OrderNo"] , $subject);
	$subject = str_replace("[Receiver]",$OrderInfo["name"] , $subject);
	
	if($logo != ""){
		$logo_img="<img style='display:block;margin:auto;' src='".$image_attributes."'>";
	}else{
		$logo_img="";
	}
	$msg=str_replace("[OrderNo]",$OrderInfo["OrderNo"] , $msg);
	$msg=str_replace("[Receiver]",$OrderInfo["name"] , $msg);
	$msg=str_replace("[OrderInfo]",$OrderInfo_content , $msg);
	//$msg=nl2br($msg);
	$msg='<div style="font-family: Arial, Helvetica,微軟正黑體, sans-serif; font-size: 13px; color: #000000;">
<div style="max-width: 800px; margin: auto; padding: 100px 50px;">'.$logo_img.apply_filters("the_content",$msg)."</div></div>";

$status["status"] = "S";
$status["subject"] = $subject;
$status["msg"] = apply_filters("my_shop_mail",$msg,$OrderInfo);
echo json_encode($status);
exit;