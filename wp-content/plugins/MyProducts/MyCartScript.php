<?php

//funding cookies ===========================================================================
add_action("init","the_super_quick_check");

function the_super_quick_check(){
	if(isset($_GET["quick_check"]) && $_GET["quick_check"] != ""){

		$the_domain = explode(".", $_SERVER['SERVER_NAME']);

		$the_domain_length = count($the_domain);

		$fixed_domain = ".".$the_domain[$the_domain_length-2].".".$the_domain[$the_domain_length-1];
		
		unset($_COOKIE["gifts"]);
		$res = setrawcookie("gifts", '', time() - 3600,"/");

		$_items = [];
		$item_id = $_GET["quick_check"];
		$item_qty = 1;
		$p_id = $item_id;
		$ProductsInfo=get_post_meta($p_id,"ProductsInfo",true);
		$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$p_id);
		$the_price=$ProductsInfo["price"];
		if($ProductsInfo["onsale"] == 1 ){
	  		$the_price=$ProductsInfo["saleprice"];
		}

		$amount = apply_filters("the_super_quick_check_qty",$item_qty,$item_id);

		$imgUrl = get_the_post_thumbnail_url( $p_id ,  'myproduct' );
		$price = $the_price;
		$title = get_the_title($p_id);
		$_items[] = array(
			
			"id" => $p_id,
			"amount"=> $amount,
			"imageUrl"=> $imgUrl,
			"price"=> $price,
			"title"=>urldecode($title),

		);

		$_items = apply_filters("the_super_quick_check_items",$_items,$p_id);

		$_items_json = json_encode($_items);
		//$_COOKIE["cart"] = $_items_json;
		//$_items_json= str_replace('"', '\"', $_items_json);
		setrawcookie("cart",rawurlencode($_items_json), time()+3600*24);
	
	}else{
		unset($_COOKIE["cart"]);
		$res = setrawcookie("cart", '', time() - 3600);
	}
}

add_action("init","the_fund_cookie");
function the_fund_cookie(){

	if(isset($_POST["funding_check"])){

		$item_ids = $_POST["item_id"];
		$item_qty = $_POST["item_qty"];
		$_items = [];
		if(is_array($item_ids) && count($item_ids) > 0){

			foreach ($item_ids as $key => $p_id) {

				$ProductsInfo=get_post_meta($p_id,"ProductsInfo",true);

				$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$p_id);

				$the_price=$ProductsInfo["price"];
				
				if($ProductsInfo["onsale"] == 1 ){
			  		$the_price=$ProductsInfo["saleprice"];
				}

				$amount = $item_qty[$key];

				if($amount == 0){
					continue;
				}
				$imgUrl = get_the_post_thumbnail_url( $p_id ,  'myproduct' );
				$price = $the_price;
				$title = get_the_title($p_id);
				$_items[] = array(
					
					"id" => $p_id,
					"amount"=> $amount,
					"imageUrl"=> $imgUrl,
					"price"=> $price,
					"title"=>urldecode($title),

				);
			}

			$_items = apply_filters("the_fund_cookie",$_items);
			$_items_json = json_encode($_items);
			//$_COOKIE["cart"] = $_items_json;
			//$_items_json= str_replace('"', '\"', $_items_json);
			setrawcookie("cart",rawurlencode($_items_json), time()+3600*24);
			//var_dump($_COOKIE["cart"]);
			
		}


	}

}

add_action("init","the_custom_payment");
function the_custom_payment(){

	if(isset($_GET["custom_payment"])){

		$_items = [];
		$_items = apply_filters("the_custom_payment",$_items);
		$_items_json = json_encode($_items);
		//$_COOKIE["cart"] = $_items_json;
		//$_items_json= str_replace('"', '\"', $_items_json);
		setrawcookie("cart",rawurlencode($_items_json), time()+3600*24);
		//var_dump($_COOKIE["cart"]);
	}

}






//footer actions ==================================================================================================
add_action( 'wp_footer', 'MyCartRender' );
function MyCartRender() {
	global $CartScriptFooter;
	
	$fn=$CartScriptFooter["normal"];
	$actName="normal";
	
	if( get_the_id()== get_option('MyCartPage')){
		$fn=$CartScriptFooter["cart"];
		$actName="cart";
	}

	if(get_the_id() == get_option('StripePayPage')){
		$fn=$CartScriptFooter["StripePay"];
		$actName="StripePay";
	}

	if(get_the_id()== get_option('MyFundingCartPage') ){
		$fn=$CartScriptFooter["FundingCart"];
		$actName="FundingCart";
	}

	if(get_the_id()== get_option('MyCheckPage') ){
		$fn=$CartScriptFooter["check"];
		$actName="check";
	}

	if(get_the_id()== get_option('MyThanksPage') ){
		$fn=$CartScriptFooter["thanks"];
		$actName="thanks";
	}

	$fn = apply_filters("mycart_footer_fn",$fn);

	if( is_callable( $fn ) ){
		$fn();
	}
	
	do_action($actName."_callback");	
}

$CartScriptFooter=array();
$CartScriptFooter["json_url"]=function(){
	if(get_option('ProductsPages') ==""){
		return "";
	}
	$pid=get_option('ProductsPages');
	$url=get_permalink($pid);
	$url=apply_filters( 'MyCartJsonUrl', $url);
	return $url;
};
$CartScriptFooter["normal"]=function(){
	global $CartScriptFooter,$my_cart_top_menu_mobile,$MyProductsSetting;
	$fn=$CartScriptFooter["json_url"];
	$url=$fn();
	if(wp_is_mobile()==true){
		
		$CartPage=get_option('MyCartPage') ;
  		$CartPage=$CartPage==""?"#":get_permalink($CartPage);
  		$cart_mobile=apply_filters("my_cart_top_menu_mobile",$my_cart_top_menu_mobile);
  		$check_now_btn_str = apply_filters("mobile_checknow_btn","Check Now");
?>

	<div>
		<div class="main_color" >
			<a  id="fixed_cart_bottom" class="button" href="<?php echo $CartPage;?>">
				<div class="check_now"><?php echo $check_now_btn_str;?></div>
				<i class="fas fa-credit-card" style="display: none;"></i>
				<span class="total-amount">0</span>
			</a>
		</div>
	</div>		

	<script>

		$=jQuery;
		$("header#header").append('<div class="top-cart-continer">\
    <a class="<?php echo join(" ",$cart_mobile->classes);?>"><?php echo $cart_mobile->title;?></a>\
  </div>');

		$("header#header").append('<div id="top-cart" class="top-cart-content">\
      <div class="top-cart-title">\
        <h4><?php echo MyCartWords("cart");?></h4>\
      </div>\
      <div class="top-cart-items" style="max-height: 65vh; overflow-y: auto;">\
        <!--item from cart-->\
      </div>\
      <div class="top-cart-action clearfix">\
        <span class="fleft top-checkout-price">$<span class="total-bill">0</span></span>\
        <a id="goCheck" href="<?php echo $CartPage; ?>" class="fright"><div class="button button-3d button-small nomargin fright"><?php echo MyCartWords("gotocheck");?></div></a>\
      </div>\
    </div>');
	</script>	
<?php		
	}
?>
	<script>
		MyCart.usecate=<?php echo $MyProductsSetting["UseProductCate"];?>;		
		MyCart.setting.json_url="<?php echo $url;?>";
		MyCart.setting.moneylogo="<?php echo GetTheMoneyLogo();?>";


		if(MyCart.setting.json_url !=""){
			$=jQuery;
			MyCart.Start_My_Cart();
			MyCart.GetProductsList(function(){ MyCart.renderCart(); });
			$(".top-cart-content").hide();
			

			/*
			if(!MyCart.GetCartItems().length){
				$(".top-cart-content").hide();
			}
			*/
		}		
	</script>	
<?php
	if(is_single() && get_post_type()=="myproducts"){
		$id=get_the_ID();
?>
	<script>
	$=jQuery;	
	$(window).load(function(){

		
	  if(typeof(fbq)=="function" ){
		fbq('track', 'ViewContent', {
	    	content_ids: ['<?php echo $id;?>'],
	    	content_type:MyPixelSetting.content_type
	  	});
      }
	}) 
	</script>	
<?php
	}	
};

$CartScriptFooter["cart"]=function(){
	global $CartScriptFooter,$MyProductsSetting;
	$fn=$CartScriptFooter["json_url"];
	$url=$fn();

	$CheckPage=get_option('MyCheckPage') ;
  	$CheckPage=$CheckPage==""?"#":get_permalink($CheckPage);

	if(wp_is_mobile()==true){
		$is_mobile=1;
	}else{
		$is_mobile=0;
	}

	//$items=str_replace('\"', '"', $_COOKIE["cart"]);	
	//$items=json_decode($items,true);
	//$shippingfee=MyShippingfee($items);
	//$discount=MyDiscount($items);
	//$discount=0;
?>
	<div id="country_opt_temp" style="display: none;">
		<?php echo MyCountrySelector();?>
	</div>
	<div id="shipping_opt_temp" style="display: none;">
		<?php echo MyShippingSelector();?>
	</div>	
	<script>
		MyCart.usecate=<?php echo $MyProductsSetting["UseProductCate"];?>;
		MyCart.setting.moneylogo="<?php echo GetTheMoneyLogo();?>";
		$=jQuery;
		var shippingfee;
		var discount;
		var items;
		var country="";
		var coupon="";
		var shipping_method="";
		is_mobile=<?php echo $is_mobile; ?>	
		MyCart.setting.json_url="<?php echo $url;?>";
		MyCart.ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		
		MyCart.CountrySelector=$("#country_opt_temp").html();
		MyCart.ShippingSelector=$("#shipping_opt_temp").html();
		
		MyCart.AjaxEverythingBefore=function(){
			$(".amount_shipping-bill").html('<i class="fas fa-spinner fa-spin"></i>');
			$(".amount_product").html('<i class="fas fa-spinner fa-spin"></i>');
		}
		$("#country_opt_temp").remove();
		$("#shipping_opt_temp").remove();

		if(MyCart.setting.json_url !=""){
			$=jQuery;
			items=MyCart.GetCartItems();
			
			var ids=items.map(function(item){
				return item.id;
			})
			$args={
			    'post__in':ids,        
			};

			MyCart.SetCartPage(is_mobile,"<?php echo $CheckPage;?>"); 

			MyCart.AjaxEverythingCallback=function(data){
				console.log(data);
				items=data["items"];
				shippingfee=data.shippingfee;
				discount=data.discount;
				MyCart.RenderCartList(data["items"],data.shippingfee,data.discount);
				//delete MyCart.AjaxEverythingCallback;
			}

			$("#the_country,#shipping_method").on("change",function(){
				country=$("#the_country").val();
				shipping_method=$("#shipping_method").val();
				MyCart.SetCountry($("#the_country").val());
				MyCart.SetShippingMethod($("#shipping_method").val());
				MyCart.AjaxEverything(items,country,coupon,shipping_method);		
			});

			
/*
			MyCart.AjaxGetProductsList($args,function(){
				items=MyCart.PriceFixer(items,productsList);
				if($("#the_country").length > 0){					
					MyCart.ResetCountryOpts(function(){
						$("#the_country").change();
					});							
				}else{
					MyCart.AjaxEverything(items,country,coupon);
				}

				//MyCart.AjaxEverything(items,country,coupon="");
		
			});
*/
			MyCart.GetProductsList(function(){
				items=MyCart.PriceFixer(items,productsList);
				if($("#the_country").length > 0 || $("#shipping_method").length){					
					MyCart.ResetAllOptions(function(){
						$("#the_country,#shipping_method").eq(0).change();
					});							
				}else{
					MyCart.AjaxEverything(items,country,coupon,shipping_method);
				}

				//MyCart.AjaxEverything(items,country,coupon="");
		
			});

			
		}

		
		
	</script>	
<?php	
};


$CartScriptFooter["check"]=function(){
	global $CartScriptFooter,$MyProductsSetting;
	$fn=$CartScriptFooter["json_url"];
	$url=$fn();

	if(wp_is_mobile()==true){
		$is_mobile=1;
	}else{
		$is_mobile=0;
	}
	//$shippingfee=0;
	$items=str_replace('\"', '"', $_COOKIE["cart"]);	
	$items=json_decode($items,true);
	$shippingfee=MyShippingfee($items,$coupon="");
	$discount=MyDiscount($items);
	if(!empty(get_option('MyCartPage'))){
		$shop_link=get_permalink( get_option('MyCartPage'));
	}else{
		$shop_link="#";
	}

		
?>
	<script>
		MyCart.usecate=<?php echo $MyProductsSetting["UseProductCate"];?>;
		MyCart.setting.moneylogo="<?php echo GetTheMoneyLogo();?>";
		is_mobile=<?php echo $is_mobile; ?>	
		MyCart.setting.json_url="<?php echo $url;?>";
		MyCart.ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		MyCart.setting.shop_url="<?php echo $shop_link;?>";
		if(MyCart.setting.json_url !=""){
			$=jQuery;

			items=MyCart.GetCartItems();

			var ids=items.map(function(item){
				return item.id;
			})

			$args={
			        'post__in':ids,        
			};

			shippingfee=<?php echo $shippingfee;?>; 
			discount=<?php echo $discount;?>;   
			
			MyCart.SetCheckPage(is_mobile);    
			
			MyCart.AjaxEverythingCallback=function(data){
				items=data["items"];
				shippingfee=data.shippingfee;
				discount=data.discount;
				MyCart.RenderCheckList(data["items"],data.shippingfee,data.discount);
				//delete MyCart.AjaxEverythingCallback;
			}

			$("#the_country,#shipping_method").on("change",function(){
				var country=$("#the_country").val();
				var shipping_method=$("#shipping_method").val();
				var coupon=$("#coupon").val();
				var origin_items=MyCart.GetCartItems();
				origin_items=MyCart.PriceFixer(origin_items,productsList);
				MyCart.SetCountry($("#the_country").val());
				MyCart.SetShippingMethod($("#shipping_method").val());
				MyCart.AjaxEverything(origin_items,country,coupon,shipping_method);		
			});

			

			//MyCart.AjaxGetProductsList($args,readCart);
			MyCart.AjaxGetProductsList($args,function(){

				var coupon=$("#coupon").val();
				var origin_items=MyCart.GetCartItems();
				origin_items=MyCart.PriceFixer(origin_items,productsList);
				
				if($("#the_country").length > 0 || $("#shipping_method").length){					
					MyCart.ResetAllOptions(function(){
						$("#the_country,#shipping_method").eq(0).change();
					});							
				}else{

					MyCart.AjaxEverything(origin_items,country="",coupon,shipping_method="");
				}
			});

			items = MyCart.PriceFixer(items,productsList);

			MyCart.FBpixel("InitiateCheckout",items);
		}		
	</script>	
<?php	
};




$CartScriptFooter["thanks"]=function(){
	global $CartScriptFooter,$MyProductsSetting;
	$fn=$CartScriptFooter["json_url"];
	$url=$fn();

	$apiurl=get_option('MyOrderApi');
	$apiurl=apply_filters( 'MyOrderApiUrl', $apiurl);

	$curl = curl_init(); //开启curl
	$apiurl.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $apiurl); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	$pixelcode="";
	if(isset($info["status"])){
		if($info["status"]["PayType"]=="01" && $info["status"]["TranStatus"]!="S"){
			$pixelcode="";
		}else{
			$pixelcode='MyCart.FBpixel("Purchase",items);';
		}
	};
?>
	<script>
		MyCart.usecate=<?php echo $MyProductsSetting["UseProductCate"];?>;
		MyCart.setting.moneylogo="<?php echo GetTheMoneyLogo();?>";
		MyCart.setting.json_url="<?php echo $url;?>";
		if(MyCart.setting.json_url !=""){
			$=jQuery;
			MyCart.Start_My_Cart();
			MyCart.GetProductsList();
			//MyCart.renderCart();
			$(".top-cart-content").hide();
		}
		items=MyCart.GetCartItems();
		console.log(items);
		<?php echo $pixelcode;?>
		MyCart.clear();
		//MyCart.renderCart();

		

	</script>	
<?php	
};

//FundingCart ===============================================================

$CartScriptFooter["FundingCart"]=function(){
	global $CartScriptFooter,$MyProductsSetting;
	$fn=$CartScriptFooter["json_url"];
	$url=$fn();

	
	if(wp_is_mobile()==true){
		$is_mobile=1;
	}else{
		$is_mobile=0;
	}


?>
	<div id="country_opt_temp" style="display: none;">
		<?php echo MyCountrySelector();?>
	</div>
	<div id="shipping_opt_temp" style="display: none;">
		<?php echo MyShippingSelector();?>
	</div>		
	<script>
		MyCart.usecate=<?php echo $MyProductsSetting["UseProductCate"];?>;
		MyCart.setting.moneylogo="<?php echo GetTheMoneyLogo();?>";
		$=jQuery;
		var shippingfee;
		var discount;
		var items;
		var country="";
		var shipping_method="";
		var coupon="";
		var totalBill = 0;
		is_mobile=<?php echo $is_mobile; ?>	
		MyCart.setting.json_url="<?php echo $url;?>";
		MyCart.ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		
		MyCart.CountrySelector=$("#country_opt_temp").html();
		MyCart.ShippingSelector=$("#shipping_opt_temp").html();

		

		MyCart.AjaxEverythingBefore=function(){
			$(".amount_shipping-bill").html('<i class="fas fa-spinner fa-spin"></i>');
			$(".amount_product").html('<i class="fas fa-spinner fa-spin"></i>');
		}

		$("#country_opt_temp").remove();
		$("#shipping_opt_temp").remove();

		$(".the_country_selector").html(MyCart.CountrySelector);
		$(".the_shipping_selector").html(MyCart.ShippingSelector);

		function SetItems(){

			if(typeof BeforeSetItems == "function"){
				BeforeSetItems();
			}

			items = [];
			totalBill = 0;
			$("input.qty").each(function(i){
				var value = $(this).val();
				var id = $(this).data("id");
				var singleline = $(this).closest(".item-single-line");
				if(value == 0){
					singleline.find(".col-total").html(MyCart.CartMoneyFormat(0));
					return;
				}else{

					var itemData = MyCart.GetProductDataById(id);
					var AddtoItems = {
						amount: value,
						id: itemData.id,
						imageUrl: itemData.imageUrl,
						price: itemData.price,
						title: itemData["title"]
					}
					
					items.push(AddtoItems);

					var subtotal = Number(value)*Number(itemData.price);

					singleline.find(".col-total").html(MyCart.CartMoneyFormat(subtotal));
					totalBill += subtotal;
				}

			});

			if(totalBill == 0){
				shippingfee = 0;
				discount = 0;
				$('.amount_product').html(MyCart.CartMoneyFormat(totalBill));
				$('.amount_shipping-bill').html(MyCart.CartMoneyFormat(shippingfee));
    			$('.amount_discount-bill').html(MyCart.CartMoneyFormat(discount));
			}else{
				MyCart.AjaxEverything(items,country,coupon,shipping_method);
			}
			
		}

		if(MyCart.setting.json_url !=""){
			
			$=jQuery;

			
			MyCart.AjaxEverythingCallback=function(data){

				items=data["items"];
				shippingfee=data.shippingfee;
				discount=data.discount;
				$('.amount_product').html(MyCart.CartMoneyFormat(totalBill));
				$('.amount_shipping-bill').html(MyCart.CartMoneyFormat(shippingfee));
    			$('.amount_discount-bill').html(MyCart.CartMoneyFormat(discount));
			}

			$("input.qty").on("change",function(){
				var oldvalue = $(this).data('origin');
				var value =  $(this).val();
		    	var reg = /^\d+$/;
		    	if(reg.test(value)===false){
		    		alert("請輸入正確數字");
		    		$(this).val(oldvalue);
		    	}

		    	SetItems();	
			});

			$('input.minus').click(function(){
				var singleline = $(this).closest(".item-single-line");
				var qty_input = singleline.find("input.qty");
				var qty = qty_input.val();
				if(qty == 0){
					return;
				}
				qty --;
				qty_input.val(qty);
				SetItems();
				
		    })


		    $('input.plus').click(function(){
		    	var singleline = $(this).closest(".item-single-line");
				var qty_input = singleline.find("input.qty");
				var qty = qty_input.val();
				qty ++;
				qty_input.val(qty);
				SetItems();

		    })


			$("#the_country,#shipping_method").on("change",function(){
				country=$("#the_country").val();
				shipping_method=$("#shipping_method").val();
				MyCart.SetCountry($("#the_country").val());
				MyCart.SetShippingMethod($("#shipping_method").val());
				SetItems();
			});

			MyCart.GetProductsList(function(){
				SetItems();
				if($("#the_country").length > 0 || $("#shipping_method").length){					
					MyCart.ResetAllOptions(function(){
						$("#the_country,#shipping_method").eq(0).change();
					});							
				}else{
					SetItems();
				}
				
		
			});

			
		}

		
		
	</script>	
<?php	
};



