function parent_product_fn(title,id,child_id){
	var parent=MyCart.GetProductDataById(id);
	if(child_id==""){
		return;
	}

	var child_id_str=child_id.toString();
	var child_ids=child_id_str.split(",");

	if(child_ids.length ==1){
		MyCart.addToCartById(child_ids[0]);
		return;
	}
/*
	var numberWithCommas=function(x) {
		if(typeof x !=="string" && typeof x !=="number"){
			return;
		}
    	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
*/
	var numberWithCommas = MyCart.numberWithCommas;

	var html="";
	var price_arr=[];
	var title_arr=[];
	var actived=false;

	$(child_ids).each(function(i){
		
		var item=MyCart.GetProductDataById(child_ids[i]);
		

		if(item === undefined ){
			return;
		}
		
		if(!actived){
			var active=" active";
			actived=true;
		}else{
			var active="";
		}

		
		if(item === undefined){
			return;
		}
		price_arr.push(item.price);
		title_arr.push(item.title);

		if(item.store==0){
			active+=" "+"nostore";
		}

		html+="<div class='clild_opt"+active+"' data-title='"+item.title+"' data-id="+item.id+" data-price="+item.price+">";
		html+="<img src='"+item.imageUrl+"' title='"+item.title+"'>";
		html+="<div class='hover_info'>"+item.title+"<span class='price_on_mobile'>$"+numberWithCommas(item.price)+"</span>"+"</div>";
		html+="</div>";
	})

	var get_bigpic_html=function(pic_info){
		//var pic_info=MyCart.GetProductImageSizesById(id,"myproduct");
		if(pic_info === undefined){
			return;
		}
		return "<img width='"+pic_info.width+"' height='"+pic_info.height+"' src='"+pic_info.url+"'>";
	}

	var render_bigpic_img=function(id){

		var pic_info=MyCart.GetProductImageSizesById(id,"myproduct");

		var img = new Image();
		
		img.onload = function () {
		   $(".myproduct_light_box_bg .opt_bigpic .big_pic_img").html(get_bigpic_html(pic_info));
		}

		img.src = pic_info.url;
	}

	var default_pic_info=MyCart.GetProductImageSizesById(child_ids[0],"myproduct");
	var big_pic=get_bigpic_html(default_pic_info);

	if(big_pic== undefined){
		return;
	}

	var light_box_html="<div class='myproduct_light_box_bg' style='display:none;'>";
			light_box_html+="<div class='myproduct_light_box_container main_color'>";
			light_box_html+="<div class='myproduct-close'></div>";				
				light_box_html+='<div class="opt_bigpic">';
				light_box_html+='<h3 class="big_pic_title">'+title+'</h3>';
				light_box_html+='<div class="big_pic_img">'+big_pic+'</div>';
				light_box_html+='<h3 id="option_title">'+title_arr[0]+'</h3>';
				light_box_html+='<h3 class="price_title">'+MyCartWords("SinglePrice")+': <span id="option_price">'+numberWithCommas(price_arr[0])+'</span></h3>';
				light_box_html+='</div>';
				light_box_html+='<div class="opt_content">';
					light_box_html+='<h3 class="product_opt_title">'+MyCartWords("productOpts")+'</h3>';
					light_box_html+="<div class=opt_container>";
					light_box_html+=html;
					light_box_html+="</div>";
					light_box_html+="<div class='myproduct_light_box_footer'>";
						light_box_html+="<div id='option_submit' class='button addtocart' data-id='"+child_ids[0]+"'>"+MyCartWords("addtocart")+"</div>";
					light_box_html+="</div>";
				light_box_html+="</div>";
				light_box_html+="<div class='clear'></div>";	
			light_box_html+="</div>";
		light_box_html+="</div>";


	if($(".myproduct_light_box_bg").length == 0){
		$("body").append(light_box_html);
		$(".myproduct_light_box_bg").fadeIn(500);	
	}

	$(".myproduct_light_box_bg .clild_opt").click(function(){
		$(".myproduct_light_box_bg .clild_opt").removeClass("active");
		$(this).addClass("active");
		//$(".myproduct_light_box_bg .opt_bigpic .big_pic_img").html(get_bigpic_html($(this).data("id")));
		render_bigpic_img($(this).data("id"));
		$("#option_price").html("$"+numberWithCommas($(this).data("price")));
		$("#option_title").html($(this).data("title"));
		$("#option_submit").attr("data-id",$(this).data("id"));
		$("#option_submit").data().id=$(this).data("id");

	})

	$("#option_submit").click(function(){
		//console.log($(this).data("id"));
		//$(".myproduct_light_box_bg").remove();
	})
	
	$(".myproduct_light_box_bg").click(function (e) {	    	    
	    	var $tgt = $(e.target);
	    	if ($tgt.closest(".myproduct_light_box_container").length) {	    		
		    }else{
		    	$(".myproduct_light_box_bg").remove();
		    }

		    if($tgt.closest(".myproduct-close").length){
		    	$(".myproduct_light_box_bg").remove();
		    }       
	});	
	
	

	
	/*
	if(typeof(jQuery.magnificPopup !="undefined")){
		$.magnificPopup.open({
		   mainClass: 'mfp-fade myproduct_mfp',
		   type: 'inline',		
		  items: {
		    src:'<div class="mfp-close"></div>'+
	                '<h3>產品選項</h3>'+
	                '<div class="opt_content">'+html+'</div>',
		    type: 'inline'
		  }
		});
	}
	*/
}

function addItemToCart_callback(newItem,amount){


};

function addToCartById_callback(item){
	var changeto=[
		{
			"search":"#1",
			"replace":item.title,
		}
	];
	var msg = MyCartWords("addItemToCart",changeto);
	if(!$(".mycart_alertbox").length){
		$("body").append('<div class="mycart_alertbox avia_message_box avia-color-green avia-size-normal"><div class="avia_message_box_content"><i class="fas fa-cart-plus"></i> <p>'+msg+'</p></div></div>');
		setTimeout(function(){
	        $(".mycart_alertbox").fadeOut(400,function(){
		        	$(".mycart_alertbox").remove();
		        });
	    }, 1000);
	}

};

function NotEnoughStore_fn(item){

	var avaible=MyCart.GetProductDataById(item.id,"store");

	var changeto=[
		{
			"search":"#1",
			"replace":item.title,
		}
	];
	var msg = MyCartWords("NotEnoughStore",changeto);


	//if( parseInt(avaible) === 0){
		//msg+= "最高可購買數量為 "+parseInt(avaible);

		//msg=item.title+" 目前無庫存。";

	//}
	if(!$(".mycart_alertbox").length){

		msg = $('<textarea />').html(msg).text();
		alert(msg);
		/*
		$("body").append('<div class="mycart_alertbox avia_message_box avia-color-red avia-size-normal"><div class="avia_message_box_content"><i class="fas fa-cart-plus"></i> <p>'+msg+'</p></div></div>');
		setTimeout(function(){
	        $(".mycart_alertbox").fadeOut(400,function(){
		        	$(".mycart_alertbox").remove();
		        });
	    }, 1000);
		*/
	}
}

MyCart.NotGoodItem;

function FinalCheckNotGood_fn(notgood){
	$("#insert-btn").hide();

	var titles=[];
	for(var i=0;i<notgood.length;i++){
		var item = notgood[i];
		var item_index=item["index"];
		var max_amount=item["max_amount"];
		titles.push(item.title);
		$(".item-single-line").eq(item_index).css("color","red");
		items[item_index]["amount"]=max_amount;
	}

	MyCart.NotGoodItem=notgood;

	var changeto=[
		{
			"search":"#1",
			"replace":titles.join(", "),
		}
	];
	var msg = MyCartWords("NotEnoughStore",changeto);
	// $MycartLang["NotEnoughMsg"]


	$("#check-count").after("<div class='final_check_msg'><p>"+msg+"</p></div>");
	$(".final_check_msg").hide();
	$(".final_check_msg").append('<a id="go_to_shop" class="button" href="'+MyCart.setting.shop_url+'"">'+MyCartWords("BackToCart")+'</a>');
	if(items.length >0){
		$(".final_check_msg").append('<a id="insert-accept" class="button" href="javascript:void(0)"">'+MyCartWords("CheckOut")+'</a>');
	}
	$("#insert-accept").click(function(){
		//$(".final_check_msg").remove();
		$("#checkform").submit();
	});

	AjaxShippingFee_callback=function(){
		//console.log("AjaxShippingFee_callback");
		MyCart.RenderCheckList(items,shippingfee,discount);
		var notgood=MyCart.NotGoodItem;
		for(var i=0;i<notgood.length;i++){
			var item = notgood[i];
			var item_index=item["index"];
			$(".item-single-line").eq(item_index).css("color","red");
			$(".final_check_msg").show();
		}
	};

	MyCart.AjaxShippingFeeAndDiscount(items);

	

	
	
	

	


	//AjaxShippingFee_callback=function(){};

}


