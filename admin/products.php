<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); ?>
<?php
if(isset($_GET["action"])){
	$action=$_GET["action"];

	function NewProduct(){
		$arr=array(
			"product_id"=>array(
				"label"=>"產品編號(不可重複)",
				"type"=>"text",
			),			
			"title"=>array(
				"label"=>"名稱",
				"type"=>"text",
			),
			"img_url"=>array(
				"label"=>"產品圖片一",
				"type"=>"text",
			),
			"img_url2"=>array(
				"label"=>"產品圖片二",
				"type"=>"text",
				"required"=>true,
			),
		);

		foreach($arr as $key => $val):
			$arr[$key]["name"]="product[".$key."]";
		endforeach;	

		$table=inputCreater($arr);

		$output["head"]="新增產品";
		$output["body"]=$table;
		echo json_encode($output);
	}
	$action();
	exit();
}
?>
<?php require_once("temp/manage-header.php"); ?>
<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-10 divcenter">
				<div class="bottommargin-sm">
					<a id="fullscreenGo" href="javascript:void(0)" onclick="NewProduct()" class="btn btn-default">新增產品</a>
				</div>
				<div class="the-table">
					<?php

						$sql="SELECT * FORM `porducts` LIMIT 500";
						$row=doSQLgetRow($sql);
						$product_table=new DataTable;
						$product_table->datas=$row;
						$product_table->Render();
					?>

				</div>				
			</div>	
		</div>

	</div>
	<!-- Large modal -->
	<button class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Large modal</button>

	<div id="the_data_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-body">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">Modal Heading</h4>
					</div>
					<div class="modal-body">
						<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
						<p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
						<p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
						<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
						<p class="nobottommargin">Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>
<?php require_once("temp/manage-footer.php"); ?>
<script>

function UpdateModal(head,body){
	$("#the_data_modal .modal-header h4").html(head);
	$("#the_data_modal .modal-content .modal-body").html(body);
	$("#the_data_modal").modal("show");
}

const NewProduct =  async() => {
  const a = await fetch("?action=NewProduct",{ credentials: 'include'});
  const b = await a.json();
  const c = await UpdateModal(b.head,b.body)
 
}	

const getData =  async(year,month) => {
  const a = await fetch("manage.php",{
	    method: "POST",
	    credentials: 'include',
	    headers: {
	        'Accept': 'application/json',
	        'Content-Type': 'application/json'
	      },
	   body: JSON.stringify({
	        year: year,
	        month: month
	    })
	});
  const b = await a.json();
  const c = await console.log(b);
  const d = await cal.setData(b);
  //const c = await cal.setData( {'12-22-2017': "交易成功: 3<br>金額: $7,840"} );
 
  //const b = await fetch(“xxx.xxx.xx.x”)
}


</script>	