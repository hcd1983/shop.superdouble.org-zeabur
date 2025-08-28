<?php require_once("functions.php");

if(isset($_REQUEST["action"]) && $_REQUEST["action"] !=""){
  $action=$_REQUEST["action"];
}else{
  exit("error");
}

date_default_timezone_set('Asia/Taipei');

$order=$_POST["order"];
$buyer=$_POST["buyer"];
$shippingFee=$_POST["shippingFee"];
$_POST["receiver"]==0;


$custom_order_info=array();
$ProductList=array();
if(isset($_POST["custom_item_title"]) && count($_POST["custom_item_title"]) > 0){

  foreach ($_POST["custom_item_title"] as $key => $item) {
    $custom_order_info[]=array(
      "id"=>"custom_".$key,
      "amount"=>$_POST["custom_item_amount"][$key]    
    );
    $ProductList[]=array(
      "title"=>$_POST["custom_item_title"][$key],
      "price"=>$_POST["custom_item_price"][$key], 
      "id"=>"custom_".$key,
      "onsale"=>0,     
    );
  }


}


$order["OrderInfo"]=json_encode($custom_order_info);

$buyer["bname"]=substr( strip_tags(addslashes(trim($order["ReceiverName"]))),0,40);
$buyer["bphone"]=substr( strip_tags(addslashes(trim($order["ReceiverTel"]))),0,12);
$buyer["bemail"]=substr( strip_tags(addslashes(trim($order["ReceiverEmail"]))),0,50);

//$jsonurl=dirname(dirname(__FILE__))."/ProductList/products.json";
//$ProductList=get_json_from($jsonurl);
$OrderInfo=PaynowOrderInfoMaker($ProductList,$order["OrderInfo"],$shippingFee);
$CargoList=CargoListMaker($ProductList,$order["OrderInfo"]);

$order["OrderInfo"]=$OrderInfo["OrderInfo"];


if($_POST["receiver"]==1):
	$receiver=$_POST["receiverdata"];	
else:
	$receiver=array();		
endif;



$buyer=urlencodeArray($buyer);
$receiver=urlencodeArray($receiver);
$order=urlencodeArray($order);

$paynow=getSettingVal("paynow");

$arr=$order;
$arr["OrderType"]="B";
$arr["buyer"]=serialize($buyer);
$arr["receiver"]=serialize($receiver);
$arr["Note2"]=count($arr["receiver"])==0?$arr["buyer"]:$arr["receiver"];
if(isset($_REQUEST["OrderNo"]) && $_REQUEST["OrderNo"] !=""){
  $arr["OrderNo"]=$_REQUEST["OrderNo"];
}else{
  $arr["OrderNo"]=serialno_advance($paynow["serial"]);
}
$arr["reg_date"]=date('Y/m/d H:i:s');
$arr["TotalPrice"]=$OrderInfo["TotalPrice"];
$arr["CargoList"]=serialize($CargoList);
$arr["ShippingFee"]=$shippingFee;


$PassCode= sha1($paynow["WebNo"].$arr["OrderNo"].$arr["TotalPrice"].$paynow["shopkey"]);
$arr["PassCode"]=$PassCode;

insertInto($arr,["OrderNo"],$dbset['table']['orders']);

if($action=="CustomOrder"){

  $domain_setting=getSettingVal("domain")["domain"];
  //var_dump(getSettingVal("domain"));
  $domain_setting=rtrim($domain_setting, '/');
  $backurl=$domain_setting."/custom_order.php";

  $url=$domain_setting."/custom_check.php?OrderNo=".$arr["OrderNo"];
  echo "付款連結已產生，網址如下:<br>";
  echo "<a href='".$url."' target='_blank'>".$url."</a><br>";
  echo "<button class='btn' onclick='copyToClipboard(\"a\")'>複製連結</button>";
?>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script>
   function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
  }
/*  function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
  }
*/  
  </script>  
<?php
  exit();
}

?>

<?php 
if($action!="CustomCheck"){
  exit();
}
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
