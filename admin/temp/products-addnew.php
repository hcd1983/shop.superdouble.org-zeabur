<?php
								
	$arr=array(
			"title"=>array(
					"label"=>"產品名稱(中文)",
					"type"=>"text",					
				),
			"entitle"=>array(
					"label"=>"產品名稱(英文)",
					"type"=>"text",					
				),
			"img_url"=>array(
					"label"=>"產品圖片1",
					"type"=>"text",					
				),
			"img_url2"=>array(
					"label"=>"產品圖片2",
					"type"=>"text",					
				),
			"price"=>array(
					"label"=>"產品售價",
					"type"=>"number",
					"min"=>"0",
					"value"=>"0",					
				),
			"saleprice"=>array(
					"label"=>"產品特價",
					"type"=>"number",
					"min"=>"0",
					"value"=>"0",					
				),
			"store"=>array(
					"label"=>"庫存",
					"type"=>"number",
					"min"=>"0",
					"value"=>"0",					
				),
			"weight"=>array(
					"label"=>"重量",
					"type"=>"number",
					"min"=>"0",
					"max"=>"500",
					"value"=>"0",					
				),
			"sold"=>array(
					"label"=>"累積銷售",
					"type"=>"number",
					"min"=>"0",
					"value"=>"0",					
				),
			"coloroptions"=>array(
					"label"=>"顏色選項",
					"id"=>"coloroptions",
					"type"=>"coloroptions",	
					"colors"=>array(
						array("number"=>"#FFFFFF","name"=>"test","img"=>"aa"),
						array("number"=>"#000000","name"=>"test","img"=>"aa"),
						)			
				),
			"options"=>array(
					"label"=>"其他選項",
					"id"=>"options",
					"type"=>"textarea",
				)
			
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

				<h2>新增產品</h2>

				<form id="form" name="form" class="nobottommargin" action="#" method="post">

					<?php echo	$table; ?>

					
					<input type="submit" value="儲存" class="button  fright">
					<input type="button" value="取消" onclick=(reset()) class="button button-red fright">
				</form>	

				
			</div>	

		</div>

	</div>

</section><!-- #content end -->



