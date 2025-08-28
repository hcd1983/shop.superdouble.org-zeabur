<?php
	
	$install=1;		
	require_once("functions.php");

	$dbset=$_POST["dbset"];

	if(isset($_GET["deldbset"]) && $_GET["deldbset"]=="yes"):
		
		if(file_exists("dbset.php")):
			unlink("dbset.php");
		else:
		//	exit("dbset.php 檔案不存在");
		endif;
	endif;	

	if(file_exists("dbset.php")):
		echo "<p>請刪除舊的dbset.php</p>";
		echo "<a href='?deldbset=yes'>快速刪除</a>";
		exit();
	endif;	

		
	$db_conn = mysqli_connect($dbset["url"], $dbset["ur"], $dbset["pw"]);

	// Check connection
	if ($db_conn->connect_error) {
	    die("Connection failed: " . $db_conn->connect_error);
	} else{
		
	

		$sql="SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$dbset["db"]."'";
	

		$result = mysqli_query($db_conn, $sql);
		
		
		if( mysqli_num_rows($result) ==0):
			die("此資料庫不存在，請重新確認資料庫名稱!");
		endif;


				
	}

 	

   $ps="<?php";
   $pe="?>";

   


$output="";
foreach($dbset as $key =>$val):
	$output.="$"."dbset['".$key."']='".$val."';\r\n";
endforeach;	

$output.="$"."dbset['table']['products']='products';\r\n";
$output.="$"."dbset['table']['users']='users';\r\n";
$output.="$"."dbset['table']['orders']='orders';\r\n";
$output.="$"."dbset['table']['settings']='settings';\r\n";
$output.="$"."dbset['table']['email']='email';\r\n";

?>


<?php	

ob_start(); 
 
	echo $ps."\r\n"; 

	echo $output; 

	echo $pe; 

	$out1 = ob_get_contents();

ob_end_clean();

?>

<?php	        

	        $fp = fopen("dbset.php","w");
	        if(!$fp)
	        {
	        echo "dbset寫入失敗。";
	        exit();
	        }
	        else
	        {
	        fwrite($fp,$out1);
	        fclose($fp); 
	        //echo "<a href='".$page["file"]."' target='_blank'>".$page["file"]."</a> ";       
	        echo "dbset寫入成功<br>";
	   		}
	//}   		
?>

<?php

include("dbset.php");
$db_conn = mysqli_connect($dbset["url"], $dbset["ur"], $dbset["pw"],$dbset["db"]);
insert_sql_file("install/email.sql");
insert_sql_file("install/orders.sql");
insert_sql_file("install/products.sql");
insert_sql_file("install/settings.sql");
insert_sql_file("install/users.sql");

?>
<?php //require_once("install/create_paynow_table.php"); ?>
<?php //require_once("install/create_product_table.php"); ?>
<?php //require_once("install/create_user_table.php"); ?>
<?php //require_once("install/create_setting_table.php"); ?>
<?php //require_once("install/create_email_table.php"); ?>




<?php
gotoUrl("login.php");
?>