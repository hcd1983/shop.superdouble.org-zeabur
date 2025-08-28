<?php
//ini_set('display_errors', 1); 
require_once("functions.php");
date_default_timezone_set('Asia/Taipei');

$order=$_POST["order"];
$buyer=$_POST["buyer"];

if(strlen($order["ReceiverName"])==0 ){
  echo "<script>window.history.back();</script>";
  exit();
}

if(strpos($order["ReceiverEmail"], "test") != false){
   echo "<script>window.history.back();</script>";
  exit();
}


if(strlen($order["OrderInfo"])==0 ){
   echo "<script>window.history.back();</script>";
  exit();
}


$buyer["bname"]=substr( strip_tags(addslashes(trim($order["ReceiverName"]))),0,40);
$buyer["bphone"]=substr( strip_tags(addslashes(trim($order["ReceiverTel"]))),0,12);
$buyer["bemail"]=substr( strip_tags(addslashes(trim($order["ReceiverEmail"]))),0,50);

$OrderInfo=json_decode($order["OrderInfo"],true);

$CargoList=array();
$OrderContent=array();

$order["TotalPrice"]=0;

foreach ($OrderInfo as $key => $the_order) {    
    $the_order["title"]=str_replace("&#8211;","-", $the_order["title"]);
    $OrderContent[]=$the_order["title"]."_".$the_order["price"]."_".$the_order["amount"]."_".($the_order["amount"]*$the_order["price"]);
    $the_order["title"]=urlencode($the_order["title"]);
    $CargoList[]=$the_order;

    $order["TotalPrice"]+=($the_order["amount"]*$the_order["price"]);
      
}

if(isset($_POST["shippingFee"]) && $_POST["shippingFee"]>0 ){
  $shippingFee=$_POST["shippingFee"];
  $OrderContent[]="運費_".$shippingFee."_1_".$shippingFee;   
}else{
  $shippingFee=0;
}

if(strlen($buyer["receipt"])>0){
  $OrderContent[0]=$OrderContent[0]."_".$buyer["receipt"];
} 

$OrderInfo=join(";",$OrderContent);
$order["OrderInfo"]=$OrderInfo;

//===================
unset($OrderContent);

$CargoList=$CargoList;


if(isset($_POST["receiver"]) && $_POST["receiver"]==1):
  $receiver=$_POST["receiverdata"]; 
else:
  $receiver=array();    
endif;

$buyer=urlencodeArray($buyer);
$receiver=urlencodeArray($receiver);
$order=urlencodeArray($order);
$paynow=getSettingVal("paynow");
//print_r(getSettingVal("paynow"));

$arr=$order;
$arr["OrderType"]="A";
$arr["buyer"]=serialize($buyer);
$arr["receiver"]=serialize($receiver);
$arr["Note2"]=count($arr["receiver"])==0?$arr["buyer"]:$arr["receiver"];
$arr["OrderNo"]=serialno_advance($paynow["serial"]);
$arr["reg_date"]=date('Y/m/d H:i:s');
//$arr["TotalPrice"]=$order["TotalPrice"];
$arr["CargoList"]=serialize($CargoList);
$arr["ShippingFee"]=$shippingFee;
$arr["return_url"]=$_POST["return_url"];



$PassCode= sha1($paynow["WebNo"].$arr["OrderNo"].$arr["TotalPrice"].$paynow["shopkey"]);

$arr["PassCode"]=$PassCode;

insertInto($arr,["OrderNo"],$dbset['table']['orders']);

?>
<form name="form1"  id="form1" action="https://www.paynow.com.tw/service/etopm.aspx" method="post">

<input type="hidden" name="WebNo" Value="<?php echo $paynow["WebNo"]; ?>">
<input type="hidden" name="PassCode" Value="<?php echo $arr["PassCode"]; ?>">
<input type="hidden" name="OrderNo" Value="<?php echo $arr["OrderNo"]; ?>" >
<input type="hidden" name="ECPlatform" Value="<?php echo $paynow["ECPlatform"]; ?>">
<input type="hidden" name="TotalPrice"  Value="<?php echo $arr["TotalPrice"]; ?>">
<input type="hidden" name="OrderInfo"  Value="<?php echo $arr["OrderInfo"]; ?>">
<input type="hidden" name="ReceiverTel"  Value="<?php echo $arr["ReceiverTel"]; ?>">
<input type="hidden" name="ReceiverName"  Value="<?php echo $arr["ReceiverName"]; ?>">
<input type="hidden" name="ReceiverEmail" Value="<?php echo $arr["ReceiverEmail"]; ?>">
<input type="hidden" name="ReceiverID"  Value="<?php echo $arr["ReceiverEmail"]; ?>">
<input type="hidden" name="Note1" Value="<?php echo $arr["Note1"]; ?>">
<input type="hidden" name="Note2" Value="<?php echo $arr["Note2"]; ?>">
<input type="hidden" name="PayType" Value="<?php echo $arr["PayType"]; ?>">
<input type="hidden" name="AtmRespost" Value="<?php echo $paynow["AtmRespost"]; ?>">
<input type="hidden" name="Deadline" Value="<?php echo $paynow["DeadLine"]; ?>">
<input type="hidden" name="PayEN" Value="<?php echo $paynow["PayEN"]; ?>">
</form>
<p>頁面自動轉跳中...(如未自動轉跳，請點擊按扭)<input type="button" value="轉跳" onclick="$('#form1').submit();"></p>


<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">


  $('#form1').submit(); // SUBMIT FORM
</script>


<?php
exit();