<?php
if(!isset($_REQUEST["action"]) || $_REQUEST["action"]!="CustomStripeCharge"){
  exit;
}else{  
    require_once("functions.php");
    require_once('stripe/stripe/init.php');
    $payload = file_get_contents('php://input');
    if($payload !=""){
      $payload = json_decode($payload,true);      
    }else{
      $payload=$_REQUEST;
    }

}



function random_num($random=5){
  //$random預設為10，更改此數值可以改變亂數的位數----(程式範例-PHP教學)
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
//序號產生器_亂數
function stripe_custom_id(){  
  global $db_conn,$dbset;
  $final="";      
  $random_id=random_num(16);
  $number=date('md').$random_id;
  //echo $ser;
  if ($db_conn) {   

    $sql="SELECT `custom_id` FROM `stripe` WHERE `custom_id` LIKE '%".$number."%' ORDER BY id DESC LIMIT 1";
    //$serleng=strlen($ser);
    $result = mysqli_query($db_conn, $sql);  
    
    if (mysqli_num_rows($result) > 0) {     
          stripe_custom_id();
        }else{
          $final= $number;
          return $final;
        } 
  }
    

}

date_default_timezone_set('Asia/Taipei');


$stripe=$payload["stripe"];

$stripe["amount"]=$stripe["amount"]==""?0.5:$stripe["amount"];
$stripe["amount"]=$stripe["amount"]*100;
$stripe["time"]=date('Y/m/d H:i:s');
$stripe["custom_id"]=stripe_custom_id();
$stripe["mode"]=$stripe_setting["mode"];

  insertInto($stripe,["custom_id"],"stripe");

$meta=$payload["meta"];
foreach ($meta as $key => $val) {
  $meta_insert=array(
    "custom_id"=>$stripe["custom_id"],
    "meta"=>$key,
    "value"=>$val
  );
  insertInto( $meta_insert,["custom_id"],"stripe_meta");
}

  
  if(isset($stripe_setting["url"])){
    $domain_setting=$stripe_setting["url"];
  }else{
    $domain_setting="";
  }

  $backurl=$domain_setting."/custom_order.php";

  $url=$domain_setting."?c_id=".$stripe["custom_id"];

  $output=array(
    "custom_id"=>$stripe["custom_id"],
    "url"=>$url,
    "time"=>$stripe["time"]
  );

  echo json_encode($output);
  exit;
//縮網址=================================================================

//縮網址 END================================================================= 

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

  </script>  
<?php
  exit();