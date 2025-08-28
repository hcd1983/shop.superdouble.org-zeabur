<?php



class AllroverMail {
    
     public $header=_WebTitle." 自動發信";
     public $msg1="感謝您對 "._WebTitle." 的支持";
     public $reminder;
     public $force=false;
     public $rules='<h3>【條款與細則】</h3>
     				<ol style="padding-left: 15px;">
							<li><strong>付款</strong>：於本網站購買商品時，您將透過立吉富PayNow 金流服務將保留您的個人資料以及交易記錄。您有責任維護您帳戶資料的保密性，包含您的密碼及電腦連線之安全性，並同意承擔您的帳戶與密碼內所有動作的責任。</li>
							<li><strong>隱私政策</strong>：您所提供的個人資料將完全保密並僅於處理您的訂單時使用。我們絕不會共享，售出或是以任何其他方式泄露您的個人資料。如果您對於您的隱私保護尚有疑問或擔憂，煩請與我們聯繫。</li>
							<li><strong>準據法及割離</strong>：本網站之相關條款，就無關法律衝突之事項，將適用台灣之法律規範，而不適用於聯合國國際貨物銷售合同公約。若您為台灣地區（本島以及外島）之消費者，則本條款適用於您所在區域之司法管轄範圍內。若司法管轄權之法院，無論基於任何理由，判定本條款內任何條文或條款無效，均不影響本條款內其他條文之有效性。</li>
							<li>我們將保留隨時變更本網站政策及條款與細則之權利。如其中條文被視為失效，無效或出於任何原因無法執行，此類條款之效力將可被分割，並不影響本合約其餘條文的有效性及可執行性。</li>
							</ol>';
     public $content;

     

	function mailContent( ) {

		$container='
     				<div style="font-family:Arial, Helvetica,微軟正黑體, sans-serif; font-size:13px; color:#000000;" >
     					<div style="max-width: 800px;margin: auto;padding: 100px 50px;">
	     					<div style="text-align:center;">
								<img src="http://shop.spinbox.cc/images/logo@2x_1.png" style="max-width:300px;">
							</div>

							<h1 style="text-align:center;font-size:25px;">'.$this->header.'</h1>
							
							<hr style="margin-bottom:50px;">
							<div style="max-width:700px;margin:auto;margin-bottom:50px;">
								<p style="font-size:16px;text-align:left;margin-bottom:50px;">'.$this->msg1.'</p>
								<div >
									'.$this->content.'
								</div>
     						</div>

     						<div style="font-size:16px;text-align:left;margin-bottom:50px;">'.$this->rules.'</div>
     						<div style="font-size:16px;text-align:left;margin-bottom:50px;">'.$this->reminder.'</div>
     						
			          		<hr style="margin-top:50px;">
			           		 <div style="text-align:center;">
				              <a style="font-size:14px;color:#555;padding:0 15px;text-align:center;display:inline-block;border-right:1px solid #555;" href="http://spinbox.cc" target="_blank">官網</a>
				              <a style="font-size:14px;color:#555;padding:0 15px;text-align:center;display:inline-block;border-right:1px solid #555;" href="https://www.facebook.com/Spinbox.cc/" target="_blank">Facebook</a>
							  <a style="font-size:14px;color:#555;padding:0 15px;text-align:center;display:inline-block;border-right:1px solid #555;" href="https://shop.allrover.cc" target="_blank">購物專區</a>
				            </div>  
			          		<hr>
							  
     					</div>
     				</div>
     				';			
     				

		$msg=$this->header.$this->content.$this->footer;

	    return  $container;
	}


};


class AllroverServiceMail{

	public $OrderNo;
	public $TestMode;

	function buyMail(){



		$OrderNo=$this-> OrderNo;
		
		global $dbset;

		$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";
		$orderinfo=doSQLgetRow($sql)[0];

		$OneOrder = new PaynowOrder;
		$OneOrder  -> orderinfo = $orderinfo;
		$OneOrder  -> PaynowInfo();

		$Buy = new AllroverMail;

		$Buy -> header = _WebTitle." 訂 單 通 知 信";
		$Buy -> msg1 = "親愛的 "._WebTitle." 購買人您好:<br><br>

						非常感謝您選購 "._WebTitle." 系列商品，請確認您的訂購內容及金額。<br><br>

						提醒您本通知函只是通知您本系統已經收到您的訂購訊息、並供您再次核對之用，不代表交易已經完成，選擇匯款或ibon繳費的朋友請特別留意繳納期限，交易成功後約1-5個工作日左右會發送電子發票到您的Email信箱，關於付款與出貨請詳閱「條款及細則」。";


		$Buy -> reminder =	"";


		$buymsg=$orderinfo["PayType"]=="01" || $orderinfo["PayType"]=="11"?"":'<tr bgcolor="#ffffff"> 
																					<td width="85" height="65" align="center" class="m_938273667354807077text12">
																						<font >交 易 資 訊</font>
																					</td>
																					<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
																						<font >'.$OneOrder  ->PayInfo.' </font> 
																					</td>
																				</tr>';

		$Err=$orderinfo["ErrDesc"]=="" ?"":'<tr bgcolor="#ffffff"> 
												<td width="85" height="65" align="center" class="m_938273667354807077text12">
													<font >錯 誤 訊 息</font>
												</td>
												<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
													<font style="color:red;">'.$OneOrder  ->ErrDesc.' </font> 
												</td>
											</tr>';																		

		$Buy -> content ='<table width="700" border="1" cellpadding="8" cellspacing="0">
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
										<font>'.$OneOrder  -> OrderNo.'</font>
									</td>
								</tr>
								<tr> 
									<td width="85" align="center" bgcolor="#ffffff" nowrap="">
										<font  class="m_938273667354807077text12">Paynow<br> 編 號</font>
									</td>
									<td width="577" bgcolor="#ffffff">
										<font>'.$OneOrder  -> BuysafeNo.'</font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center">
										<font >訂 購 日 期</font>
									</td>
									<td height="20" class="m_938273667354807077text12">
										<font >'.$OneOrder  ->reg_date.'</font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >訂 單 明 細</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OneOrder  ->CargoList.' </font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >運 費</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OneOrder  ->shippingFee.' </font>
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >交 易 金 額</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff"> 
										<font > '.$OneOrder  ->TotalPrice.' </font>
									</td>
								</tr>					
								<tr bgcolor="#ffffff"> 
									<td width="85" height="65" align="center" class="m_938273667354807077text12">
										<font >付 款 方 式</font>
									</td>
									<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
										<font >'.$OneOrder  ->PayType.' </font> 
									</td>
								</tr>
								'.$buymsg.'
								
								<tr bgcolor="#ffffff"> 
									<td width="85" height="65" align="center" class="m_938273667354807077text12">
										<font >付 款 狀 態</font>
									</td>
									<td height="65" class="m_938273667354807077text12" bgcolor="#ffffff">
										<font >'.$OneOrder  ->TranStatus.' </font> 
									</td>
								</tr>
								'.$Err.'
								
				
							
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font >訂購人資料</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OneOrder  ->buyerInfo.'
									</td>
								</tr>

								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font>收貨人資料</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OneOrder  ->receiverInfo.'
									</td>
								</tr>
								<tr bgcolor="#ffffff"> 
									<td width="85" height="20" align="center" class="m_938273667354807077text12">
										<font>備註</font>
									</td>
									<td height="20" class="m_938273667354807077text12" bgcolor="#ffffff">
										'.$OneOrder  ->Note1.'
									</td>
								</tr>
				
							</tbody></table>';
					
		
		$sentto= $OneOrder  ->buyerMail;
		$receiver= $OneOrder  ->buyerName;
		$msg= $Buy -> mailContent();

		$subject="感謝您訂購 "._WebTitle." 商品，附上您的訂單資訊予您參考 - 訂單編號(".$OrderNo.")";
		//echo $subject;

		global $wordpress_setting;
		if(isset($wordpress_setting["active_mail"]) && $wordpress_setting["active_mail"] == 1 &&  isset($wordpress_setting["mail_url"]) && $wordpress_setting["mail_url"] != ""){
			$WP_OrderInfo=array(
				"OrderNo"=>$this-> OrderNo,
				"BuysafeNo"=>$OneOrder-> BuysafeNo,
				"name"=>$OneOrder->buyerName,
				"reg_date"=>$OneOrder->reg_date,
				"shippingFee"=>$OneOrder->shippingFee,
				"TotalPrice"=>$OneOrder->TotalPrice,
				"PayType"=>$OneOrder->PayType,
				"CargoList"=>$OneOrder->CargoList,
				"buymsg"=>$buymsg,
				"Err"=>$Err,
				"TranStatus"=>$OneOrder->TranStatus,
				"buyerInfo"=>$OneOrder  ->buyerInfo,
				"receiverInfo"=>$OneOrder->receiverInfo,
				"Note1"=>$OneOrder->Note1
			);

			$postdata=array("action"=>"MailTpls",
					"tpl"=>"buy_mail",
					"OrderInfo"=>$WP_OrderInfo,
			);

			$url=$wordpress_setting["mail_url"];

			$curl = curl_init(); //开启curl	
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);		
			curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postdata));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
			$obj = curl_exec($curl); //执行curl操作
			curl_close($curl);
			$obj=json_decode($obj,true);
			$subject=$obj["subject"];
			$msg=str_replace('\"', '"', $obj["msg"]);
		}
		
		if($this->TestMode== true){
			echo "<h3>".$subject."</h3>";
			echo "<div>".$msg."</div>";
			exit;
		}



		if(strpos($orderinfo["SendStatus"], 'S1') === false || $buy -> force == true){

			$resault=sendmail($sentto,$receiver,$subject,$msg);
			
			if($resault=="S" && $OneOrder ->SendStatus ==""){
				$arr["OrderNo"]=$OrderNo;
				$arr["SendStatus"]="S1";

				AddIfNotExist($dbset["table"]["orders"],"SendStatus","S1","OrderNo",$OrderNo);
				
			}

		}


	}	



};
