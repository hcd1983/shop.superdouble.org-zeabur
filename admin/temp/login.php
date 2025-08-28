<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>

	<?php require_once("inputcss.php"); ?>

</head>

<?php
	 if(isset($wordpress_setting) && $wordpress_setting != false && $wordpress_setting["active"]==1 && !isset($_GET["logout"]) && isset($_GET["wp_login"])):
?> 	
<script>	
	jQuery.ajax({
    url: "<?php echo $wordpress_setting["url"];?>",
    data: {
        login_key: '<?php echo $wordpress_setting["key"];?>'
    },
    type: 'POST'
}).done(function( data ) {
    data=JSON.parse(data);		
    if(data.status=="S"){
    	wp_login(data.datas);
    }else{
    	$("body").append("Login Fail");
    }    
});


function wp_login(datas){
	jQuery.ajax({
        url: "login.php",
        data: {
        	action:'wp_login',
            data: datas,
            session_id:'<?php echo session_id ();?>',
        },
        type: 'POST'
    }).done(function( data ) {
    //	console.log(data);
	    location.reload();  
	});
}
</script>	
<?php
exit;
endif;
?>

<body class="stretched">

	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">

		<!-- Content
		============================================= -->
		<section id="content">

			<div class="content-wrap nopadding">

				<div class="section nopadding nomargin" style="width: 100%; height: 100%; position: absolute; left: 0; top: 0; background: #444;"></div>

				<div class="section nobg full-screen nopadding nomargin">
					<div class="container vertical-middle divcenter clearfix">
						
						<div class="panel panel-default divcenter noradius noborder" style="max-width: 400px;">
							<div class="panel-body" style="padding: 40px;">
								<form id="login-form" name="login-form" class="nobottommargin" action="login.php" method="post">
									<h3>請輸入帳號密碼</h3>

									<div class="col_full">
										<label for="userid">帳號或E-MAIL</label>
										<input type="text" id="login-form-username" name="userid" value="" class="form-control not-dark" />
									</div>

									<div class="col_full">
										<label for="password">密碼</label>
										<input type="password" id="password" name="password" value="" class="form-control not-dark" />
									</div>

									<div class="col_full nobottommargin">
										<button class="button button-3d button-black nomargin" id="login-form-submit" name="login-form-submit" value="login">登入</button>
									</div>
								</form>


								
							</div>
						</div>


					</div>
				</div>

			</div>

		</section><!-- #content end -->

	</div><!-- #wrapper end -->

	<?php require_once("inputjs.php"); ?>
	<?php 
		$script=isset($script)?$script:"";
		echo $script;
	 ?>

</body>
</html>