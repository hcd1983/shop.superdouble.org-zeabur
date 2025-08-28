<?php
if(isset($_SERVER['HTTP_ORIGIN'])){
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
	//header('Access-Control-Allow-Origin: http://*.spinbox.cc');
	header('Access-Control-Allow-Credentials: true');

	header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

}
//fatalError=============================================================================
register_shutdown_function('fatalErrorShutdownHandler');

function myErrorHandler($code, $message, $file, $line) {
	global $payload,$stripe_setting;
	if(!isset($_REQUEST["action"]) || $_REQUEST["action"]=="create_charge"){
	
		$c_id=$payload["c_id"];
		$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
		$datas=doSQLgetRow($sql)[0];
		$arr=array(
	 		"custom_id"=>$c_id,
	 		"status"=>"failed",
	 	);
	 	insertInto($arr,["custom_id"],"stripe");

	}
  	echo "failed";
  	exit;
}

function fatalErrorShutdownHandler()
{
  $last_error = error_get_last();
  if ($last_error['type'] === E_ERROR) {
    // fatal error
    myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
  }
}
//===============================================================================================

//GET META  BY CUSTOMID===============================================================================================
function StripeGetMetaByCustomId($c_id="",$meta=""){
	//global $db_conn;
	if($c_id=="" || $meta==""){
		return false;
	}
	$sql="SELECT `value` FROM `stripe_meta` WHERE `meta` LIkE '".$meta."' AND `custom_id` LIKE '".$c_id."' LIMIT 1;";

	$result=doSQLgetRow($sql);
	if(count($result)==0){
		$value=false;
	}else{
		$value=doSQLgetRow($sql)[0]["value"];
	}
	
	return $value;	
}

function StripeGetMetaByCustomIdNoFalse($c_id="",$meta=""){
	$value=StripeGetMetaByCustomId($c_id,$meta);
	if($value===false){
		$value="";
	}
	return $value;	
}

function StripeSetMeta($c_id="",$meta="",$value=""){
	global $db_conn;

	if($c_id=="" || $meta==""){
		return ;
	}

	$value_o=StripeGetMetaByCustomId($c_id,$meta);

	if($value_o===false){
		$meta_insert=array(
		    "custom_id"=>$c_id,
		    "meta"=>$meta,
		    "value"=>$value
		);
		insertInto($meta_insert,["custom_id"],"stripe_meta");
	}else{
		$sql="UPDATE  `stripe_meta` SET `value` = '".$value."' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE '".$meta."';";
		//$sql="UPDATE  `stripe_meta` SET `value` = 'bacd' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE '".$meta."';";
		mysqli_query($db_conn, $sql); 
	}

}


//================================================================================================
if(!isset($_REQUEST["action"]) || $_REQUEST["action"]==""){
	exit;
}else{
	if(function_exists($_REQUEST["action"])){
		require_once("functions.php");
		require_once('stripe/stripe/init.php');
		$payload = file_get_contents('php://input');
		if($payload !=""){
			$payload = json_decode($payload,true);
			
		}else{
			$payload=$_REQUEST;
		}

		$_REQUEST["action"]();
		exit;
	}else{
		exit;
	}	
}


function ReadCustomStrip(){
	global $payload,$stripe_setting;
	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$payload["c_id"]."';";
	$datas=doSQLgetRow($sql)[0];

	$sql="SELECT * FROM `stripe_meta` WHERE `custom_id` LIKE '".$payload["c_id"]."';";
	$metas=doSQLgetRow($sql);
	
	$meta_val=array();
	foreach ($metas as $key => $val) {
		$meta_val[$val["meta"]]=$val["value"];
	}

	$datas["metas"]=	$meta_val;	
	$datas["token"]=$stripe_setting["token"];
	
	echo json_encode($datas);
}

function create_charge(){

	global $payload,$stripe_setting;
	$api_key=$stripe_setting["token"];
	$api_key=$stripe_setting["secret_token"];
	$c_id=$payload["c_id"];
	$email=$payload["email"];
	$name=$payload["name"];
	$token=$payload["stripeToken"];

	\Stripe\Stripe::setApiKey($api_key);
	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
	$datas=doSQLgetRow($sql)[0];
	
	if($datas["status"]=="succeeded"){
		echo "fail";
		exit;
	}
	//====================================================test
try {
    $charge = \Stripe\Charge::create([
	    'amount' => intval($datas["amount"]),
	    'currency' => 'usd',
	    'description' => $datas["memo"],
	    "metadata"=>["memo"=>$datas["admin_memo"]],
	    'source' => $token,
	    'receipt_email'=>$email
	]);

} catch(\Stripe\Error\Card $e) {
  // Since it's a decline, \Stripe\Error\Card will be caught
  $body = $e->getJsonBody();
  $err  = $body['error'];

  //print('Status is:' . $e->getHttpStatus() . "\n");
  //print('Type is:' . $err['type'] . "\n");
  //print('Code is:' . $err['code'] . "\n");
  //print('Param is:' . $err['param'] . "\n");
  //print('Message is:' . $err['message'] . "\n");
  echo $err['message'];
  exit;
} catch (\Stripe\Error\RateLimit $e) {
  // Too many requests made to the API too quickly
	exit;
} catch (\Stripe\Error\InvalidRequest $e) {
  // Invalid parameters were supplied to Stripe's API
	exit;
} catch (\Stripe\Error\Authentication $e) {
  // Authentication with Stripe's API failed
  // (maybe you changed API keys recently)
	exit;
} catch (\Stripe\Error\ApiConnection $e) {
  // Network communication with Stripe failed
} catch (\Stripe\Error\Base $e) {
  // Display a very generic error to the user, and maybe send
  // yourself an email
	exit;
} catch (Exception $e) {
  // Something else happened, completely unrelated to Stripe
	exit;
}

	//====================================================
/*
	$charge = \Stripe\Charge::create([
	    'amount' => intval($datas["amount"]),
	    'currency' => 'usd',
	    'description' => $datas["memo"],
	    "metadata"=>["memo"=>$datas["admin_memo"]],
	    'source' => $token,
	    'receipt_email'=>$email
	]);
*/	

	$json=str_replace("Stripe\Charge JSON: ", "", $charge);
 	$json=json_decode($json,true);
 	$arr=array(
 		"custom_id"=>$c_id,
 		"stripe_id"=>$json["id"],
 		"status"=>$json["status"],
 		"full_info"=>str_replace("Stripe\Charge JSON: ", "", $charge),
 	);
 	insertInto($arr,["custom_id"],"stripe");

 	//StripeSetMeta($c_id,"name",$name);
 	//StripeSetMeta($c_id,"email",$email);	
 	global $db_conn;
	$sql="UPDATE  `stripe_meta` SET `value` = '".$name."' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE 'name';";
	mysqli_query($db_conn, $sql); 
	$sql="UPDATE  `stripe_meta` SET `value` = '".$email."' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE 'email';"; 
	mysqli_query($db_conn, $sql);  	
	echo $json["status"];
	
}

function stripe_detail(){
	global $payload,$stripe_setting;
	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$payload["c_id"]."';";
	$datas=doSQLgetRow($sql)[0];
	$api_key=$stripe_setting["secret_token"];
	$charge_id=$datas["stripe_id"];

	\Stripe\Stripe::setApiKey($api_key);
	$charge = \Stripe\Charge::retrieve( $charge_id);
	$json=str_replace("Stripe\Charge JSON: ", "", $charge);	
	
	echo $json;
	//echo json_encode($datas);
}

function stripe_refund(){
	global $payload,$stripe_setting;
	$api_key=$stripe_setting["secret_token"];
	$c_id=$payload["c_id"];
	$token=$payload["stripeToken"];

	
	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
	$datas=doSQLgetRow($sql)[0];

	if($datas["stripe_id"]==""){
		exit;
	}

	$refunded=json_decode($datas["refunded"],true);

	if($refunded["status"]=="succeeded"){
		exit;
	}


	\Stripe\Stripe::setApiKey($api_key);
	//detail==============================================

	$charge_id=$datas["stripe_id"];
	
	$charge = \Stripe\Charge::retrieve( $charge_id);
	$json=str_replace("Stripe\Charge JSON: ", "", $charge);	
	$charge_detail=json_decode($json,true);
	if($charge_detail["refunded"]==true){
		$refund = $charge_detail["refunds"]["data"][0];
		
		$arr=array(
	 		"custom_id"=>$c_id,
	 		"refunded"=>json_encode($refund),
	 	);

		insertInto($arr,["custom_id"],"stripe");
	 	echo json_encode($refund);
		exit;	
	}

	//detail end===========================================

	$refund = \Stripe\Refund::create([
	    'charge' => $datas["stripe_id"],
	]);

	
	$json=str_replace("Stripe\Refund JSON: ", "", $refund);

	$arr=array(
 		"custom_id"=>$c_id,
 		"refunded"=>str_replace("Stripe\Refund JSON: ", "", $refund ),
 	);

	insertInto($arr,["custom_id"],"stripe");

	echo $json;

}


function stripe_edit(){
	global $payload,$stripe_setting;
	$sql="SELECT `custom_id`,`memo`,`amount`,`admin_memo`,`status` FROM stripe WHERE `custom_id` LIKE '".$payload["c_id"]."';";
	$datas=doSQLgetRow($sql)[0];
/*
	$sql="SELECT * FROM `stripe_meta` WHERE `custom_id` LIKE '".$payload["c_id"]."';";
	$metas=doSQLgetRow($sql);

	foreach ($metas as $key => $val) {
		$datas[$val["meta"]]=$val["value"];
	}
*/	
	$datas["name"]=StripeGetMetaByCustomIdNoFalse($payload["c_id"],"name");
	$datas["email"]=StripeGetMetaByCustomIdNoFalse($payload["c_id"],"email");	
	echo json_encode($datas);
}

function stripe_update(){
	global $payload,$stripe_setting;
	$arr=array(
 		"custom_id"=>$payload["custom_id"],
 		"amount"=>$payload["amount"]*100,
 		"memo"=>$payload["memo"],
 		"admin_memo"=>$payload["admin_memo"],
 	);
	insertInto($arr,["custom_id"],"stripe");

	$c_id=$payload["custom_id"];
	$email=$payload["email"];
	$name=$payload["name"];
/*
	global $db_conn;
	
	
	$sql="UPDATE  `stripe_meta` SET `value` = '".$name."' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE 'name';";
	mysqli_query($db_conn, $sql); 
	$sql="UPDATE  `stripe_meta` SET `value` = '".$email."' WHERE `custom_id` LIKE '".$c_id."' AND `meta` LIKE 'email';"; 
	mysqli_query($db_conn, $sql);  	
*/
	StripeSetMeta($c_id,"name",$name);
	StripeSetMeta($c_id,"email",$email);

	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
	$datas=doSQLgetRow($sql)[0];
/*	
	if(StripeGetMetaByCustomId($c_id,"name")==false){
		$meta_insert=array(
		    "custom_id"=>$c_id,
		    "meta"=>"name",
		    "value"=>$name
		);
		insertInto($meta_insert,["custom_id"],"stripe_meta");
	}

	if(StripeGetMetaByCustomId($c_id,"email")==false){
		$meta_insert=array(
		    "custom_id"=>$c_id,
		    "meta"=>"email",
		    "value"=>$email
		);
		insertInto($meta_insert,["custom_id"],"stripe_meta");
	}
*/
	$datas["name"]=StripeGetMetaByCustomId($c_id,"name");
	$datas["email"]=StripeGetMetaByCustomId($c_id,"email");

	if($datas["status"]=="succeeded"){
		if($datas["memo"]==""){
			$memo=" ";
		}else{
			$memo=$datas["memo"];
		}
		$api_key=$stripe_setting["secret_token"];
		\Stripe\Stripe::setApiKey($api_key);
		$ch = \Stripe\Charge::retrieve($datas["stripe_id"]);
		$ch->description = $memo;
		$ch->receipt_email = $datas["email"];
		$ch->metadata = ["memo"=>$datas["admin_memo"]];
		$ch->save();
	}

	$datas["memo"]=nl2br($datas["memo"]);
	$datas["admin_memo"]=nl2br($datas["admin_memo"]);
	$datas["amount"]=number_format($datas["amount"]/100,2);
	
	//echo json_encode($datas);
	$output=array();
	$output["custom_id"]=$datas["custom_id"];
	ob_start();
	trMaker($datas);
	$output["html"]=ob_get_contents();
	ob_end_clean();	
	echo json_encode($output);
	//echo trMaker($datas);
}

function return_row_by_id($c_id){
	global $payload,$stripe_setting;
	$c_id=$payload["c_id"];
	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
	$datas=doSQLgetRow($sql)[0];
	$datas["name"]=StripeGetMetaByCustomId($c_id,"name");
	$datas["email"]=StripeGetMetaByCustomId($c_id,"email");

	$output=array();
	$output["custom_id"]=$datas["custom_id"];
	ob_start();
	trMaker($datas);
	$output["html"]=ob_get_contents();
	ob_end_clean();	
	echo json_encode($output);

}

function invoice_mail($datas){
	global $stripe_setting;
	$basic_setting=getSettingVal("basic");
	
	$subject="Invoice from".$basic_setting["title"]." #".$datas["custom_id"];
	$message_top=$datas["name"]==""?"":"Dear ".$datas["name"].", \n\n";

	$theme_setting=array(
		"title"=>$subject,
		"payment_link"=>array(
		    "title"=>"Payment Link → "."$ ".number_format($datas["amount"]/100,2)." USD",
		    "url"=>$stripe_setting["url"]."?c_id=".$datas["custom_id"],
		  ),
		"message_top"=>nl2br($message_top.$datas["memo"]),
	);
	$temp=new email_tpl_creator();
	$temp->theme_setting = $theme_setting;
	ob_start();
	$temp->render();
	$message = ob_get_contents();
	ob_end_clean();
	return $message;
}

function send_stripe_message($c_id=false,$type=false){
	global $payload;
	$basic_setting=getSettingVal("basic");
	if($c_id===false){
		$c_id=$payload["c_id"];
	}

	if($type===false){
		$type=$payload["type"];
	}

	$sql="SELECT * FROM stripe WHERE `custom_id` LIKE '".$c_id."';";
	$datas=doSQLgetRow($sql)[0];

	if(count($datas)==0){
		echo "F";
		exit;
	}

	$datas["name"]=StripeGetMetaByCustomId($c_id,"name");
	$datas["email"]=StripeGetMetaByCustomId($c_id,"email");

	if($datas["email"]==""){
		echo "F";
		exit;
	}

	switch ($type) {
		case "invoice":
			$subject="Invoice from".$basic_setting["title"]." #".$datas["custom_id"];
			$message=invoice_mail($datas);
			break;
		default:
			$subject="Invoice from".$basic_setting["title"]." #".$datas["custom_id"];
			$message=invoice_mail($datas);
			break;
	}

	echo Stripe_sendmail($sentto=$datas["email"],$receiver=$datas["name"],$subject,$message,$type="test",$datas["custom_id"]);
	exit;
}


function trMaker($val){
	global $stripe_setting;
  $refunded=json_decode($val["refunded"],true);
?>
              <tr id="<?php echo $val["custom_id"];?>">

                <td class="stripe_id" style="background:<?php echo $bg=$val["stripe_id"]==""?"transparent":"aquamarine"?>;">
                  <div>
                    <?php echo $val["stripe_id"]=$val["stripe_id"]==""?"Not generated yet":$val["stripe_id"];?>
                  </div> 
                  <div>
                  <?php echo date("y-m-d", strtotime($val["time"]));?>
                  </div>
                  <input type="text" readonly="" value="<?php echo $stripe_setting["url"]."?c_id=".$val["custom_id"];?>">
                  <div onclick='$(this).prev("").select();document.execCommand("copy");' class="inline-block">
                    <a href="javascript:void(0)" class='button button-mini button-black nomargin' >Copy</a>
                  </div>
                </td>
                <td class="buyer">
                  <div><?php echo $val["name"];?></div>
                  <div><?php echo $val["email"];?></div>
                </td>
                <td class="amount">$ <?php echo number_format($val["amount"]/100,2);?><br>USD</td>
                <td class="status">
                  <?php 
                    if($refunded["status"]=="succeeded"){
                      echo "<span style='color:red'>refunded</div>";
                    }elseif($val["status"]=="succeeded"){
                      echo "<span style='color:green'>".$val["status"]."</div>";
                    }else{
                      echo "<span style='color:orange'>".$val["status"]."</div>";
                    }
                  ?>
                </td>
                <td class="memo"><?php echo nl2br($val["memo"]);?></td>
                <td class="admin_memo"><?php echo nl2br($val["admin_memo"]);?></td>
                <td class="mail_log">
                  <?php echo '<div><a href="javascript:void(0)" class="button button-primary button-small" onclick="stripe_maillog(\''.$val["custom_id"].'\')">Mail Log</a></div>';?> 
                  <?php echo '<div><a href="javascript:void(0)" class="button button-blue button-small" onclick="stripe_mail(\''.$val["custom_id"].'\')">Send Mail</a></div>';?>                  
                </td>
                <td class="center stripe_options">
                  
                  <?php 

                  //if($val["status"] != "succeeded"){
                    echo '<div><a href="javascript:void(0)" class="button button-blue button-small" onclick="stripe_edit(\''.$val["custom_id"].'\')">Edit</a></div>';
                  //}
                  
                  ?>

                  <?php 

                  if($val["status"] != ""){
                    echo '<div><a href="javascript:void(0)" class="button button-small" onclick="stripe_detail(\''.$val["custom_id"].'\')">Detail</a></div>';
                  }
                  
                  ?>

                  <?php 

                  if($val["status"] == "succeeded" && $refunded["status"] !="succeeded"){
                    echo '<div class="refund_btn"><a href="javascript:void(0)" class="button button-red button-small" onclick="stripe_refund(\''.$val["custom_id"].'\')">Refund</a></div>';
                  }
                  
                  ?>
                  
                  
                </td>
              </tr>
<?php
}

