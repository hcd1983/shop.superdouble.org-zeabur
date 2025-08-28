console.log("My products plugin active");
var MyCart={setting:{},usecate:true};
var productsList;	
var MyPixelSetting={
	currency:"TWD",
	content_type: 'product',
	//content_category: 'spinbox',
	//"content_category":998818916916699,
};

var shippingfee=0;
var discount=0;
var MyCartCookies = Cookies.noConflict();
var items;
var is_mobile=0;

function MyCartWords(index="",changeto=[]){
	if(typeof MycartLang[index] != "undefined"){
		var res = MycartLang[index];
		if(typeof changeto == "object" && changeto.length > 0){
			$(changeto).each(function(i){
				var value=this;
				if(typeof value.search != "undefined" && typeof value.replace != "undefined"){
					res = res.replace(value.search, value.replace);
				}
			})
		}
	}else{
		var res = "";
	}

	return res;
}

(function(){
	
	var bagInList

	function AjaxShippingFeeAndDiscount(items){
		var item_str=JSON.stringify(items);
		jQuery(".amount_shipping-bill").html('<i class="fas fa-spinner fa-spin"></i>');
		jQuery(".amount_discount-bill").html('<i class="fas fa-spinner fa-spin"></i>');
		jQuery.ajax({
	        url: MyCart.ajaxurl,
	        dataType: 'json',
	        data: {
	            action: 'SheepingFee',
	            items: items
	        },
	        type: 'POST'
	    }).done(function( data ) {

	    	  shippingfee=data.shippingfee;	
	    	  discount=data.discount;
			  var Myshippingfee=MyCart.numberWithCommas(data.shippingfee);
			  var Mydiscount=MyCart.numberWithCommas(data.discount);	
		      jQuery(".amount_shipping-bill").html("$"+Myshippingfee);
		      jQuery(".amount_discount-bill").html("$"+Mydiscount);
		      jQuery(".amount_shipping").html("$"+Myshippingfee);
		      jQuery(".total-bill-discount").html("$"+Mydiscount);

		      if(discount > 0){
		      	jQuery(".total-bill-discount").show();
		      }else{
		      	jQuery(".total-bill-discount").hide();
		      }
		     

		      if(typeof AjaxShippingFee_callback == "function" ){
			    	AjaxShippingFee_callback();
			  }

		    
		});
	}

	function AjaxEverything(items=[],country="",coupon="",shipping_method=""){
		var item_str=JSON.stringify(items);

		if(typeof MyCart.AjaxEverythingBefore == "function"){
	    	MyCart.AjaxEverythingBefore();
	    }	
		//jQuery(".amount_shipping-bill").html('<i class="fas fa-spinner fa-spin"></i>');
		jQuery.ajax({
	        url: MyCart.ajaxurl,
	        dataType: 'json',
	        data: {
	            action: 'Everything',
	            items: items,
	            country:country,
	            coupon:coupon,
	            shipping_method:shipping_method,
	            //shippingfee:shippingfee,
	            //discount:discount,
	        },
	        type: 'POST'
	    }).done(function( data ) {
	    	 
	    	  //console.log(data);
	    	  if(typeof MyCart.AjaxEverythingCallback == "function"){
	    	  	MyCart.AjaxEverythingCallback(data);
	    	  }	
		});
	}	

	function applyCoupon(){
		var item_str=JSON.stringify(items);
		jQuery("#coupon_block button").hide();
		jQuery("#couponStr").attr("readonly","readonly");
		jQuery("#coupon_block button").after('<div class="coupon_loading" style="text-align:center;width:100px;"><i class="fas fa-spinner fa-spin"></i>');
		$("#coupon_block .error-msg").remove();
		//jQuery(".amount_shipping-bill").html('<i class="fas fa-spinner fa-spin"></i>');
		jQuery.ajax({
	        url: MyCart.ajaxurl,
	        dataType: 'json',
	        data: {
	            action: 'Coupon',
	            items: items,
	            coupon:$("#couponStr").val(),
	            shippingfee:shippingfee,
	            discount:discount,
	        },
	        type: 'POST'
	    }).done(function( data ) {
	    	  $(".coupon_loading").remove();	    	  
	    	  items=data.items;
	    	  shippingfee=data.shippingfee;
	    	  discount=data.discount;
	    	  if(data.status=="S"){
	    	  	$('#coupon').val(data.coupon);
	    	  	$("#coupon_block .col_half").remove();
	    	  	$("#coupon_block").append("<h5 style='color:green;text-align:center;display:none;'>"+data.message+"</h5>");
	    	  	$("#coupon_block h5").fadeIn(300);
	    	  }else{
	    	  	jQuery("#couponStr").removeAttr("readonly");
	    	  	jQuery("#coupon_block button").show();
	    	  	$("#coupon_block").append("<h5 class='error-msg' style='color:red;text-align:center;display:none;'>"+data.message+"</h5>");
	    	  	$("#coupon_block .error-msg").fadeIn(300);
	    	  }
	    	  console.log(data);
		      RenderCheckList(data.items,data.shippingfee,data.discount);	
		});
	}		

	const GetProductsList =  async(callback) => {
	  const a = await fetch(MyCart.setting.json_url,{
		    method: "POST",
		    credentials: 'include',
		    headers: {
		        'Accept': 'application/json',
		        'Content-Type': 'application/json'
		      },
		   body: JSON.stringify({
		        picsize:"thumbnail"
		    })
		});
	  const b = await a.json();
	  //const c = await console.log(b);
	  const d = await setProductList(b);
	  const e = await callbackFn(callback,b);
	}

	const AjaxGetProductsList =  async(args={},callback) => {
	  const a = await fetch(MyCart.setting.json_url,{
		    method: "POST",
		    credentials: 'include',
		    headers: {
		        'Accept': 'application/json',
		        'Content-Type': 'application/json'
		      },
		   body: JSON.stringify({
		        picsize:"thumbnail",
		        args
		    })
		});
	  const b = await a.json();
	  const c = await console.log(b);
	  const d = await setProductList(b);
	  const e = await callbackFn(callback,b);
	}





	const AjaxaddToCartById =  async(id,amount=1) => {		
	  const a = await fetch(MyCart.setting.json_url,{
		    method: "POST",
		    credentials: 'include',
		    headers: {
		        'Accept': 'application/json',
		        'Content-Type': 'application/json'
		      },
		    body: JSON.stringify({
		        args:{

		        	include:{
		        		'p' : id ,
		        	},		        
		        }, 
		        picsize:"thumbnail",
				fields:["id","title","price","imageUrl"]	
		    })
		});
	  const b = await a.json();
	  //const nb = await console.log(b);
	  const c = await addItemToCart(b[0],amount);
	  
	}



	var callbackFn=function(callback=function(){return;},b){
		if(typeof(callback) != "function"){
			return;
		}

		return callback(b);
	}

	var setProductList = function(list){
	  	productsList = list
	}

	var getHostName=function(url) {
	    var matcher = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/i);
	    if (matcher != null && matcher.length > 2 && typeof matcher[2] === 'string' && matcher[2].length > 0) {
	    return matcher[2];
	    }
	    else {
	        return null;
	    }
	}

	var getDomain=function(url) {
	    var hostName = getHostName(url);
	    var domain = hostName;
	    
	    if (hostName != null) {
	        var parts = hostName.split('.').reverse();
	        
	        if (parts != null && parts.length > 1) {
	            domain = parts[1] + '.' + parts[0];
	                
	            if (hostName.toLowerCase().indexOf('.co.uk') != -1 && parts.length > 2) {
	              domain = parts[2] + '.' + domain;
	            }
	        }
	    }

	    if(domain != "localhost"){
	    	domain="."+domain;
	    }	    
	    return domain;
	}


	var FBpixel = function(action,items=[]){

		if(typeof(fbq)!="function" || items.length==0){
			return false;
		}

		var content_ids=[];
		var content_name=[];
		var num_items=[];
		var totalBill=0;

		if(typeof shippingfee == "undefined" || shippingfee == ""){
			shippingfee=0;
		}

		if(typeof discount == "undefined" || discount == ""){
			discount=0;
		}

		for(var i=0;i<items.length;i++){
			
			var item = items[i];
			totalBill += (item.price * Number(item.amount));

			content_name.push(item.title);
			num_items.push(item.amount);

			if(typeof(item.color_id) == "string"){

				content_ids.push(item.color_id);


			}else{
				content_ids.push(item.id);
			}

			
		}

		switch (action) {
			
			case 'AddToCart':

				if(MyCart.usecate==false){
					fbq('track', 'AddToCart');
					break;
				}
			  				
				fbq('track', 'AddToCart', { 
				    content_type: MyPixelSetting.content_type,
				    //content_category: 'allrover products',
				    currency: MyPixelSetting.currency,
				    content_ids:content_ids,
				    content_name: content_name,
				    num_items:num_items,		   
				    value: totalBill,
				    				   
				});

				if(typeof ga == "function"){
					var label = $(this).data("title");
					ga('send', 'event', 'add to cart', 'click',label,1);
				}
											
				break;
			
			case 'Purchase':

				totalBill=totalBill+Number(shippingfee)-Number(discount);

				if(MyCart.usecate==false){
					fbq('track', 'Purchase',{currency:MyPixelSetting.currency,value: totalBill});
					break;
				}

				fbq('track', 'Purchase', {
				  content_type: MyPixelSetting.content_type,
				  //content_category: 'allrover products',
				  currency: MyPixelSetting.currency,		
				  content_ids: content_ids,
				  content_name: content_name,
				  num_items:num_items,						 
				  value: totalBill,
				  
				});

				if(typeof ga == "function"){
					var label = "finish payment";
					ga('send', 'event', 'purchase','purchase',label,totalBill);
				}
				break;

			case 'InitiateCheckout':

				if(MyCart.usecate==false){
					fbq('track', 'InitiateCheckout',{value: totalBill,currency:MyPixelSetting.currency});
					break;
				}
				fbq('track', 'InitiateCheckout', {
				  content_type: MyPixelSetting.content_type,
				  //content_category: 'allrover products',
				  currency: MyPixelSetting.currency,		
				  content_ids: content_ids,
				  content_name: content_name,
				  num_items:num_items,						 
				  value: totalBill,
				  
				});

				if(typeof ga == "function"){
					var label = "InitiateCheckout";
					ga('send', 'event', 'InitiateCheckout','InitiateCheckout',label,totalBill);
				}
				break;	

			case 'AddPaymentInfo':
				if(MyCart.usecate==false){
					fbq('track', 'AddPaymentInfo',{value: totalBill});
					break;
				}
				fbq('track', 'AddPaymentInfo', {
				  content_type: MyPixelSetting.content_type,
				  //content_category: 'allrover products',
				  currency: MyPixelSetting.currency,			
				  content_ids: content_ids,
				  content_name: content_name,
				  num_items:num_items,						 
				  value: totalBill,
				  
				});

				if(typeof ga == "function"){
					var label = "AddPaymentInfo";
					ga('send', 'event', 'AddPaymentInfo','AddPaymentInfo',label,totalBill);
				}
				break;		

			default:
				return false;
				break;
		}

	}

	var GetCartItems = function(){
		var items=MyCartCookies.getJSON('cart');
		if(typeof items == 'undefined' || Array.isArray(items) == false) {
			items =[];
		}

		var fixed_items=[];
		var the_items= items;
		$(the_items).each(function(i){

			if(!isNaN(this.amount) && this.amount != null){
				fixed_items.push(this);
				//items.splice(i, 1);
				//console.log(i);
			}
		})

		return fixed_items;
	}

	var GetCartCountry = function(){
		var country=MyCartCookies.getJSON('country');
		if(typeof country !== 'string') {			
			country="";
		}

		return country;
	}

	var SetCountry = function(country){
		MyCartCookies.set('country',country, { domain: MyCart.CookidDomain ,expires: 7 });
	}

	

	var ResetCountryOpts = function(callback){
		var country=MyCart.GetCartCountry();
		
		if(typeof country=="undefined"){
			return;
		}

		$("#the_country").find("option").each(function(i) {
		    if($(this).val() == country){
		    	$(this).prop('selected', true);
		    }
		});
		if(typeof callback==="function"){
			callback();
		}
	}

	var GetShippingMethod = function(){
		var method=MyCartCookies.getJSON('shipping_method');
		if(typeof method !== 'string') {			
			method="";
		}

		return method;
	}

	var SetShippingMethod = function(method){
		MyCartCookies.set('shipping_method',method, { domain: MyCart.CookidDomain ,expires: 7 });
	}

	var ResetShippingOpts = function(callback){
		var method=MyCart.GetShippingMethod();
		
		if(typeof method=="undefined"){
			return;
		}

		$("#shipping_method").find("option").each(function(i) {
		    if($(this).val() == method){
		    	$(this).prop('selected', true);
		    }
		});
		if(typeof callback==="function"){
			callback();
		}
	}

	var ResetAllOptions = function(callback){
		ResetShippingOpts();
		ResetCountryOpts();
		if(typeof callback==="function"){
			callback();
		}
	}


	
	var renderCart =function() {
		items = GetCartItems();
		if(typeof items == 'undefined') {
			items =[];
		}

		var totalAmount = 0;
		var totalBill =0
		var output = '';
		var absolute_index=0;

		if(items.length == 0){
			$(".top-cart-content").hide();
		}

		for(var i=0;i<items.length;i++){
			var item = items[i];

			if(typeof item.price == "undefined" || typeof item.amount == "undefined"){
				items.splice(absolute_index, 1);
				absolute_index--
				MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
				continue;
			}

			item.amount=Number(item.amount);

			var EnoughStore=MyCart.IfEnoughStore(item.id,item.amount);
			if(EnoughStore !==true){
	  			item.amount=EnoughStore;
	  			MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });	  				  			
	  		}

	  		if(item.amount==0){
	  			console.log(absolute_index);
	  			removeItem(absolute_index,false);
	  			absolute_index--
	  			continue;
	  		}

			totalAmount +=  parseInt(item.amount);
			totalBill += (item.price * item.amount);

			output += '<div class="top-cart-item clearfix">\
										<div class="top-cart-item-image">\
											<img src="'+item.imageUrl+'" />\
										</div>\
										<div class="top-cart-item-desc">\
											<p style="margin-bottom: 10px;">'+item.title+'</p>\
											<span class="top-cart-item-price">$'+MyCart.numberWithCommas(item.price)+'</span>\
											<span>&nbsp x '+item.amount+'</span>\
											<span class="top-cart-item-remove" index="'+i+'"><i class="far fa-trash-alt"></i></span>\
										</div>\
									</div>';
			absolute_index++;
		}

		$('.top-cart .total-amount').text(totalAmount);
		$('a#fixed_cart_bottom .total-amount').text(totalAmount);
		$('#top-cart .total-bill').text(MyCart.numberWithCommas(totalBill));

	    if(totalAmount == 0){
	        $('#top-cart a#goCheck').hide();
	        $('a#fixed_cart_bottom').fadeOut();
	    }else {
	        $('#top-cart a#goCheck').show();
	        $('a#fixed_cart_bottom').fadeIn(300,"swing",function(){
	        	// setTimeout(function(){
	        	// 	$('a#fixed_cart_bottom').fadeOut();
	        	// },1500)
	        });
	        
	    }

	    if(output==""){
	    	output=MyCartWords("BuyNothing");
	    }
	    setTimeout(function(){
	        $('#top-cart .top-cart-items').html(output);
	        $('#top-cart .top-cart-item-remove').click(function(){
	            var remove_int = $(this).attr('index');
	            // removeCallback(removeItem);
	            removeItem(remove_int);
	            //console.log("aaa");
	        })
	    }, 100);
	}

	var removeItem= function(remove_int,render=true){
		items.splice(remove_int,1);
		//MyCartCookies.set('cart',items, { expires: 7 });
		MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
		if(render){
			renderCart();
		}		
	}

	var addToCartById = function(id){
		


		if(!productsList) {
			console.log('no preload productList.');
			AjaxaddToCartById(id,1);
			return;
		}


		var target = productsList.filter(function(item){ return item.id == id })[0];
	  	//get list of products, find item by id
	  	if(!target) {
	  		console.log('product id not exist in preload productList:', id);
	  		AjaxaddToCartById(id,1);
	  		return;
	  	}

	  	console.log(target);

	  	item = {
  			id: target.id,
  			price: target.price,
  			title: target.title,
  			imageUrl: target.imageUrl,
  		}

		if(target.parent_title){  			
  			item.title = target.parent_title + " - " + target.title;
  		}  		

  		if(item == undefined || item.price==="" || item.price=== null){
  			return;
  		}

  		var good = addItemToCart(item,1);

  		if(!good){
  			return;
  		}

  		if(typeof(addToCartById_callback)=="function"){
  			addToCartById_callback(item);
  		}  		

  		
  			
	  	return;
/*
		
		if(item.length==0){
			console.log('product id not exist, please check id:', id);
	  		return
		}else{
			item=item[0];
		}

		var inCartArrItem = items.filter(function(item){ return item.id == id; })[0];
		if (inCartArrItem) {
			var index = items.indexOf(inCartArrItem);
			items[index].amount++;
		} else {
				  		
	  		item.amount = 1;
	  		items.push(item);
		}

		//FBpixel--------------------------

		

		//FBpixel End-------------------------
		
		MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
		addToCartAlert();
		renderCart();
*/		
  }

  var GetProductDataById = function(id,_target=false){
		
		if(!productsList) {
			return;
		}
		var target = productsList.filter(function(item){ return item.id == id })[0];
	  	//get list of products, find item by id
	  	if(!target) {
	  		return;
	  	}

	  	var item = {
  			id: target.id,
  			price: target.price,
  			title: target.title,
  			imageUrl: target.imageUrl,
  			store:target.store,
  		}

  		if(_target===false){  			
	  		return item;
  		}

  		if(item[_target] == undefined){
  			return null;
  		}else{
  			return item[_target];
  		}


  }

  var GetProductImageSizesById = function(id,size="full"){
		
		if(!productsList) {
			return;
		}
		var target = productsList.filter(function(item){ return item.id == id })[0];
		// console.log(target);
	  	//get list of products, find item by id
	  	if(!target) {
	  		return;
	  	}

	  	var item = {
  			all_image_size: target.all_image_size
  		}

        if(!item.all_image_size){
        	return ;
        }
  		if(typeof item.all_image_size[size] == "undefined"){
  			return 	 item.all_image_size["full"]; 	
  		}else{
  			return item.all_image_size[size];
  		}

  }



  var addToCartAlert = function(){
  	$(".cart-alert").click();
  }





  var addItemToCart = function (_newItem,amount){
		

		newItem = {
  			id: _newItem.id,
  			price: _newItem.price,
  			title: _newItem.title,
  			imageUrl: _newItem.imageUrl,
  			amount: amount
  		}
  		
  		// if(_newItem.parent_title){
  			
  		// 	newItem.title = _newItem.parent_title + newItem.title;
  		// }

		items = GetCartItems();

		if(typeof items == 'undefined') {
			items =[];
		}
		var sameItemInCart = false;
		var sameItem = null;

		var sameIdItems = items.filter(function(item){ return item.id == newItem.id}); 

		if(sameIdItems.length>0) {
			var sameItem = sameIdItems.filter(function(item){
				return (item.color == newItem.color);
			})[0];
			if(sameItem)
				sameItemInCart = true;
		}

		if (sameItemInCart) {
			var index = items.indexOf(sameItem);
			var final_amount = parseInt(items[index].amount)+parseInt(newItem.amount);

			if(MyCart.IfEnoughStore(items[index].id,final_amount)!==true){
	  			if(typeof(NotEnoughStore_fn)=="function"){
					NotEnoughStore_fn(items[index]);
				}
	  			return;
	  		}

	  		items[index].amount = final_amount;

		} else {

			if(MyCart.IfEnoughStore(newItem.id,newItem.amount)!==true){
	  			if(typeof(NotEnoughStore_fn)=="function"){
					NotEnoughStore_fn(newItem);
				}
	  			return;
	  		}

			items.push(newItem);
			
		}

		//FBpixel--------------------------
			var newItemForPixel=newItem;
			if(sameItemInCart){
				newItemForPixel.amount = items[index].amount;
			}

			FBpixel("AddToCart",[newItemForPixel]);
			//FBpixel("AddToCart",items);
		
		//FBpixel End-------------------------

		MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });

		if(typeof(addItemToCart_callback)=="function"){
  			addItemToCart_callback(newItem,amount);
  		}
		//addToCartAlert();
		renderCart();

		return true;
	}

	function numberWithCommas(x) {
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	function CartMoneyFormat(x,custom=""){
		var moneylogo;
		if(custom !== ""){
			moneylogo=custom;
		}else{
			if(typeof MyCart.setting.moneylogo == "string"){
				moneylogo=MyCart.setting.moneylogo;
			}else{
				moneylogo="";
			}			
		}

		return moneylogo+MyCart.numberWithCommas(x);
		
	}

	var clear = function(){
		
		MyCartCookies.remove('cart');
		MyCartCookies.remove('cart', { domain: MyCart.CookidDomain}); 
	}

	function SetCartPage(is_mobile=0,CheckPage='#'){
		var html;
		if(is_mobile==0){
		html='<div id="cart-list" class="cart item-list">'+
					'<div class="list-head">'+
						'<div class="cell col-img">&nbsp;</div>'+
				        '<div class="cell col-name">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-price">'+MyCartWords("SinglePrice")+'</div>'+
				        '<div class="cell col-num">'+MyCartWords("amount")+'</div>'+
				        '<div class="cell col-total">'+MyCartWords("SubTotal")+'</div>'+
				        '<div class="cell col-action">'+MyCartWords("action")+'</div>'+
				    '</div>'+
				    '<div class="list-body"></div>'+    
				'</div>'+
				'<div id="cart-count">'+
					'<div class="the_country_selector">'+MyCart.CountrySelector+'</div>'+
					'<div class="the_shipping_selector">'+MyCart.ShippingSelector+'</div>'+
					'<div class="total-bill">'+MyCartWords("ProductTotal")+': <span class="amount_product"></span></div>'+
					'<div class="total-bill">'+MyCartWords("shippingFee")+': <span class="amount_shipping-bill"></span></div>'+
					'<div class="total-bill total-bill-discount">'+MyCartWords("discount")+': <span class="amount_discount-bill"></span></div>'+
					'<a id="check-btn" class="button" href="'+CheckPage+'">'+
					MyCartWords("gotoCheckOut")+
					'</a>'+
				'</div>';
		}else{
			html='<div id="cart-list" class="cart item-list">'+
					'<div class="list-head">'+
				        '<div class="cell col-name">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-num">&nbsp;</div>'+
				        '<div class="cell col-action"></div>'+
				    '</div>'+
					'<div class="list-body"></div>'+
					'</div>'+
					'<div id="cart-count">'+
						'<div class="the_country_selector">'+MyCart.CountrySelector+'</div>'+
						'<div class="the_country_selector">'+MyCart.ShippingSelector+'</div>'+
						'<div class="total-bill">'+MyCartWords("ProductTotal")+': <span class="amount_product"></span></div>'+
						'<div class="total-bill total-bill-shipping">'+MyCartWords("shippingFee")+': <span class="amount_shipping-bill"></span></div>'+
						'<div class="total-bill total-bill-discount">'+MyCartWords("discount")+': <span class="amount_discount-bill"></span></div>'+
						'<a id="check-btn" class="button" href="'+CheckPage+'" >'+
						MyCartWords("gotoCheckOut")+
						'</a>'+	
					'</div>';
		}

		$('.cart-list-container').html(html);
	}

	function SetCheckPage(is_mobile=0){
		var html;
		if(is_mobile==0){
		html='<div id="check-list" class="cart item-list">'+
					'<div class="list-head">'+
						'<div class="cell col-img">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-name">&nbsp;</div>'+
				        '<div class="cell col-num">&nbsp;</div>'+
				        '<div class="cell col-total">&nbsp;</div>'+
				    '</div>'+
				    '<div class="list-body"></div>'+    
				'</div>'+
				'<div id="check-count">'+
					'<div class="total-bill"><div class="label">'+MyCartWords("ProductTotal")+':</div> <span class="amount_product"></span></div>'+
					'<div class="total-bill total-bill-shipping"><div class="label">'+MyCartWords("shippingFee")+':</div> <span class="amount_shipping"></span></div>'+
					'<div class="total-bill total-bill-discount"><div class="label">'+MyCartWords("discount")+':</div> <span class="amount_discount"></span></div>'+
					'<div class="total-bill total-price"><div class="label">'+MyCartWords("CheckTotal")+':</div> <span class="amount_total"></span></div>'+
					'<a id="insert-btn" class="button" href="javascript:void(0)">'+
					MyCartWords("CheckOut")+
					'</a>'+
				'</div>';
		}else{
		html='<div id="check-list" class="cart item-list">'+
					'<div class="list-head">'+
						'<div class="cell col-img">'+MyCartWords("ProductName")+'</div>'+
				        '<div class="cell col-name">&nbsp;</div>'+
				        '<div class="cell col-total">&nbsp;</div>'+
				    '</div>'+
				    '<div class="list-body"></div>'+    
				'</div>'+
				'<div id="check-count" class="mobile">'+
					'<div class="total-bill"><div class="label">'+MyCartWords("ProductTotal")+':</div> <span class="amount_product"></span></div>'+
					'<div class="total-bill  total-bill-shipping"><div class="label">'+MyCartWords("shippingFee")+':</div> <span class="amount_shipping"></span></div>'+
					'<div class="total-bill  total-bill-discount"><div class="label">'+MyCartWords("discount")+':</div> <span class="amount_discount"></span></div>'+
					'<div class="total-bill total-price"><div class="label">'+MyCartWords("CheckTotal")+':</div> <span class="amount_total"></span></div>'+
					'<a id="insert-btn" class="button mobile" href="javascript:void(0)">'+
					MyCartWords("CheckOut")+
					'</a>'+
				'</div>';
		}

		$('.check-list-container').html(html);
		$('#insert-btn').click(function(){
			$("#checkform").submit();
		});

	}

	function RenderCartList(items=[]){
		if(items.length==0){
			$('.cart-list-container').html("<h2>"+MyCartWords("nodata")+"</h2>");
			return "No data";
		}
    	//var singleShippingAmount = 0;//單一計運費
    	//var otherShippingAmount = 0; //全部計運費
    	var totalBill =0;
    	var CartListOutput = '';
    	//var shippingBill = 0;
    	var absolute_index=0;
    	for(var i=0;i<items.length;i++){
    		
    		var item = items[i];

    		//console.log(item);
    		var targetArr = productsList.filter(function(_item){ return _item.id == item.id; });

    		var price = item.price;
    		
    		var image_html="";
    		if(item.imageUrl !=""){
    			image_html='<img src="'+item.imageUrl+'" alt="'+item.title+'">';
    		}

    		var Enough_Store=MyCart.IfEnoughStore(item.id,item.amount);

    		if(Enough_Store !== true){
				item.amount=Enough_Store;
				if(typeof(NotEnoughStore_fn)=="function"){
					setTimeout(function(){NotEnoughStore_fn(item)},1000);
				}								
			}

			if(item.amount===0){					
				MyCart.removeItem(absolute_index);
				absolute_index--;
			}

			MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
			if(item.amount===0){
				if(items.length==0){
					$('.cart-list-container').html("<h2>"+MyCartWords("nodata")+"</h2>");
					return;
				}					
				continue;
			}
    		
    		
    		totalBill += (price * item.amount);

    		if(is_mobile==0){
    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-name">\
				        	'+item.title+'\
				        </div>\
				        <div class="cell col-price">\
				        	'+CartMoneyFormat(price,"$")+'\
				        </div>\
				        <div class="cell col-num">\
				        	<div class="quantity clearfix">\
								<input type="button" value="-" index="'+i+'" class="minus">\
								<input type="text" index="'+i+'" name="quantity" data-origin="'+item.amount+'" value="'+item.amount+'" class="qty" />\
								<input type="button" value="+" index="'+i+'" class="plus">\
							</div>\
				        </div>\
				        <div class="cell col-total">'+CartMoneyFormat(price * item.amount,"$")+'</div>\
				        <div class="cell col-action">\
				        	<a class="remove" title="Remove this item" index="'+i+'"><i class="far fa-trash-alt"></i></a>\
				        </div>\
			    	</div>';
    		}else{

    			CartListOutput +='\
		    		<div class="item-single-line">\
			    		<div class="cell col-img">'+image_html+'</div>\
				        <div class="cell col-num">\
				        	'+item.title+'<br>\
				        	<div class="quantity clearfix">\
								<input type="button" value="-" index="'+i+'" class="minus">\
								<input type="text" index="'+i+'" name="quantity" data-origin="'+item.amount+'" value="'+item.amount+'" class="qty" />\
								<input type="button" value="+" index="'+i+'" class="plus">\
							</div>\
				        </div>\
				        <div class="cell col-action">\
				        	<a class="remove" title="Remove this item" index="'+i+'"><i class="far fa-trash-alt"></i></a><br>\
				        	<span class="col-total-mobile">'+CartMoneyFormat(price,"$")+'</span>\
				        </div>\
			    	</div>';
    		}

			absolute_index++;
    		

    	}


    	$('#cart-list .list-body').html(CartListOutput);
    	$('.amount_product').html(CartMoneyFormat(totalBill));
    	$('.amount_shipping-bill').html(CartMoneyFormat(shippingfee));
    	$('.amount_discount-bill').html(CartMoneyFormat(discount));
    	
    	if(Number(discount) > 0){
    		$('.total-bill-discount').show();
    	}else{
    		$('.total-bill-discount').hide();
    	}

    	$('div#cart-list a.remove').click(function(){
	  		var remove_int = $(this).attr('index');
	  		removeItemFromList(remove_int);
	  	})

	  	$('div#cart-list  input.minus').click(function(){
	  		var index = $(this).attr('index');
	  		//console.log(index);
	  		AmountChange(index, 'minus');
	    })


	    $('div#cart-list  input.plus').click(function(){
	    	var index = $(this).attr('index');
	    	//console.log(index);
	  		AmountChange(index, 'plus');
	    })

	    $('div#cart-list  input.qty').change(function(){

	    	var index = $(this).attr('index');
	    	var oldvalue = $(this).data('origin');
	    	var value =  $(this).val();
	    	var reg = /^\d+$/;
	    	if(reg.test(value)===false){
	    		var msg=MyCartWords("plsEnterNum");
	    		alert(msg);
	    		$(this).val(oldvalue);
	    		return;
	    	}
	    	SetItemAmount(index,parseInt(value));
	    })

	    //AjaxShippingFeeAndDiscount(items);


	    if(items.length == 1){
	    	$('div#cart-list a.remove').hide();
	    }
    }

    function RenderCheckList(items,shippingfee=0,discount=0){

    	//var singleShippingAmount = 0;//單一計運費
    	//var otherShippingAmount = 0; //全部計運費
    	var totalBill =0;
    	var CartListOutput = '';
    	//var shippingBill = 0;
    
    	for(var i=0;i<items.length;i++){
    		
    		var item = items[i];
    		var targetArr = productsList.filter(function(_item){ return _item.id == item.id; });

    		var price = item.price
    		
    		totalBill += (price * item.amount);

    		var image_html="";
    		if(item.imageUrl !=""){
    			image_html='<img src="'+item.imageUrl+'" alt="'+item.title+'">';
    		}

    		if(Number(price) >= 0 ){
    			var price_html=CartMoneyFormat(price,"$");
    			var the_subtotal=CartMoneyFormat(price * item.amount,"$");
    		}else{
    			var price_html='-'+CartMoneyFormat(price*(-1),"$");
    			var the_subtotal='-'+CartMoneyFormat(price * item.amount * (-1),"$");
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

    	
    	$('#check-list .list-body').html(CartListOutput);
    	$('.amount_product').text(CartMoneyFormat(totalBill,"$"));
    	$('.amount_shipping').text(CartMoneyFormat(shippingfee,"$"));
    	$('.amount_discount').text(CartMoneyFormat(discount,"$"));
    	$('.amount_total').text(CartMoneyFormat(Number(totalBill)+Number(shippingfee)- Number(discount),"$" ));
    	if(discount>0){
    		$(".total-bill-discount").show();
    	}else{
    		$(".total-bill-discount").hide();	
    	}


    	if(typeof  RenderCheckListCallBack == "function"){
    		 RenderCheckListCallBack();
    	}

    	
    }

    var IfEnoughStore=function(id,qty){    	
    	var store = MyCart.GetProductDataById(id,"store");
    	store =  parseInt(store);
    	if(store < 0 || store===""){
    		return true;
    	}
    	if(qty <= store && store > 0){
    		return true;
    	}else{
    		return store;
    	}
    }

    var AmountChange = function (i, value){
	  	var qty = items[i].amount;
	  	if(value ==='plus'){
	  		qty ++;
	  		if(MyCart.IfEnoughStore(items[i]["id"],qty)!==true){
	  			if(typeof(NotEnoughStore_fn)=="function"){
					NotEnoughStore_fn(items[i]);
				}
	  			return;
	  		}
	  	} else {
	  		if(qty>1)
	    		qty --;
	  	}
	  	if(items[i].amount!== qty) {
	  		items[i].amount = qty;
	    	MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
	    	//MyCartCookies.set('cart',items);
	    	//RenderCartList(items);
	  		MyCart.AjaxEverything(items,country,coupon,shipping_method);
	  	}
    }

    var SetItemAmount = function (i, amount){
	  	var qty = amount;
	  	var Enough_Store=MyCart.IfEnoughStore(items[i]["id"],qty);

	  	if(qty < 1){
	  		qty=1;
	  		var msg=MyCartWords("leastOne");
	    	alert(msg);

  		}

	  	if(Enough_Store!==true){
  			items[i].amount=Enough_Store;
  			if(typeof(NotEnoughStore_fn)=="function"){
				NotEnoughStore_fn(items[i]);
			}
  		}else{
  			items[i]["amount"]=qty;
  		}


  		MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
	    	//MyCartCookies.set('cart',items);
	    //RenderCartList(items);
	    MyCart.AjaxEverything(items,country,coupon,shipping_method);
	  	
    }
    

  	var removeItemFromList= function(remove_int){
  		items.splice(remove_int,1);
  		MyCartCookies.set('cart',items, { domain: MyCart.CookidDomain ,expires: 7 });
  		//MyCartCookies.set('cart',items, { expires: 7 });
  		//RenderCartList(items);
  		MyCart.AjaxEverything(items,country,coupon,shipping_method);
  	}

  	var AjaxCheckStore=function(items,callback){
  		var ids=items.map(function(item){
			return item.id;
		})
		$args={
		        'post__in':ids,        
		};

		if(typeof callback !=="function"){
			callback=function(){} ;
		}

		MyCart.AjaxGetProductsList($args,function(){
			CheckEveryStore(callback);
		});  	
  	}

  	var CheckEveryStore=function(callback){
  		var not_good=[];

  		if(typeof callback !=="function"){
			callback=function(){} ;
		}
  		for(var i=0;i<items.length;i++){

  			if(items[i]["amount"]==0){
  				continue;
  			}
  			var Enough_Store=MyCart.IfEnoughStore(items[i]["id"],items[i]["amount"]);
  			if(Enough_Store !== true){

  				if(typeof(NotEnoughStore_fn)=="function"){
				//	NotEnoughStore_fn(items[i]);					
				}
				var bad_item=items[i];
				bad_item["index"]=i;
				bad_item["max_amount"]=Enough_Store;
				not_good.push(bad_item);

  			}
  		}
  		if(not_good.length > 0){
  			if(typeof(FinalCheckNotGood_fn)=="function"){
				FinalCheckNotGood_fn(not_good);
			}
  			return false
  		}else{
  			return callback();
  		}
  	}

  	function PriceFixer(items,productsList){
		$(items).each(function(i){
			var i_key = i;
			var i_id = items[i].id;

			$(productsList).each(function(j){
				if( i_id == productsList[j].id){							
					items[i_key].price = productsList[j].price;
				}

			})

		})			

		return items;	
	}




	function Start_My_Cart(){

		$('.top-cart').click(function(){
			$(".top-cart-content").toggle();
		})

		
		


/*
		$(document).on("click",".add-to-cart",function(){
			if(typeof($(this).data("id"))=="undefined"){
				return;
			}else{
				addToCartById($(this).data("id"));
			}
		})
*/


		$(document).on("click",".addtocart",function(){
			
			if($(this).hasClass("nofunction")){			
				return;
			}

			if($(this).hasClass("selector_btn")){
				var the_block = $(this).closest(".product_selector_block");
				var id = $(the_block).find(".product_selector").val();
				if(id == "not_select"){
					
					alert("請選擇尺寸或款式");
					return;
				}
				addToCartById(id);
			}

			if(typeof($(this).data("id"))=="undefined"){
				return;
			}

			var id=$(this).data("id");

			

			if($(this).hasClass("dontadd")){
				if(typeof(Nostore_callback)=="function"){
		  			Nostore_callback(id,this);
		  		}
				return;
			}

			

			var id=$(this).data("id");
			if(typeof($(this).data("childid"))!="undefined"){
				var light_title=$(this).data("title");
				var child_id=$(this).data("childid");					
				if(typeof(parent_product_fn)=="function"){
					parent_product_fn(light_title,id,child_id);
				}					
				return;
			}

			addToCartById(id);
			

		})

		$(document).on("click",".my-product-block .product-thumnail,.my-product-block h3",function(){
			var the_block = $(this).closest(".my-product-block");
			$(the_block).find(".view-product").click();
		})

		

		// 未點擊到的物件	
		$(document).click(function (e) {
		    var $tgt = $(e.target);
		    if ($tgt.closest(".top-cart-content,.top-cart").length) {
		    }else{
		    	$(".top-cart-content").hide();
		    }   

		});
	}

	MyCart.CookidDomain=getDomain(window.location.href );
	MyCart.GetProductsList=GetProductsList;
	MyCart.AjaxGetProductsList=AjaxGetProductsList;
	MyCart.addItemToCart=addItemToCart;
	MyCart.addToCartById= addToCartById;
	MyCart.GetProductDataById=GetProductDataById;
	MyCart.renderCart=renderCart;
	MyCart.AjaxaddToCartById=AjaxaddToCartById;
	MyCart.GetCartItems=GetCartItems;
	MyCart.clear=clear;
	MyCart.Start_My_Cart=Start_My_Cart;
	MyCart.SetCartPage=SetCartPage;
	MyCart.SetCheckPage=SetCheckPage;
	MyCart.RenderCartList=RenderCartList;
	MyCart.RenderCheckList=RenderCheckList;
	MyCart.GetProductImageSizesById=GetProductImageSizesById;
	MyCart.FBpixel=FBpixel;
	MyCart.AjaxShippingFeeAndDiscount=AjaxShippingFeeAndDiscount;
	MyCart.IfEnoughStore=IfEnoughStore;
	MyCart.removeItem=removeItem;
	MyCart.AjaxCheckStore=AjaxCheckStore;
	MyCart.applyCoupon=applyCoupon;
	MyCart.AjaxEverything=AjaxEverything;
	MyCart.GetCartCountry=GetCartCountry;
	MyCart.GetShippingMethod=GetShippingMethod;
	MyCart.SetShippingMethod=SetShippingMethod;
	MyCart.ResetShippingOpts=ResetShippingOpts;
	MyCart.removeItemFromList=removeItemFromList;
	MyCart.SetCountry=SetCountry;
	MyCart.ResetCountryOpts=ResetCountryOpts;
	MyCart.numberWithCommas=numberWithCommas;
	MyCart.PriceFixer=PriceFixer;
	MyCart.CartMoneyFormat = CartMoneyFormat;

	MyCart.ResetAllOptions=ResetAllOptions;
	/*
	MyCart = {
		getData:getData,
		renderCart: renderCart,
		addToCartById: addToCartById,
		addItemToCart: addItemToCart,
		setProductList: setProductList,
		FBpixel:FBpixel,
		clear: clear
	}
	*/

}())
