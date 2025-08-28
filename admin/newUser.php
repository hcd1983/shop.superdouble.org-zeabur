<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); 
	  $role=array();
	  $role[]="admin";
	  notRole($role);	
?>
<?php require_once("temp/manage-header.php"); ?>
<?php


	$roleOpts=$TransCode["role"];

	if(isLogin()["role"]!="super"){
		unset($roleOpts["super"]);
	}

	$actionUrl="sqlfunction/insert.php";								
	$arr=array(
			array(
				"label"=>"帳號",
				"type"=>"text",
				"id"=>"account",
				"name"=>"user[userid]",
				"required"=>true
			),
			array(
				"label"=>"暱稱",
				"type"=>"text",
				"name"=>"user[name]",
				"required"=>true
			),
			array(
				"label"=>"E-Mail",
				"type"=>"email",
				"name"=>"user[email]",
				"required"=>true
			),
			array(
				"label"=>"使用者等級",
				"type"=>"options",
				"name"=>"user[role]",
				"options"=>$roleOpts,
				"value"=>"admin",
			),
			array(
				"type"=>"password",
				"label"=>"密碼",
				"name"=>"user[password]",
				"id"=>"pw1",
				"required"=>true
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

								
								<h3>新增使用者</h3>
								<?php

									echo $inputs;
								?>
								
								<input type="submit" vlaue="送出" class="button  fright" >


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

		

		

		CheckStatus=function (i){	
			switch(i) {
			    case "0":

				    Sfunction=function (i){	
						switch(i) {
						    case "S":
						        window.location = "login.php";
						        break;
						    case "F":
						        $("form").prepend(alert_box("red","寫入失敗，可能是資料庫連線有問題!"));
						        break;
						    default:
						        $("form").prepend(alert_box("red","不明原因造成錯誤!"));
						}        

					}

			    	do_sql(myform,'sqlfunction/insert.php',Sfunction);
			        break;
			   
			    default:
			    	msg=alert_box("red","帳號重複!");
					$("#account").after(msg);
			  		return false;
			}        

		}
		
		checkval={
			"val":$("#account").val(),
			"column":"userid",
			"tableName":"<?php echo $dbset["table"]["users"];?>"
		};

		do_sql(checkval,'sqlfunction/check_value.php',CheckStatus);



		


		

		

		return false;
	})

		
	</script>

