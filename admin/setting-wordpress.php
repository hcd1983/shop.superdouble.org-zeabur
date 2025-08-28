<?php require_once("functions.php"); ?>
<?php ini_set("display_errors", 1);?>
<?php notLogin("login.php"); 
	  $role=array("admin");
	  //$role[]="super";
	  notRole($role);	


	$setting_key="wordpress";

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
			"url"=>array(
					"label"=>"網址首頁",
					"type"=>"text",					
				),
			"mail_url"=>array(
					"label"=>"WP ajax 網址",
					"type"=>"text",					
				),
			"key"=>array(
				"label"=>"金鑰",
				"type"=>"text"
			),
			
			"active"=>array(
				"label"=>"是否啟用 WP登入",
				"type"=>"text",
				"placeholder"=>"1:啟用"
			),

			"active_mail"=>array(
				"label"=>"是否啟用 WP 的 Email 範本",
				"type"=>"text",
				"placeholder"=>"1:啟用"
			),

			"search_page"=>array(
				"label"=>"搜尋結果的網址",
				"type"=>"text",
				"placeholder"=>"http://shop.spinbox.cc/search.php"
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
?>

<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>Wordpress 設定</h2>

				<form id="form" name="form" class="nobottommargin" action="#" method="post">

					<?php echo	$table; ?>

					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	
			</div>	

		</div>

	</div>

</section><!-- #content end -->
<?php
/*
$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` LIKE 'S' LIMIT 1";
$orderinfo=doSQLgetRow($sql);
//var_dump($orderinfo);
if(count($orderinfo) > 0){
	$buyerMail= new AllroverServiceMail;
	$buyerMail -> OrderNo =$orderinfo[0]["OrderNo"];
	$buyerMail -> TestMode = ture;
	$buyerMail -> buyMail();	
}
*/

?>

<?php require_once("temp/manage-footer.php"); ?>