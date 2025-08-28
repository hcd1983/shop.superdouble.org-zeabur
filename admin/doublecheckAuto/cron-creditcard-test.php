<?php
set_time_limit(0);
require_once("../functions.php");

$fp = fopen (dirname(__FILE__) . '/update_credit.txt', 'w+');
fwrite($fp,date('Y-m-d h:i:s'));
fclose($fp);

//NEW HERE
// 幾分鍾內的訂單
$offsetTime = 40;
// 下單後超過多久，不再等待
$limitTime = 20;
$now = time();
$now = date("Y-m-d H:i:s");
$checkTime = strtotime($now. "-{$offsetTime} minutes");
$checkTime = date("Y-m-d H:i:s",$checkTime);

//$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `SendStatus` NOT LIKE '%DbChecked%' AND (`PayType`='01' OR `PayType`='11' ) AND `reg_date` < '".$checkTime."'";
$sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `TranStatus` NOT LIKE 'S' AND  `OrderNo` LIKE 'SD211031ZR'";
$orderinfo = doSQLgetRow($sql);
if(count($orderinfo)==0){
    exit("no datas");
}
echo "<pre>";
echo $now."\r\n";
echo $checkTime."\r\n";
echo $sql."\r\n";
echo "現在時間: ".$now."\r\n";
echo "開始查詢的時間: ".$checkTime."\r\n";
echo "---------------------------------"."\r\n";;

foreach($orderinfo as $key => $row){
    $reg_date = $row["reg_date"];
    // 寬限時間為下單開始後 ? 分鐘。超過這時間就屬於 doubleCheck
    $dueTime = strtotime($reg_date. "+{$limitTime} minutes");
    $dueTime = date("Y-m-d H:i:s", $dueTime);
    echo "下單時間: ".$reg_date."\r\n";
    echo "寬限時間 {$limitTime} 分鐘（超過就屬於 doublecheck）: ".$dueTime."\r\n";
    echo "單號: ". $row["OrderNo"]."\r\n";
    if ($now > $dueTime) {
        // 現在時間超過寬限時間，做最後檢查
        echo "已超過時間，最後檢查"."\r\n";
        $finalCheck = new dbCheck;
        $finalCheck -> Action($row["OrderNo"]);
    }else{
        // 撿查但不寫死
        echo "還沒超過時間，先檢查一次"."\r\n";
        $cronCheck = new dbCheck;
        $cronCheck -> Action_CheckButNotDouble($row["OrderNo"]);
    }
    echo "================================================================================================"."\r\n";
    echo "\r\n"."\r\n";;
}

echo "All Fixed";
echo "</pre>";