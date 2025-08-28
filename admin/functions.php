<?php
header("Content-Type:text/html; charset=utf-8");
if (session_status() == PHP_SESSION_NONE) {
	   session_start();
}

date_default_timezone_set('Asia/Taipei');
$dbsetUrl=dirname(__FILE__)."/dbset.php";


if(!isset($install)){
	$install=0;
}

if(file_exists($dbsetUrl)):
//	echo $dbsetUrl;
	require_once($dbsetUrl);
//	print_r($dbset);
	$db_conn = mysqli_connect($dbset["url"], $dbset["ur"], $dbset["pw"],$dbset["db"]);
	mysqli_query($db_conn, "SET NAMES utf8");

    $sql = 'SET @@session.time_zone = ‘+08:00’';
    mysqli_query($db_conn, $sql);
else:

	if($install!==1):

		gotoUrl("install.php");
	endif;
//	exit();
endif;

//basic settings===============================================================
function getSettingVal($settingname){

	global $db_conn;
	if (!$db_conn) {
		die("資料庫連線失敗!");
	}else{


		$sql = "SELECT `val` FROM `settings` WHERE `setting` LIKE '".$settingname."'";
		$result=mysqli_query($db_conn, $sql);
		$num=mysqli_num_rows($result);
		if($num > 0):
			$row = mysqli_fetch_assoc($result);

			$output=unserialize($row["val"]);
			$output=urldecodeArray($output);

			return $output;
		else:
			return false;
		endif;


	}
}

if($install!==1):
$basic_setting=getSettingVal("basic");
$wordpress_setting=getSettingVal("wordpress");
$stripe_setting=getSettingVal("stripe");
if($stripe_setting != false){
	if($stripe_setting["mode"]=="test"){
		$stripe_setting["token"]=$stripe_setting["token_test"];
		$stripe_setting["secret_token"]=$stripe_setting["secret_token_test"];
	}
	if($stripe_setting["mode"]==""){
		$stripe_setting["token"]="";
		$stripe_setting["secret_token"]="";
	}
}
define("_WebTitle", $basic_setting["title"]);
endif;

//==================================================================================

include_once('soap/nusoap.php');

ob_start();
require_once(dirname(__FILE__)."/TransCode.php");
require_once(dirname(__FILE__)."/class/orders.php");
require_once(dirname(__FILE__)."/class/email-tpl.php");
require_once(dirname(__FILE__)."/class/tablemaker.php");
require_once(dirname(__FILE__)."/class/export.php");
require_once(dirname(__FILE__)."/class/uploader.php");
require_once(dirname(__FILE__)."/class/OrderListRow.php");
require_once(dirname(__FILE__)."/class/condition.php");
require_once(dirname(__FILE__)."/class/InputCreater.php");
require_once(dirname(__FILE__)."/class/doublecheck.php");
require_once(dirname(__FILE__)."/class/DataTable.php");
require_once(dirname(__FILE__)."/class/slack.php");
require_once(dirname(__FILE__)."/email-tpl/main.php");
ob_end_clean();
// fix some email issues =================
function get_admin_emails () {
    $buymail_setting = getSettingVal("buymail");
    if($buymail_setting == false){
        return [];
    }
    $sendto = $buymail_setting["mailto"];
    $_sendto = explode("\r\n", $sendto);
    return $_sendto;
}

function send_buy_email_coupon_owner_email ($OrderNo) {
    global $db_conn,$dbset,$TransCode,$wordpress_setting;
    $useLaravel = useLaravelMail();

    if (!$useLaravel) {
        echo "This mail will not be sent.";
        return;
    }

    $sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";
    $res = doSQLgetRow($sql);
    if (count($res) === 0) return ;
    $orderinfo =$res[0];
    $buyer = unserialize($orderinfo["buyer"]);
    $buyer = urldecodeArray($buyer);
    $buyerMail = $buyer["bemail"];
    $coupon = $orderinfo["coupon"];
    if (!$coupon) {
        echo "This order may not use coupon.";
        return;
    }

    $couponOwner = getCouponOwners();

    foreach ($couponOwner as $key => $owner) {
        $_coupon = $owner["coupon"];
        $_mail = $owner["email"];
        if ($coupon === $_coupon) {
            $res = laravelSystemMail($OrderNo, $buyerMail, $_mail);
        }
        echo $res;
    }
}

function getCouponOwners() {
    return [
        [
            "coupon" => "喪屍老爸專屬優惠測試",
            "email" => "hcd@mojopot.com"
        ],
		[
            "coupon" => "喪屍老爸專屬優惠",
            "email" => " joan0812joan0912@gmail.com"
        ],
        [
            "coupon" => "喪屍老爸",
            "email" => " joan0812joan0912@gmail.com"
        ],
    ];
}

function send_buy_email_admin_email ($OrderNo, $mailto) {
    global $db_conn,$dbset,$TransCode,$wordpress_setting;


    if(isset($wordpress_setting["search_page"])){
        $search_page = $wordpress_setting["search_page"];
    }else{
        $search_page = "";
    }

    $sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";
    $res = doSQLgetRow($sql);
    if (count($res) === 0) return ;
    $orderinfo =$res[0];

    $reg_date = date_create($orderinfo["reg_date"]);
    $reg_date = date_format($reg_date,'Y-m-d');
    $BuysafeNo = $orderinfo["BuysafeNo"]==""?"未產生":$orderinfo["BuysafeNo"];
    $buyer = unserialize($orderinfo["buyer"]);
    $buyer = urldecodeArray($buyer);
    $TotalPrice = "$".number_format($orderinfo["TotalPrice"]);
    $shippingFee = "$".number_format($orderinfo["shippingFee"]);
    $TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
    $CargoList = unserialize($orderinfo["CargoList"]);
    $orderlist = "\r\n";

    $receiver = unserialize($orderinfo["receiver"]);

    if(empty($receiver)){
        $receiver_data = $buyer;
    }else{
        $receiver = urldecodeArray($receiver);
        $receiver_data["bname"] = $receiver["rname"];
        $receiver_data["bphone"] = $receiver["rphone"];
        $receiver_data["bemail"] = $receiver["remail"];
        $receiver_data["zip"] = $receiver["rzip"];
        $receiver_data["address"] = $receiver["raddress"];
    }

    $receipt_info = "";
    if($buyer["receipt"]){
        $receipt_info .= "\r\n"."統一編號: ".$buyer["receipt"];
    }

    if($buyer["company"]){
        $receipt_info .= "\r\n"."公司名稱: ".$buyer["company"];
    }

    $_receiver_data = "";
    $_receiver_data .= "&nbsp;".$receiver_data["bname"]."\r\n";
    $_receiver_data .= "&nbsp;".$receiver_data["bphone"]."\r\n";
    $_receiver_data .= "&nbsp;".$receiver_data["bemail"]."\r\n";

    if($receiver_data["zip"]){
        $_receiver_data .= "&nbsp;".$receiver_data["zip"].",";
    }

    $receiver_data["address"] = str_replace("\r\n", " ", $receiver_data["address"]);
    $_receiver_data .= "&nbsp;".$receiver_data["address"]."\r\n";

    if($search_page){
        $_search_url = $search_page."?email=".urlencode($buyer["bemail"])."&OrderNo=".$OrderNo;
        $search_url = "詳細資料: <a href='$_search_url' target='_blank'>".$_search_url."</a>";
    }else{
        $search_url = "";
    }

    foreach ($CargoList as $key => $val) {
        $title= urldecode($val["title"]);
        $orderlist.="&nbsp;".$title." x ".number_format($val["amount"])."\r\n";
    }

    if($orderinfo["Note1"]){
        $memo = "\r\n"."交易備註: "."\r\n"."&nbsp;".urldecode($orderinfo["Note1"])."\r\n";
    }

    $subject = "["._WebTitle." 網站]交易通知 單號- $OrderNo" ;

    $receipt = "";
    $msg=
        "下單日期: ".$reg_date."
	訂單編號: ".$OrderNo."
	金流單號: ".$BuysafeNo."
	買者姓名: ".$buyer["bname"]."
	電子郵件: ".$buyer["bemail"]."
	買者電話: ".$buyer["bphone"].$receipt_info."
	交易金額: ".$TotalPrice."
	交易結果: ".$TranStatusT."
	購賣物品: ".$orderlist.$memo."
	出貨資訊: "."\r\n".$_receiver_data."
	".$search_url;

    $_msg = nl2br($msg);

    // echo $_msg;
    $sql = "SELECT * FROM `admin_mail_log` WHERE `OrderNo` LIKE '{$OrderNo}' AND `sendTo` LIKE '{$mailto}'";
    $row = doSQLgetRow($sql);

    $receiver = "";
    $useLaravel = useLaravelMail();

//    完全沒紀錄 才會寄信，不然就等檢查吧
    if ( count($row) === 0) {

        if ($useLaravel) {
            $res = laravelSystemMail($OrderNo, $buyer["bemail"], $mailto);
        }else {
            $res = sendmail($mailto,$receiver,$subject,$msg);
        }
        $_date = date('Y/m/d H:i:s');
        $_sql = "INSERT INTO `admin_mail_log` (`OrderNo`, `status_one`, `status_two`, `status_three`, `sendTo`, `Subject`,`Content`,`Receiver`,`create_at`) VALUES ('{$OrderNo}', '{$res}', '', '', '{$mailto}', '{$subject}','{$myMsg}','{$receiver}','{$_date}')";
        mysqli_query($db_conn, $_sql);
//        echo "完全沒紀錄<br>";
//        echo "結果：$res <br>";
    } else {
//        echo "已經寄出過<br>";
    }
}
//===========================laravel email
function useLaravelMail() {
    $settings = getSettingVal("phpmailer");
    $useLaravel = isset($settings["useLaravelApi"]) ? $settings["useLaravelApi"] : false;
    return $useLaravel;
}

function laravelCustomerMail ($OrderNo, $email) {
    $settings = getSettingVal("phpmailer");
    $url = $settings["laravelApiCustomerMail"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

//設定要傳的 變數A=值A & 變數B=值B (中間要用&符號串接)
    $PostData = [
        "OrderNo" => $OrderNo,
        "email" => $email
    ];
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

function laravelSystemMail ($OrderNo, $email,$mailTo) {
    $buymail_setting = getSettingVal("buymail");
    if ($buymail_setting == false) return ;
    if(!isset($buymail_setting["mailto"]) || !$buymail_setting["mailto"]) return ;
    $_users = $buymail_setting["mailto"];
    $users = explode("\r\n", $_users);

    $settings = getSettingVal("phpmailer");
    $url = $settings["laravelApiSystemMail"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

//設定要傳的 變數A=值A & 變數B=值B (中間要用&符號串接)
    $PostData = [
        "OrderNo" => $OrderNo,
        "email" => $email,
        "mailTo" => $mailTo
    ];

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $PostData);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($PostData));
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}


//========================================================xf=====================
function insert_sql_file($file_path){
	global $db_conn;

	$file_content=file_get_contents($file_path);
	// Check connection
	if (!$db_conn) {
	    die("Connection failed: " . mysqli_connect_error());
	}

	//$sql_quo_arr = explode(PHP_EOL, $sql_quo);
	$file_content_arr =preg_split('/\r\n|\r|\n/', $file_content);
	$output ="";
	foreach($file_content_arr as $row){
		$start_character = substr(trim($row), 0, 2);
		if($start_character == '--' || $start_character == '/*' || $start_character == '//' || $row == ''){
			continue;
		}
		echo $row."<br>";
		//echo "START: ".$start_character."<br>";
		$output = $output . $row;
		$end_character = substr(trim($row), -1, 1);
		if($end_character == ';'){
			if(!mysqli_query($db_conn, $output)){
				echo("Error description: " . mysqli_error($db_conn));
				exit;
			}

			$output="";
		}


	}

	echo "<br>".$file_path." installed"."<hr>";

}

//=============================================================================
function QuickShippingURL($OrderNo){

	$basic_setting = getSettingVal("basic");
	if(!isset($basic_setting["admin_url"])){
		return false;
	}else{
		$admin_url = $basic_setting["admin_url"];
		global $dbset,$TransCode,$wordpress_setting;
		$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."'  LIMIT 1";
		$target = doSQLgetRow($sql);
		if(empty($target)){
			return false;
		}else{

			$data = [
				"DoQuickShipping" => $OrderNo,
				"PassCode" => $target[0]["PassCode"]
			];

			$_query = http_build_query($data);
			return $admin_url."?".$_query;
		}
	}
}


function slackMsg($msg){

	$setting = getSettingVal("slack");
	if($setting==false){
		return ;
	}
	$slackMsg = new slack;
	$slackMsg->sendMsg($setting["token"],$setting["channel"],$msg,$setting["username"],$setting["icon_url"],$as_user=false);
}

function sendbuymail($OrderNo){
    global $db_conn,$dbset,$TransCode,$wordpress_setting;

    $buymail_setting = getSettingVal("buymail");
    if($buymail_setting == false){
        return;
    }

    send_buy_email_coupon_owner_email($OrderNo);

    $_sendto = get_admin_emails ();

    foreach ($_sendto as $key => $_mailto) {
        $_mailto = trim($_mailto);
        send_buy_email_admin_email($OrderNo, $_mailto);
    }
}

function OrderSuccessMsgSlack($OrderNo){

	global $dbset,$TransCode,$wordpress_setting;
	$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' AND `slackcheck` NOT LIKE 'S' LIMIT 1";

	if( isset($_GET["action"]) && $_GET["action"] =="OrderNoTest"){
		$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";
		$_pre = "TEST================="."\r\n";
	}else{
		$_pre = "";
	}

	if( count(doSQLgetRow($sql)) == 0 ){
		echo "沒這個單喔!";
		return ;
	}

	if(isset($wordpress_setting["search_page"])){
		$search_page = $wordpress_setting["search_page"];
	}else{
		$search_page = "";
	}

	$QuickShippingURL = QuickShippingURL($OrderNo);

	if($QuickShippingURL){
		$QuickShippingURLBtn = "\r\n"."設為出貨: ".$QuickShippingURL;
	}else{
		$QuickShippingURLBtn = "";
	}

	$orderinfo = doSQLgetRow($sql)[0];
	$reg_date = date_create($orderinfo["reg_date"]);
	$reg_date = date_format($reg_date,'Y-m-d');
	$BuysafeNo = $orderinfo["BuysafeNo"]==""?"未產生":$orderinfo["BuysafeNo"];
	$buyer = unserialize($orderinfo["buyer"]);
	$buyer = urldecodeArray($buyer);
	$TotalPrice = "$".number_format($orderinfo["TotalPrice"]);
	$TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
	$CargoList = unserialize($orderinfo["CargoList"]);
	$orderlist = "\r\n";

	if($search_page){
		$search_url = "詳細資料: ".$search_page."?email=".urlencode($buyer["bemail"])."&OrderNo=".$OrderNo;
	}else{
		$search_url = "";
	}

	foreach ($CargoList as $key => $val) {
		$title= urldecode($val["title"]);
		$orderlist.="        ".$title." x ".number_format($val["amount"])."\r\n";
	}
	$memo="\r\n".urldecode($orderinfo["Note1"]);
	$msg=$_pre.
":dollar: :dollar: :dollar:
潮爽DER! 有人下單了~~~~~~~~~~~~~~~~~ 
下單日期: ".$reg_date."
訂單編號: ".$OrderNo."
金流單號: ".$BuysafeNo."
買者姓名: ".$buyer["bname"]."
電子郵件: ".$buyer["bemail"]."
購物金額: ".$TotalPrice."
購賣物品: ".$orderlist."
交易備註: ".$memo."
交易結果: ".$TranStatusT."
".$search_url.$QuickShippingURLBtn;

	slackMsg($msg);
	sendbuymail($OrderNo);
	$arr=array("slackcheck"=>"S","OrderNo"=>$OrderNo);
	insertInto($arr,["OrderNo"],$dbset['table']['orders']);
}


function OrderSuccessMsgSlackDbCheck($OrderNo){
	$now = time();
	$now = date("Y-m-d H:i:s");
	//$checkTime=strtotime($now. "-1hour");
	//$checkTime=date("Y-m-d H:i:s",$checkTime);
	global $dbset,$TransCode;
	$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' AND `slackcheck` NOT LIKE 'S' LIMIT 1";

	if( count(doSQLgetRow($sql)) == 0 ){
		echo "沒這個單喔!";
		return ;
	}

	if(isset($wordpress_setting["search_page"])){
		$search_page = $wordpress_setting["search_page"];
	}else{
		$search_page = "";
	}

	$QuickShippingURL = QuickShippingURL($OrderNo);

	if($QuickShippingURL){
		$QuickShippingURLBtn = "\r\n"."設為出貨: ".$QuickShippingURL;
	}else{
		$QuickShippingURLBtn = "";
	}

	$orderinfo=doSQLgetRow($sql)[0];
	$reg_date=date_create($orderinfo["reg_date"]);
	$reg_date=date_format($reg_date,'Y-m-d');
	$BuysafeNo = $orderinfo["BuysafeNo"]==""?"未產生":$orderinfo["BuysafeNo"];
	$buyer=unserialize($orderinfo["buyer"]);
	$buyer=urldecodeArray($buyer);
	$TotalPrice="$".number_format($orderinfo["TotalPrice"]);
	$TranStatusT = $TransCode["TranStatus"][$orderinfo["TranStatus"]];
	$CargoList=unserialize($orderinfo["CargoList"]);
	$orderlist="\r\n";

	if($search_page){
		$search_url = "詳細資料: ".$search_page."?email=".urlencode($buyer["bemail"])."&OrderNo=".$OrderNo;
	}else{
		$search_url = "";
	}

	foreach ($CargoList as $key => $val) {
		$title= urldecode($val["title"]);
		$orderlist.="        ".$title." x ".number_format($val["amount"])."\r\n";
	}
	$memo="\r\n".urldecode($orderinfo["Note1"]);
	$msg=
":dollar: :dollar: :dollar:
系統自動檢查交易狀態，以下訂單交易成功:
檢查時間: ".$now."
下單日期: ".$reg_date."
訂單編號: ".$OrderNo."
金流單號: ".$BuysafeNo."
買者姓名: ".$buyer["bname"]."
電子郵件: ".$buyer["bemail"]."
購物金額: ".$TotalPrice."
購賣物品: ".$orderlist."
交易備註: ".$memo."
交易結果: ".$TranStatusT."
".$search_url.$QuickShippingURLBtn;


	slackMsg($msg);
	sendbuymail($OrderNo);
	$arr=array("slackcheck"=>"S","OrderNo"=>$OrderNo);
	insertInto($arr,["OrderNo"],$dbset['table']['orders']);
}


function FixEmailMysql($email){
	$email=urlencode($email);
	//echo $email."<br>";
	$email=str_replace("[","\[",$email);
	//echo $email."<br>";
	$email=str_replace("_","\_",$email);
	//echo $email."<br>";
	$email=str_replace("%","\%",$email);
	//echo $email."<br>";
	return $email;

}


function inputCreater($arr){


	$default=array(
					"type"=>"text",
					"name"=>"test",
					"readonly"=>false,
					"required"=>false,
					"class"=>"sm-form-control",
					"value"=>""
				);

	$output="";

	foreach($arr as $key => $val){

		$val=array_merge($default,$val);

		if(isset($val["label"]) && $val["label"] !=""):
			$label="<label>".$val["label"]."</label>";
		else:
			$label="";
		endif;


		$tags=array();


		foreach($val as $ind => $value):

			switch ($ind) {
				case in_array($ind, ["label"]):
					break;
			    case in_array($ind, ["readonly","required"]):

		        	if($value==true):
		        		$tags[]=$ind;
		        	endif;
			        break;
			    default:

			    	if($value !=""):
			    		if(is_array($value)){
			    			break;
			    			continue;
			    		}

			        	$tags[]=$ind."='".$value."'";
			        endif;
			    break;

			}

		endforeach;


		$tagsOutput=implode(" ", $tags);

		if($val["type"]=="options"):

			$optionsoutput="<select class='selectpicker' name='".$val["name"]."' >";

			  if(!isset($val["value"]) ){
			  	$optionsoutput.='<option value="" selected>選擇動作</option>';
			  }

			  foreach ($val["options"] as $key => $opt){


			  	if($key == $val["value"]){

			  		$optionsoutput.='<option value="'.$key.'" selected>'.$opt.'</option>';
			  	}else{
			  		$optionsoutput.='<option value="'.$key.'">'.$opt.'</option>';
			  	}



			  }

			$optionsoutput.="</select>";
		endif;


		if($val["type"]=="checkbox"):

			  $optionsArray=array();

			  foreach ($val["options"] as $key => $opt){

			  	if(in_array( $key , $val["value"] )){

			  		$optionsArray[]='<input type="checkbox" name="'.$val["name"].'" value="'.$key.'" checked> '.$opt;
			  	}else{
			  		$optionsArray[]='<input type="checkbox" name="'.$val["name"].'" value="'.$key.'" > '.$opt;
			  	}

			  }

			  $optionsoutput=join("&emsp;",$optionsArray);

		endif;

		if($val["type"]=="coloroptions"):

			function coloroptionmaker($coloroptions){
				$coloroptionsoutput="";
				$default=array(
					"number"=>"",
					"name"=>"",
					"img"=>""
					);

				if(!isset($coloroptions["colors"]) && count($coloroptions["colors"])==0):
					$coloroptions["colors"][]=$default;
				endif;

				foreach($coloroptions["colors"] as $tags =>$color):
					$color=array_merge($default,$color);
					$coloroptions="<div class='col-md-2'><input type='color'  name='".$coloroptions["name"]."[number][]' value='".$color["number"]."'></div>";
					$colorname="<div class='col-md-5'><input type='text' name='".$coloroptions["name"]."[name][]' class='sm-form-control' value='".$color["name"]."' placeholder='色彩名稱' ></div>";
					$colorimage="<div class='col-md-5'><input type='text' name='".$coloroptions["name"]."[img][]' class='sm-form-control' value='".$color["img"]."' placeholder='替換圖' ></div>";
					$coloroptionsoutput.="<div class='row clearfix bottommargin-sm'>".$coloroptions.$colorname.$colorimage."</div>";
				endforeach;
				return $coloroptionsoutput;
			}
			$coloroptionsoutput=coloroptionmaker($val);

		endif;


		switch ($val) {
		    case in_array($val["type"], ["text","password","number","email"]):
		        $section= $label."<input ".$tagsOutput." />";
		        $section="<div class='col_full'>".$section."</div>\r\n";
		        break;
		    case in_array($val["type"], ["options"]):
		        $section= $label."<br>".$optionsoutput;
		        $section="<div class='col_full'>".$section."</div>\r\n";
		        break;
		    case in_array($val["type"], ["checkbox"]):
		        $section= $label."<br>".$optionsoutput;
		        $section="<div class='col_full'>".$section."</div>\r\n";
		        break;
		    case in_array($val["type"], ["textarea"]):
		        $section= $label."<textarea ".$tagsOutput." >".$val["value"]."</textarea>";
		        $section="<div class='col_full'>".$section."</div>\r\n";
		        break;
		    case in_array($val["type"], ["hidden"]):
		        $section= $label."<input ".$tagsOutput." />";
		        break;
		    case in_array($val["type"],["coloroptions"]):
		    	$section= $label.$coloroptionsoutput;
		    	$section="<div class='col_full' id='".$val["id"]."'>".$section."</div>\r\n";
		    	break;


		}

		$output.=$section;
	}

	return $output;
}


//USERS LOGIN--------------------------------------------------------

function login($userid,$password){
	global $db_conn;

	$sql = "SELECT `id`,`userid`,`email`,`role`,`name` FROM `users` WHERE  (`userid` LIKE '".urlencode($userid)."' OR `email` LIKE '".urlencode($userid)."') AND binary `password` LIKE '".urlencode($password)."' LIMIT 1;";

	//echo $sql."<br>";

	if (!$db_conn) {
		die("資料庫連線錯誤!");
	}else{

		$result=mysqli_query($db_conn, $sql);
		$num = mysqli_num_rows($result);
		if($num >0 ):
			$row = mysqli_fetch_assoc($result);
			return $row;
		else:
			return false;
		endif;

	}

}

function isLogin(){
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	global $_SESSION;
	if(isset($_SESSION["user"]["role"])):
		return $_SESSION["user"];
	else:
		return false;
	endif;
}


function notLogin(){

	if(isLogin()==false):
		gotoUrl("login.php");
		die();
	endif;
}


function notRole($arr,$url="login.php"){
	$role=isLogin()["role"];

	if(in_array($role, $arr) || $role=="super"){
		return false;
	}

	if(isset($url)){
		gotoUrl($url);
		die();
	}else{
		gotoUrl("login.php");
		die();
	}

}

function isRole($arr,$WihtSuper=true){

	$role=isLogin()["role"];


	if( $WihtSuper == true ){
		$arr[]="super";
	}


	if(in_array($role, $arr)){
		return true;
	}else{
		return false;
	}

}




function logOut(){
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	global $_SESSION;
	unset($_SESSION["user"]);
}



//settings--------------------------------------------------------------------

function updateSettings($arr,$tableName){

	global $db_conn;



	$arraykeys=array();
	$arrayvals=array();


		foreach($arr as $key => $val):
			$arraykeys[]=$key;

			$arrayvals[]=$val;
		endforeach;

	$tb_row="`".implode("` , `", $arraykeys)."`";
	$value="'".implode("' , '", $arrayvals)."'";

	if (!$db_conn) {
		die("資料庫連線失敗!");
	}else{

		$sql = "REPLACE INTO `".$tableName."` ( ".$tb_row.") VALUES (".$value.");";
		echo $sql;
		mysqli_query($db_conn, $sql);


	}

}








//MYSQL-------------------------------------------------------------------------------------------



function insertInto($arr,$keyArray,$tableName){

	global $db_conn;



	$arraykeys=array();
	$arrayvals=array();
	$arrayupdate=array();


		foreach($arr as $key => $val):

			$arraykeys[]=$key;
			$arrayvals[]=$val;

			if( !in_array($key,$keyArray)):

				$arrayupdate[]= "`".$key."`"."="."VALUES("."`".$key."`".")";
			endif;



		endforeach;

	$tb_row="`".implode("` , `", $arraykeys)."`";
	$value="'".implode("' , '", $arrayvals)."'";
	$updates=implode(" , ", $arrayupdate);

	if (!$db_conn) {
		die("資料庫連線失敗!");
	}else{

		$sql='SET @@SESSION.sql_mode = "ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";';
		mysqli_query($db_conn, $sql);

		$sql = "INSERT INTO `".$tableName."` ( ".$tb_row.") VALUES (".$value.") ON DUPLICATE KEY UPDATE ".$updates.";";

		mysqli_query($db_conn, $sql);


	}

}




function urlencodeArray($arr){

	foreach($arr as $key => $val):

		$arr[$key]=urlencode($val);

	endforeach;

	return $arr;
}

function urldecodeArray($arr){

	foreach($arr as $key => $val):

		$arr[$key]=urldecode($val);

	endforeach;

	return $arr;
}




function insert_table($arr,$tableName){


	global $db_conn;

	$arraykeys=array();
	$arrayvals=array();
	foreach($arr as $key => $val):
		$arraykeys[]=$key;
		$arrayvals[]=urlencode($val);
	endforeach;

	$tb_row="`".implode("` , `", $arraykeys)."`";
	$value="'".implode("' , '", $arrayvals)."'";

	$sql = "INSERT INTO `".$tableName."` ( ".$tb_row.") VALUES (".$value.");";



	if (!$db_conn) {
		echo "F";
	}else{

		$sql='SET @@SESSION.sql_mode = "ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";';
		mysqli_query($db_conn, $sql);

		$sql = "INSERT INTO `".$tableName."` ( ".$tb_row.") VALUES (".$value.");";
		mysqli_query($db_conn, $sql);

		echo "S";


	}


}




function check_value($val,$coulmn,$tableName){
	global $db_conn;
	$sql = "SELECT `".$coulmn."` FROM `".$tableName."` WHERE `".$coulmn."` LIKE '".$val."';";

	//return $sql;
	$result=mysqli_query($db_conn, $sql);
	$num=mysqli_num_rows($result);
	return $num;
}

function doSQLgetRow($sql){
	global $db_conn;

	$result=mysqli_query($db_conn, $sql);
	$output=array();

	while($row = mysqli_fetch_assoc($result)):

 		$output[]=$row;

     endwhile;

     return $output;

}

function AddIfNotExist($tableName,$column,$value,$targetColumn,$targetValue,$onlysql=false){
	global $db_conn;
	$sql="UPDATE  `".$tableName."` SET `".$column."` = CASE
                 WHEN `".$column."` LIKE '' THEN '".$value."'
                 WHEN `".$column."` NOT LIKE '' AND `".$column."` NOT LIKE  '%".$value."%' THEN CONCAT(`".$column."`, '+".$value."')
                 ELSE `".$column."`
                 END
        WHERE `".$targetColumn."` LIKE '". $targetValue."%'; ";


   // echo $sql;


   if($onlysql == false){
   		mysqli_query($db_conn, $sql);
   }else{
   		return $sql;
   }

}


function AddIfNotExistArray($tableName,$column,$value,$targetColumn,$targetArray,$onlysql=false){
	global $db_conn;

	$ids = join("','",$targetArray);


	$sql="UPDATE  `".$tableName."` SET `".$column."` = CASE
                 WHEN `".$column."` LIKE '' THEN '".$value."'
                 WHEN `".$column."` NOT LIKE '' AND `".$column."` NOT LIKE  '%".$value."%' THEN CONCAT(`".$column."`, '+".$value."')
                 ELSE `".$column."`
                 END
        WHERE `".$targetColumn."` IN ('".$ids."')";


   // echo $sql;


   if($onlysql == false){
   		mysqli_query($db_conn, $sql);
   }else{
   		return $sql;
   }

}


function ReplaceExistArray($tableName,$column,$value,$result,$targetColumn,$targetArray,$onlysql=false){
	global $db_conn;

	$ids = join("','",$targetArray);

	$sql_1="UPDATE  `".$tableName."` SET `".$column."` = REPLACE (`".$column."`,'+".$value."', '".$result."')
		 WHERE `".$targetColumn."` IN ('".$ids."');";
	$sql="UPDATE  `".$tableName."` SET `".$column."` = REPLACE (`".$column."`,'".$value."', '".$result."')
		 WHERE `".$targetColumn."` IN ('".$ids."');";


   // echo $sql;


   if($onlysql == false){
   		mysqli_query($db_conn, $sql_1);
   		mysqli_query($db_conn, $sql);
   }else{
   		return $sql;
   }

}




function removeFromTable($column,$valarr,$tableName){
	global $db_conn;
	if (!$db_conn) {
		return "F";
	}else{
		if(!isset($valarr) || $valarr===""){
			return;
		}

		$vals=join("','",$valarr);
		$sql="DELETE FROM `".$tableName."` WHERE `".$column."` IN ('".$vals."')" ;
		mysqli_query($db_conn, $sql);
		//echo $sql;
		return "S";
	}

}

function csvToArray($file, $delimiter) {

	$keys = array();
	$newArray = array();

  if (($handle = fopen($file, 'r')) !== FALSE) {
    $i = 0;
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) {
      for ($j = 0; $j < count($lineArray); $j++) {
        $arr[$i][$j] = $lineArray[$j];
      }
      $i++;
    }
    fclose($handle);
  }


	  // Do it
	$data = $arr;
	// Set number of elements (minus 1 because we shift off the first row)

	$count = count($data) - 1;

	//Use first row for names
	$labels = array_shift($data);
	$i=0;
	foreach ($labels as $label) {
	  if($label ==""){
	  	$label="coulmn_".$i;
	  }
	  	$label=str_replace ("#65279;","",$label);
	  	$keys[] = trim($label);

	  	$i++;

	  //$keys[] = iconv("utf-8", "big5",$label);
	}
	// Add Ids, just in case we want them later
	//$keys[] = 'id';
	array_unshift($keys, 'id');
	for ($i = 0; $i < $count; $i++) {
	 //$data[$i][] = $i;

	  array_unshift($data[$i], $i);
	}

	// Bring it all together
	for ($j = 0; $j < $count; $j++) {


	  	$d = array_combine($keys, $data[$j]);
	  //$d = array_combine($keys, iconv("utf-8", "big5",$data[$j]));
	  $newArray[$j] = $d;
	}



  return $newArray;
}


//JAVASCRITP 轉址---------------------------------------------------------------------------------------------------
function gotoUrl($url){

	echo '<script language="JavaScript">
			window.location.replace("'.$url.'");
		  </script>';

};




//paynow------------------------------------------------------------------------------------------------------------


//產品JSON
function get_json_from($url){
	$obj = file_get_contents($url);
	$obj = json_decode($obj,true);
	return $obj;
}


//Orderinfo

function PaynowOrderInfoMaker($ProductList,$arrOrderInfo,$shippingFee){

	global $buyer;

	$arrOrderInfo=json_decode($arrOrderInfo,true);


	function ListMaker($order,$ProductList){


		$key = array_search($order["id"], array_column($ProductList, 'id'));

		$Product=$ProductList[$key];


		switch ($Product["onsale"]) {
			case '1':

				if(isset($Product["saleprice"]) && $Product["saleprice"] < $Product["price"]):

					$price=$Product["saleprice"];
				else:
					$price=$Product["price"];
				endif;

			break;

			default:
				$price=$Product["price"];
		}





		$options=array();

		if(isset($order["color"])){
			$options[]=$order["color"];
		}

		if(isset($order["otherOptions"])){

			foreach($order["otherOptions"] as $key => $val):
				$options[]=$val["value"];
			endforeach;
		}


		if(count($options) >0){
			$options=implode("、",$options);
			$options="(".$options.")";
		}else{
			$options="";
		}


		$total=$price*$order["amount"];

		$List=array();

		if($Product["id"]=="8"){
			$Product["title"].=" (有把套) ";
		}



		$List["info"]=$Product["title"].$options."_".$price."_".$order["amount"]."_".$total;
		$List["subtotal"]=$total;

		return $List;

	}



	$output=array();
	$orderList=array();
	$total=0;

	//包包特價---------------------------------------------------------
	/*
	$bagAmount=0;

	foreach ($arrOrderInfo as $key => $order){
		if( $order["id"]=="8"){
			$bagAmount += $order["amount"];
		}
	}

	if($bagAmount >=2){

		$key = array_search("8", array_column($ProductList, 'id'));

		$ProductList[$key]["onsale"]="1";
		$ProductList[$key]["saleprice"]=4280;



	}
	*/

	//包包特價結束-----------------------------------------------------

	foreach ($arrOrderInfo as $key => $order) {

		$List=ListMaker($order,$ProductList);
		$orderList[]=$List["info"];
		$total+=$List["subtotal"];

	}

	if(isset($shippingFee) && $shippingFee>0){

		$orderList[]="運費_".$shippingFee."_1_".$shippingFee;
		$total+=$shippingFee;

	}

	if(isset($buyer["receipt"]) && strlen($buyer["receipt"])>0){
		$orderList[0]=$orderList[0]."_".$buyer["receipt"];
	}

	$output["OrderInfo"]=implode(";",$orderList);
	$output["TotalPrice"]=$total;


	return $output;

};


//出貨列表產生器

function CargoListMaker($ProductList,$arrOrderInfo){

	$arrOrderInfo=json_decode($arrOrderInfo,true);


	function CListMaker($order,$ProductList){


		//$output=array();

		$key = array_search($order["id"], array_column($ProductList, 'id'));

		$Product=$ProductList[$key];



		switch ($Product["onsale"]) {
			case '1':

				if(isset($Product["saleprice"]) && $Product["saleprice"] < $Product["price"]):

					$price=$Product["saleprice"];
				else:
					$price=$Product["price"];
				endif;

			break;

			default:
				$price=$Product["price"];
		}





		$options=array();

		if(isset($order["color"])){
			$options[]=$order["color"];
		}

		if(isset($order["otherOptions"])){

			foreach($order["otherOptions"] as $key => $val):
				$options[]=$val["value"];
			endforeach;
		}


		if(count($options) >0){
			$options=implode("、",$options);
			$options="(".$options.")";
		}else{
			$options="";
		}

		if($Product["id"]=="8"){
			$Product["title"].=" (有把套) ";
		}


		$output["price"]=$price;
		$output["title"]=urlencode($Product["title"].$options);
		$output["amount"]=$order["amount"];


		return $output;

	}




	//包包特價---------------------------------------------------------

	/*$bagAmount=0;

	foreach ($arrOrderInfo as $key => $order){
		if( $order["id"]=="8"){
			$bagAmount += $order["amount"];
		}
	}

	if($bagAmount >=2){

		$key = array_search("8", array_column($ProductList, 'id'));

		$ProductList[$key]["onsale"]="1";
		$ProductList[$key]["saleprice"]=4280;



	}	*/


	//包包特價結束-----------------------------------------------------



	$output=array();

	foreach ($arrOrderInfo as $key => $order) {
		//print_r($order);
		$output[]=CListMaker($order,$ProductList);
		//$output[]="TESTER";
	}

	//print_r($output);


	return $output;

};




//序號產生器_亂數
function serialno_advance($ser,$datetoster=0,$length="3"){
	global $db_conn,$dbset;
	$final="";
	$random_id=random($length);
	if($datetoster==1){
		$number=$ser.dateToser().date('md').$random_id;
	}else{
		$number=$ser.date('y').date('md').$random_id;
	}

	//echo $ser;
	if ($db_conn) {

		$sql="SELECT `OrderNo` FROM `".$dbset['table']['orders']."` WHERE `OrderNo` LIKE '%".$number."%' ORDER BY id DESC LIMIT 1";
		//$serleng=strlen($ser);
		$result = mysqli_query($db_conn, $sql);

		if (mysqli_num_rows($result) > 0) {
      		serialno_advance($ser);
      	}else{
      	 	$final= $number;
      	 	return $final;
      	}

		//mysqli_query($db_conn, $sql);
		//mysqli_close($db_conn);

	}

	//echo $final."</br>";


}


function random($length){
	//$random預設為10，更改此數值可以改變亂數的位數----(程式範例-PHP教學)
		$random=$length;
		//FOR回圈以$random為判斷執行次數
		$randoma="";
		for ($i=1;$i<=$random;$i=$i+1)
		{
		//亂數$c設定三種亂數資料格式大寫、小寫、數字，隨機產生
		$c=rand(1,3);
		//在$c==1的情況下，設定$a亂數取值為97-122之間，並用chr()將數值轉變為對應英文，儲存在$b
		if($c==1){$a=rand(97,122);$b=chr($a);}
		//在$c==2的情況下，設定$a亂數取值為65-90之間，並用chr()將數值轉變為對應英文，儲存在$b
		if($c==2){$a=rand(65,90);$b=chr($a);}
		//在$c==3的情況下，設定$b亂數取值為0-9之間的數字
		if($c==3){$b=rand(0,9);}
		//使用$randoma連接$b
		$randoma=$randoma.$b;
		}
		//輸出$randoma每次更新網頁你會發現，亂數重新產生了
		return $randoma;
}




function dateToser(){


	$dateser=array();
	$dateser[17]="A";
	$dateser[18]="B";
	$dateser[19]="C";
	$dateser[20]="D";
	$dateser[21]="E";
	$dateser[22]="F";
	$dateser[23]="F";
	$dateser[24]="G";
	$dateser[25]="H";

	return $dateser[date('y')];

}




//phpmailer----------------------------------------------------------------------------------------------------------------
function sendmail($sentto="",$receiver="",$subject="",$msg="",$custom=array()){

  $setting = getSettingVal("phpmailer");

  if($setting ==false):

  	return false;

  endif;

  if(isset($custom) && is_array($custom)):

 	$setting=array_merge($setting,$custom);

  endif;



  $setting["sentto"]=$sentto;
  $setting["receiver"]=$receiver;
  $setting["subject"]=$subject;
  $setting["msg"]=$msg;


  mb_internal_encoding('UTF-8');
  require_once 'phpmailer/PHPMailerAutoload.php';


  //Create a new PHPMailer instance
  $mail = new PHPMailer;

  $mail->CharSet = 'UTF-8';
  //Tell PHPMailer to use SMTP
  $mail->isSMTP();
  //Enable SMTP debugging
  // 0 = off (for production use)
  // 1 = client messages
  // 2 = client and server messages
  $mail->SMTPDebug = 0;
  //Ask for HTML-friendly debug output
  $mail->Debugoutput = 'html';
  //Set the hostname of the mail server
  $mail->Host = $setting["host"];
  //Set the SMTP port number - likely to be 25, 465 or 587
  $mail->Port = $setting["port"];
  //Whether to use SMTP authentication
  $mail->SMTPAuth = true;
  $mail->SMTPSecure = 'ssl';
  //Username to use for SMTP authentication
  $mail->Username =  $setting["username"];
  //Password to use for SMTP authentication
  $mail->Password = $setting["password"];
  //Set who the message is to be sent from
  $mail->setFrom($setting["frommail"], mb_encode_mimeheader($setting["mailer"], "UTF-8"));
  //Set an alternative reply-to address
  $mail->addReplyTo($setting["frommail"], mb_encode_mimeheader($setting["mailer"], "UTF-8"));
  //Set who the message is to be sent to
  $mail->addAddress($setting["sentto"], mb_encode_mimeheader($setting["receiver"], "UTF-8"));
  //cc
  $mail->AddBCC($setting["bccto"], mb_encode_mimeheader('自動發信備分', "UTF-8"));
  //Set the subject line
  $mail->Subject = mb_encode_mimeheader($setting["subject"], "UTF-8");
  //Read an HTML message body from an external file, convert referenced images to embedded,
  //convert HTML into a basic plain-text alternative body
  //$mail->msgHTML(file_get_contents('http://allrover.com.tw/paynow2/editor/examples/simple/allrover_simple_save.html'), dirname(__FILE__));
  $mail->msgHTML($setting["msg"]);
  //Replace the plain text body with one created manually
  $mail->AltBody = 'This is a plain-text message body';


  if (!$mail->send()) {
     return "F";
  } else {
     return "S";
  }

}




function Stripe_sendmail($sentto="",$receiver="",$subject="",$message="",$type="",$custom_id="",$custom=array()){

	$result=sendmail($sentto,$receiver,$subject,$message,$custom);
	$arr=array(
		"type"=>$type,
		"receiver_name"=>$receiver,
		"time"=>date("Y-m-d H:i:s"),
		"receiver_mail"=>$sentto,
		"custom_id"=>$custom_id
	);
	if($result=="S"){
		insertInto($arr,["id"],"mail_log");
	}
	return $result;
}


function Coupon_For_Wordpress($OrderNo="",$coupon="",$fail_coupon=false){

	global $wordpress_setting;

	if($wordpress_setting != false && $wordpress_setting["active"]== 1 && $wordpress_setting["mail_url"]!==""&& $coupon !=="" && $OrderNo !==""){


	  $curl = curl_init(); //开启curl
	  if($fail_coupon==true){
	  	$apiurl=$wordpress_setting["mail_url"]."?action=UpdateCoupon&failed_order";
	  }else{
	  	$apiurl=$wordpress_setting["mail_url"]."?action=UpdateCoupon";
	  }
	  /*
	  if(!isset($row) || !isset($row["OrderNo"]) || !isset($row["CargoList"])){

	  	return;
	  }
	  */
	  //var_dump($apiurl);
	  $post_data=array(
	    "OrderNo"=>$OrderNo,
	    "coupon"=>$coupon
	  );


	  //var_dump( $post_data);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	  curl_setopt($curl, CURLOPT_URL, $apiurl); //设置请求地址
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	  curl_setopt($curl, CURLOPT_POST, true);
	  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $obj = curl_exec($curl); //执行curl操作
	  curl_close($curl);

	}

}



function Store_For_Wordpress($row=array(),$addback=false){
	global $wordpress_setting,$dbset;
	$OrderNo = $row["OrderNo"];
	if($wordpress_setting != false && $wordpress_setting["active"]== 1 && $wordpress_setting["mail_url"]!==""&& $OrderNo !==""){


	  $curl = curl_init(); //开启curl
	  if($addback==true){
	  	$apiurl=$wordpress_setting["mail_url"]."?action=UpdateStore&addback";
	  }else{
	  	$apiurl=$wordpress_setting["mail_url"]."?action=UpdateStore";
	  }

	  if(!isset($row) || !isset($row["OrderNo"]) || !isset($row["CargoList"])){

	  	return;
	  }

	  //var_dump($apiurl);
	  $post_data=array(
	    "OrderNo"=>$row["OrderNo"],
	    "items"=>unserialize($row["CargoList"]),
	  );


	  //var_dump( $post_data);
	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	  curl_setopt($curl, CURLOPT_URL, $apiurl); //设置请求地址
	  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	  curl_setopt($curl, CURLOPT_POST, true);
	  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $obj = curl_exec($curl); //执行curl操作
	  curl_close($curl);

	  return $obj;

	}

}

function Reduce_store_for_Wordpress($OrderNo=""){
	global $wordpress_setting,$dbset;
	if($wordpress_setting != false && $wordpress_setting["active"]== 1 && $wordpress_setting["mail_url"]!==""&& $OrderNo !==""){

		$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' AND  `SendStatus` NOT LIKE '%StoreChecked%'";

		$result=doSQLgetRow($sql);

		if(count($result)==0){
			return;
		}

		$row=$result[0];


		$feedback=Store_For_Wordpress($row,$addback=false);


		if($feedback=="S"){

			AddIfNotExist($dbset["table"]["orders"],"SendStatus","StoreChecked","OrderNo",$OrderNo);
		}
	}
}

function Addback_store_for_Wordpress($OrderNo=""){

	global $wordpress_setting,$dbset,$db_conn;

	if($wordpress_setting != false && $wordpress_setting["active"]== 1 && $wordpress_setting["mail_url"]!==""&& $OrderNo !==""){

		$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' AND  `SendStatus` LIKE '%StoreChecked%'";

		$result=doSQLgetRow($sql);

		if(count($result)==0){
			return;
		}

		$row=$result[0];

		$feedback=Store_For_Wordpress($row,$addback=true);

		if($feedback=="S"){
			$sql="UPDATE  `".$dbset["table"]["orders"]."` SET `SendStatus` = REPLACE (`SendStatus`,'+StoreChecked', '') WHERE `OrderNo` LIKE '".$OrderNo."';";
			mysqli_query($db_conn, $sql);
			//mysqli_affected_rows($db_conn) ;
		 	$sql="UPDATE  `".$dbset["table"]["orders"]."` SET `SendStatus` = REPLACE (`SendStatus`,'StoreChecked', '') WHERE `OrderNo` LIKE '".$OrderNo."';";
		 	mysqli_query($db_conn, $sql);
		 	//mysqli_affected_rows($db_conn) ;

		}
	}
}
