<?php
$funding_go = 0;

// 預購頁面
$_perorder_page = 563; // 預購頁面
$_perorder_page = 1607; // 預購頁面
$_perorder_opt_page = 1367; //選項頁面

if(!$funding_go):
add_action("wp_head","block_for_funding_hide");
function block_for_funding_hide(){
?>
<style type="text/css">
	.block_for_funding{
		display: none;
	}
</style>
<?php
}

endif;





// add_filter("addtocart_btn","get_mail_Btn_onfunding",99,2);
function get_mail_Btn_onfunding($content, $id){
	global $_perorder_page;
	$store = GetMyStore($id);
	$funding_page_url = get_permalink($_perorder_page);
	if($store == 0){
		$content = '<a href="'.$funding_page_url.'"><div class="button addtocart dontadd">熱銷售罄，預購中</div></a>';
	}

	return $content;
}

add_action("wp_head","funding_btn_style",10);
function funding_btn_style(){
	global $_perorder_page, $_perorder_opt_page;
?>
<style type="text/css">
	a.button.funding-btn.button-black.nomargin {
	    width: 100%;
	    display: block;
	    text-align: center;
	}

	.reminder{
		width: 100%;
		max-width: 700px;
		margin: 20px auto 10px;
	}

	.reminder p{
		font-size: 16px !important;
		line-height: 1.8 !important;
	}

	.funding-block.my-product-block h3 {
	    font-size: 1.2rem !important;
	    text-align: left !important;
	}

	.funding-block.my-product-block .my-price {
	    text-align: left !important;
	    font-size: 1.2rem !important;
	}
	.fixed_preorderbtn {
	    padding: 0 40px;
	    /* position: fixed; */
	    /* top: 90px; */
	    position: sticky;
	}

	@media only screen and (max-width: 989px){
		.fixed_preorderbtn {
		    position: fixed !important;
		    display: block !important;
		    top: 90px !important;
		    opacity: 1 !important;
		    width:100% !important;
		    left:0 !important;
		}
	}
		
</style>
<?php	
	$effectpages = [$_perorder_page, $_perorder_opt_page];
	$p_id = get_the_ID();
	if(in_array($p_id, $effectpages)){
?>
<style type="text/css">
	a#fixed_cart_bottom {
	    display: none !important;
	}
</style>	
<?php		
	}
}

add_filter("single_product_block_att","function_image",10,2);
function function_image($a,$_pid){

	if(has_term( "funding","product_cate", $p_id )){
		$a['pic_size'] = "large";
		$a['class'] = "funding-block";
	}

	return $a;
} 

add_filter("addtocart_btn","funding_btn",10,2);
function funding_btn($contents,$id){
	
	$checkpage = get_option("MyCheckPage");
	$checkpage_url = get_permalink($checkpage)."?quick_check=".$id;

	if(has_term( "funding","product_cate", $id )){
	
		$contents = "<a href='{$checkpage_url}' class='button funding-btn button-black nomargin'>馬上預購</a>";
	
	}
	return $contents;
}

add_action("wp_footer","funding_page_check_footer",20);
function funding_page_check_footer(){
	if(!isset($_GET["quick_check"]) || $_GET["quick_check"] == ""){
		return;
	}else{
		$p_id = $_GET["quick_check"];
	} 

	if(!has_term( "funding","product_cate", $p_id )){
		return;
	}
	// $values  = ["快卡背包時尚款 – 終極黑","快卡背包運動款 – 碳黑色","快卡背包時尚款 – 碳黑色","快卡背包運動款 – 深藍色","快卡背包時尚款 – 深藍色","快卡背包運動款 – 鈦灰色","快卡背包時尚款 – 鈦灰色"];
	switch ($p_id) {
		case '1603':
			$values  = ["輕量快卡-三用托特 - 鈦灰色","輕量快卡-三用托特 - 碳黑色","輕量快卡-三用托特 - 亮澤黑"];
			break;
		case '1628':
			$values  = ["輕量快卡-三用托特 - 鈦灰色","輕量快卡-三用托特 - 碳黑色","輕量快卡-三用托特 - 亮澤黑"];
			break;	
		
		default:
			$values  = ["快卡背包運動款 – 深藍色","快卡背包時尚款 – 深藍色","快卡背包運動款 – 鈦灰色","快卡背包時尚款 – 鈦灰色"];
			break;
	}

	
	

	

	$titles = $values;

	$options_html = "<option disabled selected value=\'\'>請選擇方案</option>";
	foreach ($values as $key => $value) {
		$title = $titles[$key];
		$options_html .= "<option value=\'{$value}\'>{$title}</option>";
	}

	if($p_id == 1336 || $p_id == 1628){
	?>	
	<script type="text/javascript">
		$("#checkform").append('<div class="col_full"><h3>產品選項</h3><label for="billing-form-address">選擇第一個包包</label><select  class="required fund_opt"><?php echo $options_html;?></select></div><div class="col_full"><label for="billing-form-address">選擇第二個包包</label><select  class="required fund_opt"><?php echo $options_html;?></select></div>');
	</script>	
	<?php	
	}else{
	?>	
	<script type="text/javascript">
		$("#checkform").append('<div class="col_full"><h3>產品選項</h3><label for="billing-form-address">選擇第一個包包</label><select  class="required fund_opt"><?php echo $options_html;?></select></div></div>');
	</script>	
	<?php		
	}
	?>
	<script type="text/javascript">
		$("#Note1").attr("id","NoteX");
		$("#Note2").removeAttr("name");
		$("#checkform").append("<input id='Note1' value='' name='order[Note1]' type='hidden'>");
		$("#checkform").append("<input id='memo' value='' name='memo' type='hidden'>");
		$("#checkform").append("<input id='hideNote1' value=''  type='hidden'>");
		var fund_opt = [];
		$(".fund_opt").change(function(){
			fund_opt = [];
			$(".fund_opt").each(function(i){
				var value = $(this).val();
				fund_opt.push(value);
			})
		})

		$("#checkform").submit(function(){
			$("#hideNote1").val("");
			var fund_opt_str = fund_opt.join("\r\n");
			var value = fund_opt_str;  
			var Note1 = $("#NoteX").val();
			var ChangedNote = Note1 + "\r\n" + value;
			$("#Note1").val(ChangedNote);
			$("#memo").val(value);
		})

		var reminder = [
			"① 台灣本島免運費",
			"② 海外官網已支援香港，香港客人請直接下單即可。其他國家之「國際運費」請 email 詢問小編 superdouble@gmail.com",
			"③ 電子發票將於下單時提供",
			"④ 預計 10月第二週，依下單順序出貨。預購期間最後一批預購（最後預購日至10/7止）寄送，預計於 11月初發貨。",
			"⑤ 同⼀筆訂單僅能配送⾄單⼀地址",
			"⑥ 若有其他預購相關問題，請來信至：superdouble@gmail.com",
			"⑦ 預購即表示購買並享有預購優惠價。恕下訂後無法更改款式與顏色。"
		];
		$(".check-list-container").before("<div class='reminder'><h3>重要提醒:</h3>"+"<p>"+reminder.join("<br>")+"</p>"+"</div>");
	</script>
	<?php
}


add_shortcode("funding_fixed_btn" ,"funding_fixed_btn");
function funding_fixed_btn(){
	global $_perorder_page, $_perorder_opt_page;
	$_link = get_permalink($_perorder_opt_page);
	return "<div class='fixed_trigger'></div><div class=' fixed_preorderbtn'><a href='{$_link}' class= 'button funding-btn button-black nomargin'>馬上預購</a></div>";
}
add_action("wp_footer","funding_page_pre",20);
function funding_page_pre(){
	global $_perorder_page, $_perorder_opt_page;
	if(is_page()){
		$p_id = get_the_ID();
		if($p_id == $_perorder_page){
			// $_link = get_permalink(1367);
?>
	<script>
		var _width = $(".fixed_preorderbtn").width();
		var _top = 50;
		function pin_preordr_button(){
			var _w_width = $(window).width();
			if( _w_width < 990){
				return;
			}
		    $(".fixed_preorderbtn").css({"position":"fixed","top": _top +"px","display":"block"});
		    	   		
		}

		function unpin_preordr_button(){
			var _w_width = $(window).width();
			if( _w_width < 990){
				return;
			}
			
			$(".fixed_preorderbtn").fadeOut();
			$(".fixed_preorderbtn").css({"position":"static","top": "0px","display":"none"});
			// $(".fixed_preorderbtn").removeAttr("style");		    
		}

		$(".fixed_preorderbtn").width(_width);	

		$(window).scroll(function(){
		  	
		  var _triger = $(".fixed_trigger").offset().top;
		  var wtop = $(window).scrollTop();
		  console.log(wtop,(_triger - _top));
		  if(wtop >=  (_triger - _top)){
		    pin_preordr_button();
		  }else{
		  	unpin_preordr_button();
		  } 
			
		})
		$(window).load(function(){
			$(window).scroll();
		})
		
	</script>	
<?php			
		}
	}
	
}
