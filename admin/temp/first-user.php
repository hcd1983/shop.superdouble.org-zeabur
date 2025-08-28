<?php

	$actionUrl="sqlfunction/insert.php";								
	$arr=array(
			array(
				"label"=>"帳號",
				"type"=>"text",
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
				"type"=>"password",
				"label"=>"密碼",
				"name"=>"user[password]",
				"id"=>"pw1",
				"required"=>true
			),
			array(
				"type"=>"hidden",
				"name"=>"user[role]",
				"value"=>"super",
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
			),
			array(
				"label"=>"角色",
				"name"=>"role",
				"value"=>"超級管理員",
				"readonly"=>true,
				"required"=>true
			)
			
		);

	$inputs=inputCreater($arr);

?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="SemiColonWeb" />

	<!-- Stylesheets
	============================================= -->
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="css/dark.css" type="text/css" />
	<link rel="stylesheet" href="css/font-icons.css" type="text/css" />
	<link rel="stylesheet" href="css/animate.css" type="text/css" />
	<link rel="stylesheet" href="css/magnific-popup.css" type="text/css" />

	<link rel="stylesheet" href="css/responsive.css" type="text/css" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="images/favico.png" type="../image/x-icon">
	<link rel="apple-touch-icon-precomposed" href="../images/favico-2.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../images/favico-3.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../images/favico-4.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../images/favico-3.png">
	<script type="text/javascript" src="js/jquery.js"></script>
	<!--[if lt IE 9]>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->

	<!-- Document Title
	============================================= -->
	<title>Install | Hcd shopping cart</title>

</head>

<body class="stretched">

	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">


		<!-- Content
		============================================= -->
		<section id="content">

			<div class="content-wrap">

				<div class="container clearfix">

					
					<div class="row clearfix">
						<div class="col-md-6 divcenter">
							

							<form id="user-form"  class="nobottommargin"  method="post" >

								
								<h3>創始級使用者</h3>
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

	

	</div><!-- #wrapper end -->

	<!-- Go To Top
	============================================= -->
	<div id="gotoTop" class="icon-angle-up"></div>

	<?php require_once("inputjs.php"); ?>

	
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

		return false;
	})

		
	</script>

</body>
</html>