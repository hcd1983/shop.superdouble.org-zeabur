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
				"setting"=>"basic",
				"val"=>$val
			);

		insertInto($arr,["setting"],$dbset["table"]["settings"]);	

	
	endif;
	$setting=getSettingVal("basic");
?>
<?php require_once("temp/manage-header.php"); ?>



<?php
								
	$arr=array(
			"title"=>array(
					"label"=>"網站標題",
					"type"=>"text",
				)
			
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

				<h2>基本設定</h2>

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