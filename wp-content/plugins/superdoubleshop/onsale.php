<?php

function OnSaleActive(){
	date_default_timezone_set("Asia/Taipei");
	$user = wp_get_current_user();
	$allowed_roles = array('editor', 'administrator', 'author');

	if( array_intersect($allowed_roles, $user->roles ) ) {
		// return "newyear";
	}


	$now = time();
	
	$start_time = "2020/01/07 11:00:00";
	$end_time = "2020/01/20 11:00:00";

	$start_time_stemp = strtotime($start_time);
	$end_time_stemp = strtotime($end_time);

	if($now >= $start_time_stemp && $now < $end_time_stemp){
		return "newyear";
	}

	$start_time = "2019/01/01 00:00:00";
	$end_time = "2019/01/03 23:59:59";

	$start_time_stemp = strtotime($start_time);
	$end_time_stemp = strtotime($end_time);

	if($now >= $start_time_stemp && $now < $end_time_stemp){
		return "8888";
	}

	$start_time = "2019/03/01 00:00:00";
	$end_time = "2019/03/10 23:59:59";

	$start_time_stemp = strtotime($start_time);
	$end_time_stemp = strtotime($end_time);

	if($now >= $start_time_stemp && $now < $end_time_stemp){
		return "38buy";
	}


	

	return "";
	

}

// add_filter("GetProductInfo","go_38buy",10,2);
function go_38buy($ProductsInfo,$post_id){

	if(OnSaleActive() == "38buy"){
		$sale_items = [1173,1175,1177];
		if(in_array($post_id, $sale_items)){
			$ProductsInfo["onsale"] = 1;
		}
	}
	

	return $ProductsInfo;
}

add_filter("GetProductInfo","newyear_sale",10,2);
function newyear_sale($ProductsInfo,$post_id){
	
	if(OnSaleActive() != "newyear"){
		return $ProductsInfo;
	}

	if(is_admin()){ return $ProductsInfo; }

	$title = get_the_title($post_id);

	

	if( strpos( $title , "快卡背包") !== false && strpos( $title , "終極黑") === false ){
		$ProductsInfo["onsale"] = 1;
		$ProductsInfo["saleprice"] = 4880;
	}

	return $ProductsInfo;
}

// add_filter('the_title','my_test_title',10,2);
// function my_test_title(  $title, $id ){
	
// 	if(OnSaleActive() != "newyear"){
// 		return $title;
// 	}

// 	if(is_admin()){ return $title; }

// 	$_title = $title;
// 	if( strpos( $_title , "快卡背包") >= 0  ){
// 		$title.= "__".strpos( $_title , "快卡背包");
// 	}

// 	return $title;
// };


add_shortcode("onsalseMsg","onsalseMsg");
function onsalseMsg(){
	
	$sale = OnSaleActive();

	switch ($sale) {
		case "newyear":
			$video = "https://shop.superdouble.org/wp-content/uploads/2020/01/ezgif.com-gif-to-mp4-1.mp4";
			$output = '<video  preload="meta" loop playsinline autoplay muted style="width:100%;" >
            			<source src="'.$video.'" type="video/mp4">
        				</video>';
			break;

		case "gift_box":
			$image_url_mobile = "https://shop.superdouble.org/wp-content/uploads/2019/08/67938804_710625246017655_4187187544133206016_n.jpg";
			$image_url = "https://shop.superdouble.org/wp-content/uploads/2019/08/67938804_710625246017655_4187187544133206016_n.jpg";
			$output = do_shortcode("[av_image src='{$image_url_mobile}' attachment='1060' attachment_size='large' align='center' styling='' hover='' link='manually,#Sale' target='' caption='' font_size='' appearance='' overlay_opacity='0.4' overlay_color='#000000' overlay_text_color='#ffffff' copyright='' animation='no-animation' av-small-hide='aviaTBav-small-hide' av-mini-hide='aviaTBav-mini-hide' av_uid='' admin_preview_bg=''][/av_image][av_image src='{$image_url}' attachment='1049' attachment_size='myproduct' align='center' styling='' hover='' link='manually,#Sale' target='' caption='' font_size='' appearance='' overlay_opacity='0.4' overlay_color='#000000' overlay_text_color='#ffffff' copyright='' animation='no-animation' av-desktop-hide='aviaTBav-desktop-hide' av-medium-hide='aviaTBav-medium-hide' av_uid='' admin_preview_bg=''][/av_image]");
			$output = "<a href='#Sale'><div style='text-align:center;'><img src='{$image_url}'></div></a>";
			break;
		case "8888":
			$output = "<div><img src='https://shop.superdouble.org/wp-content/uploads/2018/12/49209673_375393699889162_8895973156691378176_n.jpg' style='margin:auto;height:atuo;max-width:100%;display:block;'></div>";	
			break;		
		default:
			return;
			break;
	}

	
	return $output;
}


add_filter("ajax_Everything","superdouble_sale",20,6);
function superdouble_sale($output,$items,$shippingfee,$coupon,$discount,$shipping_method){

	$sale = OnSaleActive();

	switch ($sale) {
		case '8888':
			
			return the_8888_sale($output,$items,$shippingfee,$coupon,$discount,$shipping_method);
			// has_term( "spinbox","product_cate", $post_id );

			break;
		
		default:
			return $output;
			break;
	}



	

	return $output;
}

function the_8888_sale($output,$items,$shippingfee,$coupon,$discount,$shipping_method){
	
	$main_count = 0;

	foreach ($items as $key => $item) {
		if(has_term( "main","product_cate", $item["id"] )){
			$main_count += $item["amount"];
		}
	}

	$discountNum = floor($main_count/2); 
	$discount = 2472;
	$output["discount"] = $discountNum * $discount; 
	return $output;
}

// add_action("cart_callback","CartGift20181125");
function CartGift20181125(){
	
	$sale = OnSaleActive();

	if($sale != "newyear"){
		return;
	}

	$args = array(
		'post_type' => 'myproducts',
		'fields' => 'ids',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cate',
				'field' => 'slug',
				'terms' => 'main'
			)
		)
	);

    $main_products = get_posts( $args );

	$args = array(
		'post_type' => 'myproducts',
		'fields' => 'ids',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'product_cate',
				'field' => 'slug',
				'terms' => 'gift'
			)
		)
	);

    $posts = get_posts( $args );


?>	
<style type="text/css">
#gift-list.cart.item-list .cell.col-num input[type=text] {
    max-width: 70px;
    display: inline-block;
    margin-bottom: 0;
    vertical-align: middle;
    text-align: center;
}

div#gift-list {
    padding-top: 10px;
}
</style>
<script type="text/javascript">
	Array.prototype.diff = function(arr2) {
	    var ret = [];
	    this.sort();
	    arr2.sort();
	    for(var i = 0; i < this.length; i += 1) {
	        if(arr2.indexOf(this[i]) > -1){
	            ret.push(this[i]);
	        }
	    }
	    return ret;
	};
	//var gifts = [974,977];
	var main_products = <?php echo json_encode($main_products);?>

	var gifts = <?php echo json_encode($posts);?>

	MyCart.GetProductsList(function(){ 

		SetGift();
		/*MyCart.addToCartById(974);
		MyCart.AjaxEverything(items,country,coupon);*/	
	});
	
	

	function SetGift(){
		if(gifts.length==0){
			return false;
		}

		var item_ids=[];
		
		for (var i = 0; i < items.length; i++) {
			item_ids.push(Number(items[i].id));
		}

		if(main_products.diff(item_ids).length == 0){
			return false;
		};

		//$(".cart-list-container").prepend("<h3 style='text-align:center;color:red;'>贈品活動優惠期間，不得搭配 Coupon 使用。</h3>");
		$("#cart-list").after("<div class='gift-list-container'></div>");
		$(".gift-list-container").before("<h3 id='gifts-title' style='text-align:center'>贈品選擇</h3>");
		$("h3#gifts-title").after("<h4 style='text-align:center' id='gifts-msg'></h4>");
		

		var html;
		if(is_mobile==0){
		html='<div id="gift-list" class="gift item-list cart">'+
					'<div class="list-head">'+
						'<div class="cell col-img">&nbsp;</div>'+
				        '<div class="cell col-name">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-num">'+"數量"+'</div>'+
				    '</div>'+
				    '<div class="list-body"></div>'+    
				'</div>';
		}else{
			html='<div id="gift-list" class="gift item-list cart">'+
					'<div class="list-head">'+
				        '<div class="cell col-name">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-num">&nbsp;</div>'+
				    '</div>'+
					'<div class="list-body"></div>'+
					'</div>';
		}


		$('.gift-list-container').html(html);

		RenderGiftsOpts();
		ResetCheckBtn();
		RenderGiftMsgUseData();

		$(document).on("change","input.qty",function(){
			RenderGiftMsgUseData();
		})

		$(document).on("click","input.minus, input.plus",function(){
			RenderGiftMsgUseData();
		})

		$(document).on("click",".remove",function(){
			RenderGiftMsgUseData();
		})

		

	}

	function RenderGiftMsg(msg){

		$("#gifts-msg").html(msg);
	
	}

	function RenderGiftMsgUseData(){
                      
		var giftsData = GiftQtyCheck();
		var MaxGiftsAmount = GetMaxGiftAmount();

		if(gifts.length == 1){
			$(".gift-list-container input.qty").val(MaxGiftsAmount);
			$(".gift-list-container input.qty").attr("readonly","readonly");
			$(".gift-list-container input.minus,.gift-list-container input.plus").hide();
			giftsData.qty = MaxGiftsAmount;
		}

		//var msg = "可分配贈品數量: " + giftsData.qty + " / " + MaxGiftsAmount; 
		var msg =  "可分配贈品數量: "  + MaxGiftsAmount; 
		    msg += " ，未分配贈品數量: "  +  (MaxGiftsAmount - giftsData.qty);  

		if(MaxGiftsAmount == giftsData.qty){
			msg = "您已分配所有贈品。"
		}  


		if(MaxGiftsAmount < giftsData.qty){
			msg = "贈品數量重置中...";
			$("#gifts-msg").html(msg);
			$('div#gift-list  input.qty').val(0);
			setTimeout(function(){ RenderGiftMsgUseData(); }, 1000);
		}    
		                                                                                                                                                                                                                                 
		$("#gifts-msg").html(msg);


	
	}


	

	function RenderGiftsOpts(){
		
		if(gifts.length==0){
			return;
		}


    
    	var CartListOutput = '';

    	for(var i=0;i<gifts.length;i++){
    		
    		var gift = MyCart.GetProductDataById(gifts[i]);	


    		if(typeof gift == "undefined"){

    		}

    		//console.log(gift);
    		
    		var image_html="";
    		if(gift.imageUrl !=""){
    			image_html='<img src="'+gift.imageUrl+'" alt="'+gift.title+'">';
    		}
   		

    		if(is_mobile==0){
    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-name">\
				        	'+gift.title+'\
				        </div>\
				        <div class="cell col-num">\
				        	<div class="quantity clearfix">\
								<input type="button" value="-" index="'+i+'" class="minus">\
								<input type="text" data-id="'+gift.id+'" name="quantity"  value="0" class="qty" />\
								<input type="button" value="+" index="'+i+'" class="plus">\
							</div></div>\
			    	</div>';
    		}else{

    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-num">\
				        	'+gift.title+'<br>\
				        	<div class="quantity clearfix">\
								<input type="button" value="-" index="'+i+'" class="minus">\
								<input type="text" data-id="'+gift.id+'" name="quantity"  value="0" class="qty" />\
								<input type="button" value="+" index="'+i+'" class="plus">\
							</div>\
				        </div>\
			    	</div>';
    		}    		

    	}

    	$('#gift-list .list-body').html(CartListOutput);
    	
	  	$('div#gift-list  input.minus').click(function(){
	  		var target = $(this).parent().find(".qty");
	  		var qty = target.val();
	  		var oldvalue = Number(qty);
	  		if(oldvalue <= 0){
	  			//alert("贈品總數已達最大數量。");
				return;
	  		}

	  		target.val(oldvalue-1);

	    })


	    $('div#gift-list  input.plus').click(function(){
	    	var target = $(this).parent().find(".qty");
	    	var qty = target.val();
	    	var oldvalue = Number(qty);
	    	var giftsData = GiftQtyCheck();
			var MaxGiftsAmount = GetMaxGiftAmount();

			if( giftsData.qty >= MaxGiftsAmount){
				alert("贈品總數已達最大數量。");
				return;
			}

			target.val(oldvalue+1);

	    })


	    $('div#gift-list  input.qty').focusin( function(){
		    $(this).data('val', $(this).val());
		});

	    $('div#gift-list  input.qty').change(function(){
	    	
	    	var qty = $(this).val();
	    	var oldvalue = Number($(this).data('val'));
	    	var giftsData = GiftQtyCheck();
			var MaxGiftsAmount = GetMaxGiftAmount();
			if( giftsData.qty > MaxGiftsAmount){
				alert("贈品總數已達最大數量。");
				$(this).val(oldvalue);
				return;
			}
	    	
	    })

	    //AjaxShippingFeeAndDiscount(items);


	}

	function GetMaxGiftAmount(){
		var total = 0;
		for (var i = 0; i < items.length; i++) {
			
			var item=items[i];
			if(main_products.indexOf(Number(item.id)) != -1){
				total += Number(item.amount);
			}

		}
		return total;
	}

	function GiftQtyCheck(){
		
		var output = {
			qty:0,
			gifts:[]
		}

		$("div#gift-list input.qty").each(function(i){

			var amount = $(this).val();

			if(amount == 0){
				return;
			}

			var gift_id = $(this).data("id");

			var gift = MyCart.GetProductDataById(gift_id);	

			output.qty += Number(amount);
			
			output.gifts.push({
				id: gift_id,
				price: gift.price,
				title: gift.title,
				imageUrl: gift.imageUrl,
				amount: amount
			});

		})

		//console.log(output);

		return output;
	}

	function ResetCheckBtn(){
		
		$("#check-btn").click(function(){
			
			var giftsData = GiftQtyCheck();

			var MaxGiftsAmount = GetMaxGiftAmount();
			
			if(giftsData.qty != MaxGiftsAmount){

				alert("請填寫正確贈品數量。");
				return false;

			}


			SetCookieGifs(giftsData.gifts);

			//return false;
		})
	}

	function SetCookieGifs(gifts){
		
		MyCartCookies.set('gifts',gifts);

	}

	function getCookieGifs(){
		
		MyCartCookies.getJSON('gifts');

	}

	function RemoveCookieGifts(){
		MyCartCookies.remove('gifts');
	}

	

	


	

</script>
<?php
}


// add_action("check_callback","CheckGift20181125");
function CheckGift20181125(){

	
	$sale = OnSaleActive();

	if($sale != "newyear"){
		return;
	}

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
?>
<script type="text/javascript">

	//$("#coupon_block").hide();

	var gifts = MyCartCookies.getJSON('gifts');

	if(typeof gifts == "object" && gifts.length > 0){
		SetGift_CheckPage();
	}

	function SetGift_CheckPage(){
		
		for(var i=0;i<gifts.length;i++){
			var gift = gifts[i];

		}
	}

	function RenderCheckListCallBack(){

    
    	var CartListOutput = "";

    	for(var i=0;i<gifts.length;i++){
    		
    		var item = gifts[i];
    	
    		var price = item.price;

    		

    		var image_html="";
    		if(item.imageUrl !=""){
    			image_html='<img src="'+item.imageUrl+'" alt="'+item.title+'">';
    		}

    		if(Number(price) >= 0 ){
    			var price_html=MyCart.CartMoneyFormat(price,"$");
    			var the_subtotal=MyCart.CartMoneyFormat(price * item.amount,"$");
    		}else{
    			var price_html='-'+MyCart.CartMoneyFormat(price*(-1),"$");
    			var the_subtotal='-'+MyCart.CartMoneyFormat(price * item.amount * (-1),"$");
    		}
    		
    		

    		if(is_mobile==0){
    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-name">\
				        	'+item.title+'\
				        </div>\
				        <div class="cell col-num">\
				        	'+price_html+' X '+item.amount+'\
				        </div>\
				        <div class="cell col-total">'+the_subtotal+'</div>\
			    	</div>';
    		}else{

    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-name mobile">\
				        	'+item.title+'\
				        </div>\
				        <div class="cell col-total mobile">'+price_html+'<br>X '+item.amount+'</div>\
			    	</div>';
    		}

    		

    	}

    	$('#check-list .list-body').append(CartListOutput);
    	
    	
    }


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

		var receipt=$("#receipt").val();
		//console.log(receipt.length);

		if(receipt.length==0 || receipt.length==8){
			$("#receipt").removeAttr("style");
		}else{
			alert("<?php echo $FormWords["error_taxId"];?>");
			$("#receipt").css("border","2px solid red");
			return false;				
		}

		var countryname=$( "#the_country option:selected" ).text();


		if(typeof gifts == "object" && gifts.length > 0){
			for(var i=0;i<gifts.length;i++){
				var gift = gifts[i];
				order.push({
					id: gift.id,
					title:gift.title,
					price:gift.price,
					amount: gift.amount
				});
			}
		}

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
}


add_action("thanks_callback","thanksGift20181125");
function thanksGift20181125(){
?>
<script type="text/javascript">
	MyCartCookies.remove('gifts');
</script>	
<?php
}