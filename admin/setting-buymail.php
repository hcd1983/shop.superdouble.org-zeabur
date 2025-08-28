<?php require_once("functions.php"); ?>
<?php 
 	  ini_set("display_errors", 0);	
	  notLogin("login.php"); 
	  $role=["admin","wp_user"];
	  //$role[]="super";
	  notRole($role);	

?>
<?php 
	if(isset($_POST["setting"])):

		$val=urlencodeArray($_POST["setting"]);
		$val=serialize($val);
		

		$arr=array(
				"setting"=>"buymail",
				"val"=>$val
			);

		insertInto($arr,["setting"],$dbset["table"]["settings"]);	

	
	endif;

	if(isset($_GET["test"])):

		// $sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `slackcheck` NOT LIKE 'S' AND `Note1` NOT LIKE '' LIMIT 1";
		// $orderinfo = doSQLgetRow($sql)[0];
		// sendbuymail($orderinfo);

		$OrderNo = $_GET["test"];
		OrderSuccessMsgSlack($OrderNo);
		$buymail_setting = getSettingVal("buymail");
		// $sendto = $buymail_setting["mailto"];
		// $_sendto = explode("\r\n", $sendto);

		// $subject = "測試用的 EMAIL 標題".date ("Y-m-d H:i:s") ;
		// $msg = "測試用的EMAIL";
		// $receiver = "";

		// foreach ($_sendto as $key => $_mailto) {
			
		// 	$_mailto = trim($_mailto);
			
			
			
		// 	if(sendmail($_mailto,$receiver,$subject,$msg) =="S"):

		// 		echo "寄件成功 - ".$_mailto."<br>";

		// 	endif;	


		// }
				
		echo "<script>alert('測試信已寄出!')</script>";
		echo "<script>window.location.replace('setting-buymail.php');</script>";
	endif;	

	 $setting=getSettingVal("buymail");
?>
<?php require_once("temp/manage-header.php"); ?>
<?php
								
	$arr=array(
			
			
			"mailto"=>array(
				"type"=>"textarea",
				"label"=>"寄出到 E-Mail ，使用換行隔開",
			),
		);

	foreach($arr as $key => $val):
		$arr[$key]["name"]="setting[".$key."]";
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

				<h2>Email 寄送設定</h2>

				<form id="form" name="form" class="nobottommargin" action="setting-buymail.php" method="post">

					<?php echo	$table; ?>					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	

				<div class="clear"></div>
				
			</div>	

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>