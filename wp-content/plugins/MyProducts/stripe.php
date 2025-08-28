<?php

//主選單===========================================================
add_action('admin_menu', 'MenuForStripe');
function MenuForStripe(){
	add_menu_page( $page_title="Stripe設定", $menu_title="Stripe設定", $capability="administrator", $menu_slug="stripe_manage", $function = 'stripe_manage', $icon_url = '', $position = 24 );
}

function stripe_manage(){
	if(isset($_GET["source"])){
		$source = $_GET["source"];
	}else{
		$source = "";
	}
	switch ($source) {
		
		default:
			include_once plugin_dir_path( __FILE__ ).'stripe/option_page.php';
			break;
	}
}

add_action( 'admin_init', 'register_Stripe_setting' );
function register_Stripe_setting(){
	register_setting( 'MyStripe', 'StripePayPage' );

	register_setting( 'MyStripe', 'StripeToken' );
	register_setting( 'MyStripe', 'StripeSecretToken' );
	register_setting( 'MyStripe', 'StripeTokenTest' );
	register_setting( 'MyStripe', 'StripeSecretTokenTest' );
	register_setting( 'MyStripe', 'StripeInsertUrl' );

	register_setting( 'MyStripe', 'StripeTest' );		
}

function GetStripeApiKey(){

	if(get_option("StripeTest") == 1){
		$apikey=get_option("StripeTokenTest");
	}else{
		$apikey=get_option("StripeToken");
	}

	return $apikey;
}

function GetStripeApiSecret(){
	if(get_option("StripeTest") == 1){
		$apikey=get_option("StripeSecretTokenTest");
	}else{
		$apikey=get_option("StripeSecretToken");
	}

	return $apikey;
}




//Create Payment page===================================================================
add_action( 'wp_ajax_SetStripePayPage', 'ajax_SetStripePayPage' );
function ajax_SetStripePayPage() {
    $postID=wp_insert_post( array("post_title"=>"Stripe Payment",'post_type'=>"page","post_status"=>"publish","post_content"=>"[StripePayform]"), false );
    if($postID !=0){
      update_option( "StripePayPage", $postID );
    }
   
}

//short code================================
add_shortcode( "StripePayform" , "StripePayform" );

function StripePayform(){
	ob_start();
	include_once plugin_dir_path( __FILE__ ).'stripe/payform.php';
	$content=ob_get_contents();
	ob_end_clean();		
	return $content;
}

//================================================================================================
add_filter("PriceFormat","PriceFormatChange",10,3);
function PriceFormatChange($price,$f,$n){
	return $f.number_format($n,2);
}

//Scripts==================================================================================================
add_action('wp_enqueue_scripts', 'my_product_stripeInc_frontend');
function my_product_stripeInc_frontend(){
  
  wp_enqueue_script( $handle="stripeInc", $src =plugins_url("stripe/MyCartStripe.js",__FILE__), $deps = array("jquery","MyCart"), $ver = time(), $in_footer = false ); 

  if(get_the_ID() == get_option('StripePayPage')){
  	wp_enqueue_script( $handle="stripeApi", $src ="https://js.stripe.com/v3/", $deps = array("jquery"), $ver = time(), $in_footer = false );
  	$scriptUrl=get_home_url()."?StripeScript"; 
  	wp_enqueue_script( $handle="stripeScript", $src =$scriptUrl, $deps = array("jquery","stripeApi"), $ver = time(), $in_footer = true );
  }
}

if(isset($_GET["StripeScript"])){
	add_action("init","StripeScript");
}

function StripeScript(){
	include_once plugin_dir_path( __FILE__ ).'stripe/scripts.php';
	exit;
}

if(isset($_GET["CreateStripeCharge"])){
	add_action("init","CreateStripeCharge");
}

function CreateStripeCharge(){
	$payload = file_get_contents('php://input');
	$api_key=GetStripeApiSecret();
	//$c_id=$payload["c_id"];
	//$email=$payload["email"];
	//$name=$payload["name"];

	$payload = json_decode($payload,true);
	$token=$payload["stripeToken"];		
	extract($payload["datas"]);

	$items=str_replace('\"', '"', $OrderInfo);
    $items=json_decode($items,true);
    $itemsArr=array();
    foreach ($items as $key => $item) {
    	$itemsArr[]=$item["title"]." x ".$item["amount"];
    }

    $itemsString=join("\r\n",$itemsArr);

	
	\Stripe\Stripe::setApiKey($api_key);

	try {
	    $charge = \Stripe\Charge::create([
	    	'source' => $token,
		    'amount' => $total*100,
		    'currency' => 'usd',
		    'description' => $itemsString,
		    "metadata"=>["memo"=>$metadata["Note"],"coupon"=>$coupon],		    
		    'receipt_email'=>$email,
		    'shipping'=>[
		    	"address"=>[
		    		"line1"=>$address,
		    		"city"=>$city,
			    	"state"=>$state,
			    	"country"=>$country,
			    	"postal_code"=>$zip
		    		],
		    	"name"=>$FirstName." ".$LastName,
		    	"phone"=>$phone,

			]
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
		echo "RateLimit";
	  // Too many requests made to the API too quickly
		exit;
	} catch (\Stripe\Error\InvalidRequest $e) {
		echo "InvalidRequest";
		$body = $e->getJsonBody();
	  	$err  = $body['error'];
	  	echo $err['message'];
	  // Invalid parameters were supplied to Stripe's API
		exit;
	} catch (\Stripe\Error\Authentication $e) {
		echo "Authentication";
	  // Authentication with Stripe's API failed
	  // (maybe you changed API keys recently)
		exit;
	} catch (\Stripe\Error\ApiConnection $e) {
		echo "ApiConnection";
	  // Network communication with Stripe failed
	} catch (\Stripe\Error\Base $e) {
		echo "Base";
	  // Display a very generic error to the user, and maybe send
	  // yourself an email
		exit;
	} catch (Exception $e) {
		echo "Other";
	  // Something else happened, completely unrelated to Stripe
		exit;
	}

	$json=str_replace("Stripe\Charge JSON: ", "", $charge);
 	//$json=json_decode($json,true);
 	echo $json;
 	exit;

}

// checkoutform =================================================================================================
add_filter("checkoutFormWithValidation","StripeForm",$contents,10,1);
function StripeForm(){
	$FormWords=array();
	$FormWords["ShippingInfo"]=MyCartWords("ShippingInfo");
	$FormWords["country"]=MyCartWords("country");
	$FormWords["name"]=MyCartWords("name");
	$FormWords["phoneNumber"]=MyCartWords("phoneNumber");
	$FormWords["zip"]=MyCartWords("zip");
	$FormWords["address"]=MyCartWords("address");
	$FormWords["email"]=MyCartWords("email");
	$FormWords["taxIdNumber"]=MyCartWords("taxIdNumber");
	$FormWords["CompanyName"]=MyCartWords("CompanyName");
	$FormWords["PayType"]=MyCartWords("PayType");
	$FormWords["CreditCard"]=MyCartWords("CreditCard");
	$FormWords["ATM"]=MyCartWords("ATM");
	$FormWords["Ibon"]=MyCartWords("Ibon");
	$FormWords["note"]=MyCartWords("note");
	$FormWords["error_noproduct"]=MyCartWords("error_noproduct");
	$FormWords["error_blankfield"]=MyCartWords("error_blankfield");
	$FormWords["error_emailformat"]=MyCartWords("error_emailformat");
	$FormWords["error_taxId"]=MyCartWords("error_taxId");
	$FormWords["error_phoneNumber"]=MyCartWords("error_phoneNumber");

	$MyInsertPort=get_option('StripePayPage');
	$MyInsertPort=$MyInsertPort==""?"#":get_permalink($MyInsertPort);
	
	$pid=get_option('ProductsPages');
	$url=get_permalink($pid);
	$url=apply_filters( 'MyCartJsonUrl', $url);

	$thanks_id=get_option('MyThanksPage');
	if($thanks_id==""){
		$thanks_url="";
	}else{
		$thanks_url=get_permalink($thanks_id);
	}
	
	$thanks_url=apply_filters( 'MyThanksPage', $thanks_url);
	$image_url=plugins_url( 'stripe/images/blue.png', __FILE__ );


	$shippingOtps=GetShippingOptions();
	$shippingOtpsStyle = "";
	if(empty($shippingOtps) || count($shippingOtps)==1){
		$shippingOtpsStyle = "style='display:none;'";
	}

	ob_start();
?>
<form id="checkform" action="<?php echo $MyInsertPort;?>" method="post">

	<div class="checkform-container">
		<h3><?php echo $FormWords["ShippingInfo"];?></h3>
			<div class="col_half">
				<label for="billing-form-address"><?php echo $FormWords["country"];?>:</label>
				<?php echo MyCountrySelector();?>

				<label for="FirstName"><?php echo MyCartWords("FirstName");?>:<small>*</small></label>
				<input type="text"  name="FirstName" value="" class="sm-form-control required" />

				<label for="LastName"><?php echo MyCartWords("LastName");?>:<small>*</small></label>
				<input type="text"  name="LastName" value="" class="sm-form-control required" />

			</div>

			<div class="col_half col_last">
				<label for="city"><?php echo MyCartWords("city");?>:</label>
				<input type="text"  name="city" value="" class="sm-form-control" />
				<label for="state"><?php echo MyCartWords("state");?>:</label>
				<input type="text" name="state" value="" class="sm-form-control" />
				<label for="zip"><?php echo $FormWords["zip"];?>:<small>*</small></label>
				<input type="text" id="zip" name="zip" value="" maxlength="10"  class="sm-form-control required" />
			</div>

			<div class="clear"></div>

			<div class="col_full">
				<label for="address"><?php echo $FormWords["address"];?>:<small>*</small></label>						
				<input type="text"   name="address" value="" class="sm-form-control required" />
			</div>
			<div class="clear"></div>

			<div class="col_full">
				<label for="phone"><?php echo $FormWords["phoneNumber"];?>:<small>*</small></label>
				<input type="text"  maxlength="20" name="phone" value=""  class="sm-form-control required" />
			</div>
			<div class="clear"></div>

			<div class="col_full">
				<label for="email"><?php echo $FormWords["email"];?>:<small>*</small></label>
				<input type="email" name="email" value="" class="sm-form-control required email" />
			</div>
			<div class="clear"></div>
			<div class="col_full" <?php echo $shippingOtpsStyle;?>>
				<label for="shipping_method"><?php echo MyCartWords("ShippingOptions");;?>:<small>*</small></label>
				<?php echo MyShippingSelector();?>
			</div>
			<div class="clear"></div>
			<div class="col_full">
				<label for="Note"><?php echo $FormWords["note"];?></label>
				<textarea class="sm-form-control"  name="Note" rows="6" cols="30"></textarea>
			</div>

			<div class="clear"></div>
			<div class="col_full">
				<label for="Note1"><?php echo MyCartWords("PayType");?></label>
				<div><img src="<?php echo $image_url;?>" width=150></div>
			</div>

		<!--</form>-->
	</div>
	
	<input type="hidden" id="countryname" name="countryname" value=""  />
	<input type="hidden" id="return_url" name="return_url" value="<?php echo $thanks_url;?>"  />
	<input type="hidden" id="OrderInfo" name="OrderInfo" value=""  />
	<input type="hidden" id="shippingFee" name="shippingFee" value=""  />
	<input type="hidden" id="coupon" name="coupon" value=""  />
	<input type="hidden" id="discount" name="discount" value=""  />
	<input type="hidden" id="prdouct_json_url" name="prdouct_json_url" value="<?php echo $url;?>">
</form>	
<script>
	$=jQuery;
	$("#checkform").on("submit", function(e){
		e.preventDefault();
     	e.returnValue = false;
     	
		MyCart.AjaxCheckStore(items,CheckFormValidation);

	})



	function CheckFormValidation(){
		
		var total_amount=0;
		var order = items.map(function(item){
				var _item = {
					id: item.id,
					title:item.title,
					price:item.price,
					amount: item.amount
				};
				total_amount+= item.amount;
				return _item;
		})

		if( total_amount ==0 ){
			alert("<?php echo $FormWords["error_noproduct"];?>");
			return;
		}
		
		no_fill=0;

		$("#checkform .required").each(function(i){

			the_val=$.trim($(this).val())

			if(  the_val.length == 0 ){
				
				$(this).css("border","2px solid red");

				no_fill++;

			}else{
				$(this).removeAttr("style");
			}

		})


		if(no_fill !=0 ){
				alert("<?php echo $FormWords["error_blankfield"];?>");
				return false;
		}

		

		wrong=0;
		$("#check .required.email").each(function(i){

			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if (!testEmail.test($(this).val())){
				$(this).css("border","2px solid red");
				wrong++
				
			}else{
				$(this).removeAttr("style");
			}
		    
		})

		if(wrong !=0 ){
				alert("<?php echo $FormWords["error_emailformat"];?>");
				return false;
		}


		wrong=0;
		$("#check .required.tel").each(function(i){

			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if (isNaN($(this).val())){
				$(this).css("border","2px solid red");
				wrong++
				
			}else{
				$(this).removeAttr("style");
			}
		    
		})

		if(wrong !=0 ){
				alert("<?php echo $FormWords["error_phoneNumber"];?>");
				return false;
		}
/*
		var receipt=$("#receipt").val();
		console.log(receipt.length);

		if(receipt.length==0 || receipt.length==8){
			$("#receipt").removeAttr("style");
		}else{
			alert("<?php echo $FormWords["error_taxId"];?>");
			$("#receipt").css("border","2px solid red");
			return false;				
		}
*/
		var countryname=$( "#the_country option:selected" ).text();

		$("#countryname").val(countryname);
		$('#OrderInfo').val(JSON.stringify(order));
		$('#shippingFee').val(shippingfee);
		$('#discount').val(discount);
		MyCart.FBpixel("AddPaymentInfo",items);
		$("#checkform").off("submit");
		$("#checkform").submit();
		//return false;
	}

</script>	
<?php		    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}