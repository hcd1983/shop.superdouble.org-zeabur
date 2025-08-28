<?php require_once("functions.php"); ?>
<?php 
 	  ini_set("display_errors", 1);
	  notLogin("login.php"); 
	  $role=array();
	  //$role[]="super";
	  notRole($role);	
?>
<?php 
	if(isset($_POST["setting"])):

		$val=urlencodeArray($_POST["setting"]);
		$val=serialize($val);
		

		$arr=array(
				"setting"=>"phpmailer",
				"val"=>$val
			);

		insertInto($arr,["setting"],$dbset["table"]["settings"]);	

	
	endif;

	if(isset($_POST["test"])):


		$test=$_POST["test"];
		//print_r($test);

		if(sendmail($test["sentto"],$test["receiver"],$test["subject"],$test["msg"]) =="S"):

			echo "<script>alert('測試信已寄出!')</script>";

		endif;	
	
	endif;	

	 $setting=getSettingVal("phpmailer");
?>
<?php require_once("temp/manage-header.php"); ?>
<?php
								
	$arr=array(
            "useLaravelApi"=>array(
                "label"=>"使用 laravel 的 api 寄信（1: yes 0 :no）",
                "type"=>"text",
            ),
            "laravelApiCustomerMail"=>array(
                "label"=>"laravel api 的客戶信端口",
                "type"=>"text",
            ),
            "laravelApiSystemMail"=>array(
                "label"=>"laravel api 的管理員信端口",
                "type"=>"text",
            ),
			"host"=>array(
					"label"=>"host (EX: smtp.gmail.com)",
					"type"=>"text",

				),
			"port"=>array(
				"label"=>"Port (EX: gmail:587, bluehost: 26)",
				"type"=>"text",
			),
			"password"=>array(
				"type"=>"text",
				"label"=>"Password",
			),
			"username"=>array(
				"type"=>"text",
				"label"=>"主帳號",
			),
			"mailer"=>array(
				"type"=>"text",
				"label"=>"寄件人名稱",
			),
			"frommail"=>array(
				"type"=>"email",
				"label"=>"寄件人E-Mail",
			),
			"bccto"=>array(
				"type"=>"email",
				"label"=>"密件備份E-MAIL",
			)
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

	$testmail=array(
			"sentto"=>array(
					"label"=>"收件E-MAIL",
					"type"=>"text",
					"required"=>true,									
				),
			"receiver"=>array(
					"label"=>"收件人代稱",
					"type"=>"text",
					"required"=>true,						
				),
			"subject"=>array(
					"label"=>"測試信標題",
					"type"=>"text",
					"required"=>true,				
				),
			"msg"=>array(
				"label"=>"測試信內容",
				"type"=>"textarea",
				"required"=>true,	
			),
		);
	foreach($testmail as $key => $val):
		$testmail[$key]["name"]="test[".$key."]";
	endforeach;	

	$testmail=inputCreater($testmail);


?>

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>PHPMAILER 設定</h2>

				<form id="form" name="form" class="nobottommargin" action="#" method="post">

					<?php echo	$table; ?>					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	

				<div class="clear"></div>
				<form action="#" method="post">
					<?php echo	$testmail; ?>
					<input type="submit" value="發測試信" class="button button-blue  fright">
				</form>
			</div>	

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>