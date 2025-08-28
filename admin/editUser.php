<?php 
//ini_set('display_errors', 1);

require_once("functions.php"); 

notLogin("login.php"); 
	

	  $user=isLogin();

	  if($user["id"] != $_GET["id"] &&  $user["role"] != "super"){
	  	echo "你沒有權限編輯此頁面";
	  	exit();	
	  }


if(isset($_POST["action"]) && $_POST["action"]=="updateUser" && isset($_POST["user"])){
	//var_dump($_POST);

	$user=$_POST["user"];

	unset($user["id"]);
	unset($user["userid"]);
	
	$updater=array();

	foreach ($user as $key => $val) {
	 	$updater[]='`'.$key.'` = "'.urlencode($val).'"';
	 }

	 $updater=join(',',$updater);

	// echo $updater;
	
	 $sql="UPDATE  `".$dbset["table"]["users"]."` SET ".$updater." WHERE `id` LIKE '".$_POST["user"]["id"]."'"; 
    mysqli_query($db_conn, $sql);  
    gotoUrl("userList.php");    
	exit();
}


	$sql="SELECT * FROM `".$dbset["table"]["users"]."`  WHERE `id` LIKE '".$_GET["id"]."'";
	$user=doSQLgetRow($sql)[0];
	  	  
	$user= urldecodeArray($user);	  
?>
<?php require_once("temp/manage-header.php"); ?>
<?php


	$roleOpts=$TransCode["role"];

	if(isLogin()["role"]!="super"){
		unset($roleOpts["super"]);
	}

	$actionUrl="editUser.php";								
	$arr=array(
			array(
				
				"type"=>"hidden",
				"id"=>"hidden",
				"name"=>"user[id]",
				"required"=>true,
				"value"=>$user["id"],
			),
			array(
				
				"type"=>"hidden",
				"id"=>"action",
				"name"=>"action",
				"required"=>true,
				"value"=>"updateUser"
			),
			array(
				"label"=>"帳號",
				"type"=>"text",
				"id"=>"account",
				"name"=>"user[userid]",
				"readonly"=>true,
				"value"=>$user["userid"],
			),
			array(
				"label"=>"暱稱",
				"type"=>"text",
				"name"=>"user[name]",
				"required"=>true,
				"value"=>$user["name"],
			),
			array(
				"label"=>"E-Mail",
				"type"=>"email",
				"name"=>"user[email]",
				"required"=>true,
				"value"=>$user["email"],
			),
			array(
				"label"=>"使用者等級",
				"type"=>"options",
				"name"=>"user[role]",
				"options"=>$roleOpts,
				"value"=>$user["role"],
			),
			array(
				"type"=>"password",
				"label"=>"密碼",
				"name"=>"user[password]",
				"id"=>"pw1",
				"required"=>true,
				"readonly"=>ture,
				"value"=>$user["password"],
			),			
			array(
				"type"=>"hidden",
				"name"=>"tableName",
				"value"=>$dbset["table"]["users"],
			),
			array(
				"type"=>"hidden",
				"name"=>"arrName",
				"value"=>"user",
			),
			array(
				"type"=>"password",
				"label"=>"再確認一次密碼",
				"name"=>"pd_confirm",
				"id"=>"pw2",
				"value"=>$user["password"],
				"readonly"=>ture,
				"required"=>true
			)
			
		);

	$inputs=inputCreater($arr);

?>


		<section id="content">

			<div class="content-wrap">

				<div class="container clearfix">

					
					<div class="row clearfix">
						<div class="col-md-6 divcenter">
							

							<form id="user-form"  class="nobottommargin"  method="post" >

								
								<h3>編輯使用者</h3>
								<?php

									echo $inputs;
								?>
								
								<input type="submit" vlaue="送出" class="button  fright" >
								<input type="button" onclick="$('#pw1,#pw2').removeAttr('readonly')" value="編輯密碼" class="button red-button fright" >


							</form>
						</div>
						
						
					</div>
				</div>

			</div>

		</section><!-- #content end -->

	


<?php require_once("temp/manage-footer.php"); ?>


	<script>

	
	
	$("#user-form").submit(function(){

		

		$(this).find(".style-msg").remove();

		myform=$(this).serializeObject();
		
		pw1 = $("#pw1").val();
		pw2 = $("#pw2").val();
		if(isMatch(pw1,pw2)==false){

			msg=alert_box("red","請確認兩次密碼是否都輸入正確!");

			$("#pw2").after(msg);

			return false;

		}

		

	})

		
	</script>

