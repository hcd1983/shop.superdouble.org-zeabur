<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); 
	  $role=array("admin");
	  //$role[]="super";
	  notRole($role);	

	$install_sql="SELECT 1 FROM stripe LIMIT 1;";
	
	if(!mysqli_query($db_conn, $install_sql)){
		insert_sql_file("stripe/stripe.sql");
		exit;
	}

	$setting_key="stripe";

	if(isset($_POST["setting"])):

		$val=urlencodeArray($_POST["setting"]);
		$val=serialize($val);
		

		$arr=array(
				"setting"=>$setting_key,
				"val"=>$val
			);

		insertInto($arr,["setting"],$dbset["table"]["settings"]);	

	
	endif;	

	 $setting=getSettingVal($setting_key);
?>
<?php require_once("temp/manage-header.php"); ?>



<?php
								
	$arr=array(
			"token"=>array(
				"label"=>"Publishable key",
				"type"=>"text"
			),

			"secret_token"=>array(
				"label"=>"Secret key",
				"type"=>"text"
			),
			
			"token_test"=>array(
				"label"=>"Publishable key for test",
				"type"=>"text",
			),
			"secret_token_test"=>array(
				"label"=>"Secret key for test",
				"type"=>"text",
			),
			"mode"=>array(
				"label"=>"模式",
				"type"=>"options",
				"options"=>array(
					""=>"不啟用",
					"test"=>"測試模式",
					"active"=>"正式模式"	
				),
			),
			"url"=>array(
				"label"=>"付款網址",
				"type"=>"text",
			),

		);

	foreach($arr as $key => $val):
		$arr[$key]["name"]="setting[".$key."]";
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

				<h2>STRIPE 設定</h2>

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