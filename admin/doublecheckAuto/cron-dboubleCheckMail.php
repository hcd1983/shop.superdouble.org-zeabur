<?php
date_default_timezone_set('Asia/Taipei');
set_time_limit(0);
require_once("../functions.php");

function resendFailMail($OrderNo, $mailto, $extra = ''){
    global $TransCode, $wordpress_setting, $dbset;

    $buymail_setting = getSettingVal("buymail");
    if($buymail_setting == false){
        return;
    }

    if(!isset($buymail_setting["mailto"]) || !$buymail_setting["mailto"]){
        return;
    }

    if(isset($wordpress_setting["search_page"])){
        $search_page = $wordpress_setting["search_page"];
    }else{
        $search_page = "";
    }

    $sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";

    if( count(doSQLgetRow($sql)) == 0 ){
        return "F";
    }

    $orderinfo = doSQLgetRow($sql)[0];

    $OrderNo = $orderinfo["OrderNo"];
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

    $sendto = $buymail_setting["mailto"];
    $_sendto = explode("\r\n", $sendto);
    $subject = "["._WebTitle." 網站]交易通知 單號- $OrderNo" ;

    $receipt = "";
    $msg=
        "{$extra}
    下單日期: ".$reg_date."
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

    $receiver = "";
    $useLaravel = useLaravelMail();
    if ($useLaravel) {
        $result = laravelSystemMail($OrderNo,$buyer["bemail"],$mailto);
    }else {
        $result = sendmail($mailto, $receiver, $subject, $_msg);
    }
    return $result;
}


// 幾分鍾內的訂單
$offsetTime = 60 * 4;
// email 寬限 3 小時
$offsetTimeMail = $offsetTime + 180;

$now = time();
$now = date("Y-m-d H:i:s");
// 訂單的時間
$checkTime = strtotime($now. "-{$offsetTime} minutes");
$checkTime = date("Y-m-d H:i:s",$checkTime);

// 寄信的時間
$checkTimeMail = strtotime($now. "-{$offsetTimeMail} minutes");
$checkTimeMail = date("Y-m-d H:i:s",$checkTimeMail);

$admin_emails = get_admin_emails ();


foreach ($admin_emails as $key => $email) {
    echo "<h1>".$email."</h1>";
    // mail log
    $sql = "SELECT DISTINCT `OrderNo` FROM `admin_mail_log` WHERE `create_at` >= '$checkTimeMail' AND sendTo LIKE '$email'";
    $sendLog = doSQLgetRow($sql);
// success orderno
    $_sql = "SELECT * FROM `orders` WHERE `reg_date` >= '$checkTime' AND `TranStatus` Like 'S'";
    $orders = doSQLgetRow($_sql);
    $sendLogOrderNo = [];
    foreach ($sendLog as $key => $log) {
        $sendLogOrderNo[] = $log["OrderNo"];
    }
    $notMatch = [];
    foreach($orders as $key => $order) {
        $OrderNo = $order['OrderNo'];
        if (!in_array($OrderNo, $sendLogOrderNo)) {
            echo $OrderNo." -- ";
            echo "下單時間: {$order["reg_date"]}";
            $notMatch[] = $OrderNo;
            send_buy_email_admin_email($OrderNo, $email);
            echo "<hr>";
        }
    }


    echo "================================================<br>";
    echo "================================================<br>";
}

$now = time();
$now = date("Y-m-d H:i:s");

$sql = "SELECT * FROM `admin_mail_log` WHERE !(`status_one` LIKE 'S' OR `status_two` LIKE 'S') AND `status_three` LIKE ''";

echo $sql."<br>";

$result = doSQLgetRow($sql);



if( count($result) ==0 ) {
	exit("no datas");
}


echo "Start Time: ".$now."\r\n";
echo "---------------------------------"."\r\n";;

foreach($result as $key => $mail_log) {
    echo "<pre>";
    var_dump($mail_log);
    extract($mail_log);
    echo "<h3>{$OrderNo}</h3><br>";
    if ( $status_two === '' ) {
        $checkTime = strtotime($create_at. "+20 minutes");
        $checkTime = date("Y-m-d H:i:s", $checkTime);

        if ($now > $checkTime) {
            $res = resendFailMail($OrderNo, $sendTo, '系統檢查 - 第二次');
            $sql = "UPDATE `admin_mail_log` SET `status_two` = '{$res}' WHERE `admin_mail_log`.`id` = {$id}";
            mysqli_query($db_conn, $sql);
            echo "<h4>Double check: {$res}</h4>";
        }
//
    }
    if ( $status_two === 'F') {
        $checkTime = strtotime($create_at. "+4 hours");
        $checkTime = date("Y-m-d H:i:s", $checkTime);

        if ($now > $checkTime) {
            $res = resendFailMail($OrderNo, $sendTo, '系統檢查 - 第三次');
            $sql = "UPDATE `admin_mail_log` SET `status_three` = '{$res}' WHERE `admin_mail_log`.`id` = {$id}";
            mysqli_query($db_conn, $sql);
            echo "<h4>Third check: {$res}</h4>";
        }
    }
    echo "</pre>";
}


echo "All Fixed";


