<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); 
	  $role=array();
	  //$role[]="super";
	  notRole($role);	
?>
<?php 
	if(isset($_POST["setting"])):

		$val=urlencodeArray($_POST["setting"]);
		$val=serialize($val);
		

		$arr=array(
				"setting"=>"paynow",
				"val"=>$val
			);

		insertInto($arr,["setting"],$dbset["table"]["settings"]);	

	
	endif;	

	 $setting=getSettingVal("paynow");
?>
<?php require_once("temp/manage-header.php"); ?>



<?php
								
	$arr=array(
			"WebNo"=>array(
					"label"=>"賣家登入帳號，如身分證開頭請為大寫傳送。",
					"type"=>"text",					
				),
			"ECPlatform"=>array(
				"label"=>"平台開發商公司名稱(例：XXX購物平台、智邦收銀機)",
				"type"=>"text",
				"placeholder"=>"XXX購物平台、智邦收銀機"
			),
			"shopkey"=>array(
				"label"=>"商家交易碼(金鑰)",
				"type"=>"text",
			),
			"AtmRespost"=>array(
				"type"=>"number",
				"label"=>"需回傳虛擬擬 帳號0 或 1；賣家需接虛擬帳號回傳參數請帶入 1 否則請帶入 0 。 (不帶入視為不接收)",
				"value"=>"1",
				"maxlength"=>"1",
				"max"=>"1",
				"min"=>"0"
			),
			"DeadLine"=>array(
				"type"=>"number",
				"label"=>"繳款期限 限數字，部分付款方式適用(ex :  超商代收，非安泰之虛擬帳號)；預設為 0",
				"value"=>"1",
				"maxlength"=>"1",
				"max"=>"1",
				"min"=>"0"
			),
			"PayEN"=>array(
				"type"=>"number",
				"label"=>"中英文付款頁面轉換 0：中文 1：英文",
				"value"=>"0",
				"maxlength"=>"1",
				"max"=>"1",
				"min"=>"0"
			),
			"serial"=>array(
				"label"=>"序號前贅字",
				"type"=>"text",
			),
			"dateToser"=>array(
				"type"=>"number",
				"label"=>"年份轉為英文 0:不轉 1:轉",
				"value"=>"0",
				"maxlength"=>"1",
				"max"=>"1",
				"min"=>"0"
			),
			"length"=>array(
				"type"=>"number",
				"label"=>"序號亂數長度",
				"value"=>"2",
				"maxlength"=>"10",
				"max"=>"10",
				"min"=>"2"
			),
		);

	foreach($arr as $key => $val):
		$arr[$key]["name"]="setting[".$key."]";
		if(!isset($arr[$key]["value"])){
			$arr[$key]["value"]="";
		}
		$arr[$key]["value"]=isset($setting[$key])?$setting[$key]:$arr[$key]["value"];
	endforeach;	
	$table=inputCreater($arr);
?>

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>Paynow 設定</h2>

				<form id="form" name="form" class="nobottommargin" action="#" method="post">

					<?php echo	$table; ?>

					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	
			</div>	

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>