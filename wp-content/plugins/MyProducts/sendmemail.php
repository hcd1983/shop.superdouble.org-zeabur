<?php
$oldversion=get_site_option( 'SendMeMail_version' );
$SendMeMail=array(
	"version" =>"1.8",
	"table_name"=>"sandmemail",

);


/*
function UrlWithGetVal($url="",$values=array()){
    if($url==""){
       $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
   }else{
       $actual_link=$url; 
   }

   $pure_link = explode( '?', $actual_link );
   $pure_link = $pure_link[0];

   $queryString="";
   if(is_array($values) && count($values) > 0){
     array_walk($values,function(&$value,$key){
        $value="$key=$value";
     });
     $queryString="?".join("&",$values);
   }

   $fixedlink=$pure_link.$queryString;
   
   return $fixedlink;
}
*/



function sendmemail_fn(){
	if(isset($_GET["source"])){
		$source = $_GET["source"];
	}else{
		$source = "";
	}
	switch ($source) {
		
		default:
			include_once plugin_dir_path( __FILE__ ).'sendmemail/allmails.php';
			break;
	}
}

function SendMeMail_install() {
	
	global $wpdb;
	global $SendMeMail;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	
	$charset_collate = $wpdb->get_charset_collate();
	
	$table_name =  $SendMeMail["table_name"];
	$version    =  $SendMeMail["version"];
	$query      =  "CREATE TABLE `$table_name` (
					`mail_id` int(11) NOT NULL,
		  			`product_id` varchar(255) NOT NULL,
		  			`email` varchar(255) NOT NULL,
		  			`mail_date` date NOT NULL	
					) $charset_collate;

					ALTER TABLE `$table_name`
		  			ADD PRIMARY KEY (`mail_id`);

					ALTER TABLE `$table_name`
		  			MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT;";
	
	$result = dbDelta( $query );
	
	//add_option( 'SendMeMail_version', $version );
	update_option( "SendMeMail_version", $SendMeMail["version"] );	
	add_action('admin_head',"alertSuccess");
	//echo "<h3>Message Mail installed!</h3>";
	
}

function alertSuccess(){
	global $SendMeMail;
	global $oldversion;
	$newversion= $SendMeMail["version"];
?>
<script type="text/javascript">
	alert("資料庫安裝完成!舊版本: <?php echo $oldversion;?>,新版本: <?php echo $newversion;?>");
</script>
<?php
}

$SendMeMail_version = get_option( "SendMeMail_version" );

if($SendMeMail_version != $SendMeMail["version"]){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	
	//update_option( "SendMeMail_version", $SendMeMail["version"] );	
}

add_action( 'plugins_loaded', 'SendMeMail_update_db_check' );
function SendMeMail_update_db_check() {
    global $SendMeMail;    
    $version = $SendMeMail["version"];
    if ( get_site_option( 'SendMeMail_version' ) != $version ) {
        SendMeMail_install();        
    }

}

//表格匯入
if(isset($_GET["getmail"]) && isset($_GET["product_id"])){
	//add_action("init","sendmemailForm");
	add_filter( 'get_post_metadata', 'EnfoldSendmemailValue', 2, 4 );
	add_filter( 'the_content', "sendmemailForm",99,1 );
}
function sendmemailForm($content){
	
	$product_id = $_GET["product_id"];
	$product_title = get_the_title($product_id);
	ob_start();
?>	
	<style type="text/css">
		html{
			margin: 0 !important;
		}
		body{
			display: flex;
			align-items: center;
		}
		#sendmemailFormContainer{
			width: 100%;
			min-height: 500px;
			height: 100%;
			background-color: #FFF;
			display: flex;
			align-items: center;
			padding:0 10px;
		}

		#sendmemailFormContainer > div{
			margin: auto;
			text-align: center;
		}

		#sendmemailFormContainer h3 span{
			color:red;
		}
	</style>
	<div id='sendmemailFormContainer'>
		<div>
			<h3><?php 
			$changeto=[
				[
					"search"=>"#1",
					"replace"=>" <span>$product_title</span> ",
				],
			];
			echo MyCartWords("sendmemail",$changeto);?></h3>
			<form id="sendmemailForm" onsubmit="return false;">
			<div class="col_half">
				<input type="email" name="email" value="" placeholder="<?php echo MyCartWords("sendmemail_placeholder");?>" class="sm-form-control" required="">
			</div>
			<input type="hidden" name="product_id" value="<?php echo $product_id;?>" >
			<input type="hidden" name="SubmitSendMemail" value="1" >
			
			<div class="col_half">
				<input type="submit" class="button button-black"  value="<?php echo MyCartWords("submit");?>">
			</div>
			<div class="clear"></div>
			</form>	
		</div>
	</div>	
<?php
	$content =ob_get_contents();
	ob_clean();
	return $content;
	
}

function EnfoldSendmemailValue( $value, $post_id, $meta_key, $single ) {
    
    show_admin_bar( false );
 
    switch ( $meta_key ) {
        case "header_title_bar":
            
            $value = array("hidden_title_bar");
                        
            break;
        case "header_transparency":
            
            $value = array("header_transparent header_hidden ");
                        
            break;
        case "footer":
            
            $value = array("nofooterarea");
                        
            break;                   
    }
 
    return $value;
}


//csv 匯出

function sendmemailExport(){
	global $wpdb;
	global $SendMeMail;
	$filename = "匯出.csv";

	$table_name=$SendMeMail["table_name"];

	$sql = "SELECT * FROM `{$table_name}`;";
	$result = $wpdb->get_results($sql,"ARRAY_A");
	if(count($result)==0){
		return;
	}
	$rows = [];
	$titles = ["商品","email","日期"];
	$lines[] = $titles;
	foreach($result as $key => $row){
		$product_id = $row["product_id"];
		$product_title = get_the_title($product_id);
		$email = $row["email"];
		$mail_date = $row["mail_date"];	
		$lines[] = [$product_title,$email,$mail_date];		
	}

	ob_start();
	header("Content-type:text/csv"); 
	header("Content-Disposition:attachment;filename=".$filename); 
	header("Content-Type: application/vnd.ms-excel;");
	header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
	header('Expires:0'); 
	header('Pragma:public');

	$f = fopen('php://output', 'w');
	fwrite($f,chr(0xEF).chr(0xBB).chr(0xBF));
	foreach ($lines as $key => $line) {
		
		foreach ($line as $_key => $value) {
			//$value=mb_convert_encoding($value, 'big5', 'UTF-8');
			//$value=trim(preg_replace('/\s+/', ' ', $value));
			$value=html_entity_decode($value);
			//$value=str_replace("&#8211;", "-", $value);
			$line[$_key]=$value;
		}	
		
		fputcsv($f, $line, ",");
	}

	$content=ob_get_contents();
	ob_clean();
	echo $content;
	exit;
	
}

if(isset($_GET["sendmemailExport"])){
	add_action("init","sendmemailExport");	
}



//主選單
add_action('admin_menu', 'MenuForEmail');
function MenuForEmail(){
	add_menu_page( $page_title="Email蒐集", $menu_title="Email蒐集", $capability="administrator", $menu_slug="sendmemail", $function = 'sendmemail_fn', $icon_url = '', $position = 24 );
}

//js 匯入
add_action('wp_enqueue_scripts', 'sendmemailJs',99);
function sendmemailJs(){  
 
  wp_enqueue_script( $handle="sendmemail", $src =plugins_url("js/sendmemail.js",__FILE__), $deps = array("jquery"), $ver = time(), $in_footer = true );
  wp_enqueue_style( $handle="sendmemail", $src =plugins_url("css/sendmemail.css",__FILE__) ,$deps = array(),$ver = time()); 

}

//修訂文字
add_filter("addtocart_content","GetEmailBtn",99,3);
function GetEmailBtn($content,$error,$nostore){
	if($nostore==true){
		$content = MyCartWords("outofsotck_btn");
	}
	return $content;
}



//啟動外掛時執行
register_activation_hook( __FILE__, 'SendMeMail_install' );



//插入資料
function insertSendmailData(){
	global $wpdb;
	global $SendMeMail;

	$product_id=$_POST["product_id"];
	$email=$_POST["email"];
	$table_name = $SendMeMail["table_name"];
	/*
	echo $product_id;
	echo "/";
	echo $table_name;
	echo "/";
	echo $email;
	*/	
	$data = array(
			"product_id"=> $product_id,
			"email" => $email,
			"mail_date"=>current_time('mysql', 1),
			);
	$result = $wpdb->insert( $table_name, $data );

	if($result != false){
		echo 1;
	}else{
		echo 0;
	}

	exit;
}


if(isset($_POST["SubmitSendMemail"])){

	add_action("init","insertSendmailData");
	
}