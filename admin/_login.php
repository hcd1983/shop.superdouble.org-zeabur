<?php

require_once("functions.php");


if(isset($_GET["logout"])):
	//print_r($_SESSION);
	logOut();
	//die();
endif;	
if(isset($wordpress_setting) && $wordpress_setting != false && $wordpress_setting["active"]== 1 ){
	//var_dump($wordpress_setting);
	if(isset($_POST["action"]) && $_POST["action"]=="wp_login"){
		session_id($_POST["session_id"] );
		session_start();
		$_SESSION["user"]=$_POST["data"];
		$_SESSION["user"]["wp_login"]=1;
		exit;
	}
}



if( isset($_POST["userid"]) && isset($_POST["password"]) ):

	$userid=$_POST["userid"];
	$password=$_POST["password"];

	if( login($userid,$password) != false):
		session_start();
		
		$_SESSION["user"]=login($userid,$password);

		foreach($_SESSION["user"] as $key => $val):
			$_SESSION["user"][$key]=urldecode($val);
		endforeach;	

	else:

		$script='<script>$("form").prepend(alert_box("red","帳號或密碼錯誤!"));</script>';

	endif;	
	
endif;

if( isLogin() != false):
	gotoUrl("manage.php");	
	die();	
endif;

	


$sql="SELECT * FROM `".$dbset["table"]["users"]."`";
$result=mysqli_query($db_conn, $sql);
$row=mysqli_fetch_array($result);
				
	if( mysqli_num_rows($result) ==0):
		require_once("temp/first-user.php");
		exit();
	else:
		require_once("temp/login.php");

		exit();
	endif;

?>

