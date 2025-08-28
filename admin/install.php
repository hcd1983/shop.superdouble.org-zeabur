<?php
	//ini_set("display_errors", 1);
	$install=1;
	
	require_once("functions.php");


	if(isset($dbset) && $db_conn!=false):
	
		gotoUrl("login.php");
		
	endif;


?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>

	<?php require_once("temp/inputcss.php"); ?>

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
							

							<form id="billing-form" name="billing-form" class="nobottommargin" action="dbset_creater.php" method="post">

								
								<h3>INSTALL</h3>
								<?php
								
									$arr=array(
											array(
												"label"=>"資料庫位置",
												"type"=>"text",
												"name"=>"dbset[url]",
												"placeholder"=>"資料庫位置:ex localhost",
											),
											array(
												"label"=>"資料庫名稱",
												"type"=>"text",
												"name"=>"dbset[db]",
											),
											array(
												"label"=>"使用者名稱",
												"type"=>"text",
												"name"=>"dbset[ur]",
											),
											array(
												"label"=>"使用者密碼",
												"name"=>"dbset[pw]",
												"placeholder"=>"密碼",
											)
										);

									echo inputCreater($arr);
								?>
								
								<input type="submit" value="送出" class="button  fright">

							</form>
						</div>
						
						
					</div>
				</div>

			</div>

		</section><!-- #content end -->

	

	</div><!-- #wrapper end -->

	<?php require_once("temp/inputjs.php"); ?>

</body>
</html>