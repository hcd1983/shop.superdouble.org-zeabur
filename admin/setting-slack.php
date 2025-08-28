<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); 
	  $role=array();
	  //$role[]="super";
	  notRole($role);	
?>
<?php 
	
	if( isset($_GET["action"]) && $_GET["action"] =="update"){
		if(isset($_POST["setting"])):

			$val=urlencodeArray($_POST["setting"]);
			$val=serialize($val);
			

			$arr=array(
					"setting"=>"slack",
					"val"=>$val
				);

			insertInto($arr,["setting"],$dbset["table"]["settings"]);		
		endif;
	}

	$setting=getSettingVal("slack");

	if( isset($_GET["action"]) && $_GET["action"] =="test"){
		if(isset($_POST["test"])):
			$test=$_POST["test"];		
			$slackMsg=new slack;
			$slackMsg->sendMsg($setting["token"],$setting["channel"],$test["text"],$setting["username"],$setting["icon_url"],$as_user=false);	
		endif;	
	} 
	
	if( isset($_GET["action"]) && $_GET["action"] =="OrderNoTest"){
		if(isset($_POST["OrderNo"])):
			$OrderNo=$_POST["OrderNo"];		
			OrderSuccessMsgSlack($OrderNo);	
		endif;	
	}
	
?>
<?php 

	require_once("temp/manage-header.php");
								
	$arr=array(
			"token"=>array(
				"label"=>"Token",
				"type"=>"text",
			),			
			"username"=>array(
				"label"=>"名稱",
				"type"=>"text",
			),
			"icon_url"=>array(
				"label"=>"icon網址",
				"type"=>"text",
			),
			"channel"=>array(
				"label"=>"Channel",
				"type"=>"text",
				"required"=>true,
			),
		);

	foreach($arr as $key => $val):
		$arr[$key]["name"]="setting[".$key."]";
		$arr[$key]["value"]=isset($setting[$key])?$setting[$key]:$arr[$key]["value"];
	endforeach;	
	$table=inputCreater($arr);
//--
	$testslack=array(
			
			"text"=>array(
					"label"=>"訊息",
					"type"=>"textarea",
					"required"=>true,				
				)
		);
	foreach($testslack as $key => $val):
		$testslack[$key]["name"]="test[".$key."]";
	endforeach;	
	$testslack=inputCreater($testslack);
//--

	$OrderNoTable=array(
			
			"OrderNo"=>array(
					"name"=>"OrderNo",
					"label"=>"訂單編號",
					"type"=>"text",
					"required"=>true,				
				)
		);

	$OrderNoTable=inputCreater($OrderNoTable);


?>

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>Slack 設定</h2>

				<form id="form" name="form" class="nobottommargin" action="?action=update" method="post">

					<?php echo	$table; ?>					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	

				<div class="clear"></div>
				<form action="?action=test" method="post">
					<?php echo	$testslack; ?>
					<input type="submit" value="發測試訊息" class="button button-blue  fright">
				</form>
				<div class="clear"></div>
				<form action="?action=OrderNoTest" method="post">
					<?php echo	$OrderNoTable; ?>
					<input type="submit" value="使用訂單編號測試" class="button button-blue  fright">
				</form>
			</div>	

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>