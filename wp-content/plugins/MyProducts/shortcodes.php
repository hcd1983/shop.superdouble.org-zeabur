<?php

function get_total_store_by_id($p_id){
	$args = array(
    			'post_parent' => $p_id,
    			'post_type'=>'myproducts',
    			'field'=>'ids',
			);	

	$children = get_children( $args,"ARRAY_A" );

	if ( !empty($children)) {
		
		$store = 0;

		foreach ($children as $key => $product) {
			$store += GetMyStore($product["ID"]);
		}

		return $store;

	}else{
		
		return GetMyStore($p_id);
	}

	
}
function getFundingCartUrl($id=""){

	$post_type = "myproducts";
	$MyFundingCartPage = get_option('MyFundingCartPage');
	$href = get_permalink($MyFundingCartPage);

	if($id ==""){
		return $href;
	}else{
		$post_type = get_post_type($id);
	}

	if($post_type != "myproducts"  ){
		return $href;
	}

	$values = array("product"=>$id);
	$href = UrlWithGetVal($href,$values);
	return $href;
}

add_shortcode( "product_title" , "product_title" );
function product_title($atts){
	$default=array(
		'id'=>'',
    );

    $a = shortcode_atts($default , $atts );

    return get_the_title($a["id"]);
}

add_shortcode("cart_single_box_product","cart_single_box_product");
function cart_single_box_product(){
	
	$pid=get_the_ID();
	
	if(get_post_type($pid)!="myproducts" && is_single($pid)==false){		
		return;
	}

	$title = get_the_title();
	$permalink = get_permalink();

	$mailbody = "{$title}
{$permalink}
	";

	$mailbody = html_entity_decode($mailbody);

	// $mailbody = urlencode($mailbody);

	$mailbody = str_replace("\r\n", "%0D%0A",$mailbody);

	ob_start();
?>
<div class="cart_single_box">
	<h2><?php echo $title;?></h2>
	<div class="price"><?php echo do_shortcode("[showprice]");?></div>
	<div class="inbox_cart_btn"><?php echo do_shortcode("[addtocart]");?></div>
	<div class="the_share">
	    <ul class="cs-shareCotntent list-unstyled u-desk-only">
	         
	        <li>
	            <a class="line__btn" target="_blank" href="https://social-plugins.line.me/lineit/share?url=https://www.citiesocial.com/products/product-14360%3Futm_source%3Dcsw%26utm_medium%3Dline-desktop-share">
	                <img src="https://cdn.shopify.com/s/files/1/0254/0393/files/line_b0dc4a2c-dc6e-4b35-afb6-2a422c0dce19.png?7385838715848853780">
	            </a>  
	        </li>
	        <li>
	            <a class="facebook__btn" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $permalink;?>">
	                <img src="https://cdn.shopify.com/s/files/1/0254/0393/files/Facebook_f484f81d-3688-41c2-a768-44ce7608168f.png?7385838715848853780">
	            </a>
	        </li>
	        <li>
	            <a class="facebook_msg__btn" target="_blank" href="http://www.facebook.com/dialog/send?app_id=381730835916232&amp;link=<?php echo $permalink;?>&amp;redirect_uri=<?php echo $permalink;?>">
	                <img src="https://cdn.shopify.com/s/files/1/0254/0393/files/Messenger.png?12086172512739684500">
	            </a>
	        </li>
	        <li>
	            <a class="mailto__btn" href="mailto:?subject=%E9%80%99%E6%AC%BE%E5%95%86%E5%93%81%E7%9C%9F%E4%B8%8D%E9%8C%AF%E6%8E%A8%E8%96%A6%E7%B5%A6%E4%BD%A0~&amp;body=<?php echo $mailbody;?>">
	                <img src="https://cdn.shopify.com/s/files/1/0254/0393/files/mail.png?6670301370876845268">
	            </a>
	        </li>   
	        
	            
	    </ul>  
	</div>	
</div>	
<?php	
	$content = ob_get_contents();
	ob_clean();
	return $content;
}

add_filter("GetProductInfo","child_price",10,2);
function child_price($ProductsInfo,$p_id){
	if (  is_admin() ){
		return $ProductsInfo;
	}
	$parent_id = wp_get_post_parent_id( $p_id );
	if(!$parent_id){
		return $ProductsInfo;
	}else{
		
		if(!$ProductsInfo["price"] || !$ProductsInfo["saleprice"]){
			$ParentProductsInfo = get_post_meta($parent_id,"ProductsInfo",true);

			if(!$ProductsInfo["price"] && !$ProductsInfo["saleprice"]){
				$ProductsInfo["onsale"] = $ParentProductsInfo["onsale"];
			}

			if(!$ProductsInfo["price"]){
				$ProductsInfo["price"] = $ParentProductsInfo["price"];
			}

			if(!$ProductsInfo["saleprice"]){
				$ProductsInfo["saleprice"] = $ParentProductsInfo["saleprice"];
			}
			
			
		}

	}

	return $ProductsInfo;
}



function addtocart_selector($childId_array,$Parent_ProductInfo,$class,$content){


	$children = $childId_array;

	//var_dump($children);
	if ( !empty($children)) {

		
		
		$childId_arr=array();
		$default_id = "";
		$default_title = "";
		$default_price = 0;

		$price_list = [];
		$html_price = [];
		$html_noprice = []; 
		$default_value = "請選擇尺寸或款式";
		$html_price[] =  "<option value='not_select'>".$default_value."</option>";	
		$html_noprice[] =  "<option value='not_select'>".$default_value."</option>";	
		$all_store = 0;
		foreach ($children as $key => $product) {		

			$childId_arr[] = $product["ID"];
			$p_id = $product["ID"];
			$value = $p_id;
			$title = get_the_title($p_id);

			$ProductsInfo = get_post_meta($p_id,"ProductsInfo",true);
			$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$p_id);
			
			

			if($ProductsInfo["onsale"] == 1 ){
		  		$the_price = $ProductsInfo["saleprice"];
			}else{
				$the_price = $ProductsInfo["price"];
			}

			

			$price_list[]= $the_price;

			$the_price = PriceFormat($money_logo="",$the_price);
			$title_price = $title." ($the_price)";

			$store = GetMyStore($p_id);
			$all_store += $store;
			if($store == 0){
				$title_price = $title. " (無庫存)";
				$title = $title. " (無庫存)";
				$disabled = "disabled";
			}else{
				$disabled = "";
			}

			$title_price = apply_filters("title_price",$title_price,$p_id,$store);
			$title = apply_filters("title_no_price",$title,$p_id,$store);

			$html_price[] = "<option value='{$value}' {$disabled}>{$title_price}</option>";			
			$html_noprice[] = "<option value='{$value}'{$disabled}>{$title}</option>";
		}

		usort($price_list, function($a,$b){
			return $a-$b;
		});

		$ind=count($price_list)-1;
		$small_val = $price_list[0];
		$big_val = $price_list[$ind];

		

		$html = "<div class='product_selector_block'>";

		if($all_store == 0){
			$class.= " nofunction";
			$error = true;
			$nostore = true;
			$content = "售完";
		}else{
			$html .= "<select class='product_selector' id='product_selector_{$id}'>";

			if($small_val == $big_val){
				$html .= join("\r\n",$html_noprice);			
			}else{
				$html .= join("\r\n",$html_price);
			}
		}
		
		$html .= "</select>";

		

		$content = apply_filters("addtocart_content",$content,$error,$nostore);

		$html .= "<div class='{$class} selector_btn'>{$content}</div>";
		$html .= "</div>";
		return $html;
	}else{
		return "";
	}

   
}


add_shortcode( "addtocart" , "addtocart" );
function addtocart($atts){

	$addtocart_word=MyCartWords("addtocart");
	$nostore_word=MyCartWords("nostore");
	$notopen_word=MyCartWords("notopen");
	$FundingMode=get_option("FundingMode");
	$MyFundingCartPage = get_option('MyFundingCartPage');
	$product_selector_single = get_option('product_selector_single');
	$product_selector_list = get_option('product_selector_list');

	if($MyFundingCartPage  == ""){
		$FundingMode=0;
	}

	$default=array(
		'id'=>'',
        'class' => 'button',
        'title' => '',
        'content' => $addtocart_word,
    );


	if(!isset($atts["id"])){
		$p_id=get_the_ID();
		$post_type=get_post_type($p_id);
		if($post_type=="myproducts"){
			$default["id"]=$p_id;
		}else{
			return;
		}
	}


		
	$a = shortcode_atts($default , $atts );

	$ProductInfo =  get_post_meta( $a["id"], $key = "ProductsInfo", true );

	$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$a["id"]);
	
	$store = GetMyStore($a["id"]);

	$the_price = $ProductInfo["price"];
			
	
	if($store===0){
		$a["content"]=$nostore_word;
	}

	$args = array(
    			'post_parent' => $a["id"],
    			'post_type'=>'myproducts',
    			'field'=>'ids',
			);	

	$children = get_children( $args,"ARRAY_A" );
	$class_array=explode(" ", $a["class"]);
	//var_dump($children);
	if ( !empty($children)) {
		$childId_arr=array();
		foreach ($children as $key => $product) {
			$childId_arr[]=$product["ID"];
		}

		$childId="data-childid='".join(",",$childId_arr)."'";
	}else{
		$childId="";
		if($ProductInfo["onsale"] == 1 || $ProductInfo["price"]==""){
	  		$the_price=$ProductInfo["saleprice"];
		}

		if($the_price==""){
			$a["content"]=$notopen_word.$the_price;
			$class_array[]="price_error";
			$class_array[]="dontadd";
		}

	}


	
	if(!in_array("addtocart", $class_array)){
		if($FundingMode == 0):
			$class_array[]="addtocart";
		endif;	
	}

	if($store==0){
		$class_array[]="nostore";
		$class_array[]="dontadd";		
	}


	$a["class"]=join(" ",$class_array);

	if($a["title"]==""){
		$a["title"]=get_the_title($a["id"]);
	}

	if(in_array("nostore", $class_array)){
		$nostore = true;
	}else{
		$nostore = false;
	}

	if(in_array("dontadd", $class_array)){
		$error=true;
	}else{
		$error=false;
	}

	$a["content"] = apply_filters("addtocart_content",$content=$a["content"],$error,$nostore);
	ob_start();

	if($FundingMode == 1):
		
		if($error==true || $nostore == true){
			$herf="javascript:void(0)";
		}else{

			$href = getFundingCartUrl($a['id']);
			//$href = get_permalink($MyFundingCartPage);
			//$values = array("product"=>$a['id']);
			//$href = UrlWithGetVal($href,$values);
		}

?>
	<a class='<?php echo $a["class"];?>' href="<?php echo $href;?>"><?php echo $a["content"];?></a>

<?php
	else:
	if($childId && $product_selector_single == 1){
		echo addtocart_selector($children ,$ProductInfo,$a["class"],$a["content"]);
	}else{
?>
	
	<div class='<?php echo $a["class"];?>' <?php echo $childId;?> data-id="<?php echo $a['id'];?>" data-title="<?php echo $a["title"];?>"><?php echo $a["content"];?></div>

<?php

	}	
	
	endif;	    
    $contents = ob_get_contents();
    ob_end_clean();
    return apply_filters("addtocart_btn",$contents, $a["id"]);
}

add_shortcode( "addtocart_cat" , "addtocart_cat" );
function addtocart_cat($atts){
	$addtocart_word=MyCartWords("addtocart");
	$default=array(
		'cat'=>'',
        'class' => 'button',
        'title' => '',
        'content' => $addtocart_word,
    );
		
	$a = shortcode_atts($default , $atts );


	$tax_query=array('relation' => 'OR');
	if(!isset($a["cat"]) || $a["cat"]==""){		
		return;
	}else{
		$cates=explode(",", $a["cat"]);
		$tax_query[]=array(
			'taxonomy' => 'product_cate',
			'field'    => 'slug',
			'terms'    => $cates,
		);	
	}

	$args = array(
		'post_type' => 'myproducts',
		'posts_per_page'=>-1,
		'post_parent'=>0,
		'fields'=>'ids',
		'tax_query' => $tax_query,
		'orderby'=>'menu_order',
		'order'=>'ASC'
	);


	$posts=get_posts($args);
	

	//var_dump($children);
	if ( !empty($posts)) {
		$posts_arr=array();
		foreach ($posts as $key => $post) {
			
			$posts_arr[]=$post;
		}
		$childId="";
		$childId="data-childid='".join(",",$posts_arr)."'";
	}else{
		return;
	}


	$class_array=explode(" ", $a["class"]);
	if(!in_array("addtocart", $class_array)){
		$class_array[]="addtocart";
	}
	$a["class"]=join(" ",$class_array);

	ob_start();
?>
	<div class='<?php echo $a["class"];?>' <?php echo $childId;?> data-id="<?php echo $posts_arr[0];?>" data-title="<?php echo $a["title"];?>"><?php echo $a["content"];?></div>

<?php	    
    $contents = ob_get_contents();
    ob_end_clean();
    return apply_filters("addtocart_btn_content",$contents,$a["id"]);
}

add_shortcode( "linktoproduct" , "linktoproduct" );
function linktoproduct($atts){
	$viewproduct_word=MyCartWords("viewproduct");
	$default=array(
		'id'=>'',
        'class' => 'button view-product',
        'title' => '',
        'content' => $viewproduct_word,
    );
	if(!isset($atts["id"])){		
		return;		
	}
		
	$a = shortcode_atts($default , $atts );

	if(get_post_type($a["id"]) != "myproducts"){
		return;
	}

	$ProductsInfo = get_post_meta( $a["id"], "ProductsInfo", $single = true );

	if(!isset($ProductsInfo["intro_link"]) || $ProductsInfo["intro_link"]==""){
    	$ProductsInfo["intro_link"] = 1;
  	}



	
	$class_array=explode(" ", $a["class"]);
	if(!in_array("view-product", $class_array)){
		$class_array[]="view-product";
	}

	if($ProductsInfo["intro_link"]  != 1){
  		$link = "javascript:void(0)";
  		$class_array[]="dont-display";
  		
  	}else{
  		$link = get_permalink($a['id']);
  	}

	$a["class"]=join(" ",$class_array);

	ob_start();
?>
	<a class="my-product-link" href="<?php echo $link;?>">
		<div class='<?php echo $a["class"];?>'><?php echo $a["content"];?></div>
	</a>
<?php	    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

add_shortcode( "getprice" , "getprice" );
function getprice($atts){
	$money_logo=GetTheMoneyLogo();

	$default=array(
		'id'=>'',
    );
	if(!isset($atts["id"])){
		$p_id=get_the_ID();
	}
	
	$post_type=get_post_type($p_id);

	if($post_type=="myproducts"){
		$default["id"]=$p_id;
	}else{
		return;
	}

	$a = shortcode_atts($default , $atts );

	$args = array(
    			'post_parent' => $a["id"],
    			'post_type'=>'myproducts',
    			'field'=>'ids',
			);				
	$children = get_children( $args,"ARRAY_A" );

	

	if ( !empty($children) && count($children) >= 1) {
		
		$price_list=array();
		
		foreach ($children as $key => $product) {
			$the_id=$product["ID"];
			$ProductsInfo=get_post_meta($the_id,"ProductsInfo",true);
			$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$the_id);

			$the_price=$ProductsInfo["price"];
			
			if($ProductsInfo["onsale"] == 1 ){
		  		$the_price=$ProductsInfo["saleprice"];
			}

			$price_list[]=$the_price;
			
		}


		usort($price_list, function($a,$b){
			return $a-$b;
		});
		$ind=count($price_list)-1;
		$small_val=$price_list[0];
		$big_val=$price_list[$ind];
		if($small_val==$big_val){
			$price=PriceFormat($money_logo,$small_val);
		}else{
			$price=PriceFormat($money_logo,$small_val)."<span>~</span>".PriceFormat($money_logo,$small_val) ;
		}


	}else{

		$ProductsInfo=get_post_meta($a["id"],"ProductsInfo",true);
		$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$a["id"]);
		  //var_dump($ProductsInfo);
		$price=PriceFormat($money_logo,$ProductsInfo["price"]);
		  
		if($ProductsInfo["onsale"] == 1 ){
		  		//$price=$money_logo.number_format($ProductsInfo["saleprice"]);
				//$price="<del>".$money_logo.number_format($ProductsInfo["price"])."</del> ".$price;
		  		$price=PriceFormat($money_logo,$ProductsInfo["saleprice"]);		  		
		  		//$price="<del>".PriceFormat($money_logo,$ProductsInfo["price"])."</del> ".$price;
		}

		
	}
	

	return $price;	
}

add_shortcode( "showprice" , "showprice" );
function showprice($atts){

	$money_logo=GetTheMoneyLogo();

	$default=array(
		'id'=>'',
        'class' => 'my-price',
    );
	if(!isset($atts["id"])){
		$p_id=get_the_ID();
		$post_type=get_post_type($p_id);
		if($post_type=="myproducts"){
			$default["id"]=$p_id;
		}else{
			return;
		}
	}


		
	$a = shortcode_atts($default , $atts );

	

	$args = array(
    			'post_parent' => $a["id"],
    			'post_type'=>'myproducts',
    			'field'=>'ids',
			);				
	$children = get_children( $args,"ARRAY_A" );

	

	if ( !empty($children) && count($children) >= 1) {
		
		$price_list=array();
		$bigest_price = 0;
		$group_onsale = 0;
		foreach ($children as $key => $product) {
			$the_id=$product["ID"];
			$ProductsInfo=get_post_meta($the_id,"ProductsInfo",true);
			$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$the_id);
			$the_price=$ProductsInfo["price"];
			
			if( $the_price > $bigest_price){
				$bigest_price = $the_price;
			}

			if($ProductsInfo["onsale"] == 1 ){
				$group_onsale = 1;
		  		$the_price=$ProductsInfo["saleprice"];
			}

			$price_list[]=$the_price;
			
		}


		usort($price_list, function($a,$b){
			return $a-$b;
		});
		$ind=count($price_list)-1;
		$small_val = $price_list[0];
		$big_val = $price_list[$ind];
		if($small_val==$big_val){

			$price=PriceFormat($money_logo,$small_val);
			if($group_onsale == 1){
				$price="<del>".PriceFormat($money_logo,$bigest_price)."</del> ".$price;
			}

		}else{
			$price=PriceFormat($money_logo,$small_val)."<span>~</span>".PriceFormat($money_logo,$big_val) ;
		}


	}else{

		$ProductsInfo=get_post_meta($a["id"],"ProductsInfo",true);
		$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$a["id"]);
		  //var_dump($ProductsInfo);
		$price=PriceFormat($money_logo,$ProductsInfo["price"]);
		  
		if($ProductsInfo["onsale"] == 1 ){
		  		//$price=$money_logo.number_format($ProductsInfo["saleprice"]);
				//$price="<del>".$money_logo.number_format($ProductsInfo["price"])."</del> ".$price;
		  		$price=PriceFormat($money_logo,$ProductsInfo["saleprice"]);		  		
		  		$price="<del>".PriceFormat($money_logo,$ProductsInfo["price"])."</del> ".$price;
		}

		
	}

	if($price===$money_logo){
		$price="";
		$a["class"].=" "."no-price";
	}


    return "<div class='".$a["class"]."'>".$price."</div>";
}


add_shortcode( "single_product_block" , "single_product_block" );
function single_product_block($atts){

	$addtocart_word=MyCartWords("addtocart");

	$default=array(
		'id'=>'',
        'class' => 'my-product-block',
        'btn_class' => 'button',
        'btn_content' => $addtocart_word,
        'price_class'=>'my-price',
        'pic_size'=>'myproduct',

    );

	if(!isset($atts["id"])){
		$p_id=get_the_ID();
		$post_type=get_post_type($p_id);
		if($post_type=="myproducts"){
			$default["id"]=$p_id;
		}else{
			return;
		}
	}
		
	$a = shortcode_atts($default , $atts );

	$a = apply_filters("single_product_block_att",$a,$p_id);

	$my_tax = get_the_terms( $a["id"] , "product_cate" );

	$a["tax"]=array();

	if(is_array($my_tax) && count($my_tax) > 0){
		foreach ($my_tax as $key => $tax) {
			$a["tax"][]=$tax->term_id;
		}
	}

	$atts_btn = array(
		'id'=>$a["id"],
        'class' => $a["btn_class"],
        'content' => $a["btn_content"]
    ); 

    $atts_price = array(
		'id'=>$a["id"],
        'class' => $a["price_class"],
    );

    $atts_link=array(
    	'id'=>$a["id"],
    );

    $class_array=explode(" ", $a["class"]);
	if(!in_array("my-product-block", $class_array)){
		$class_array[]="my-product-block";
	}
	$a["class"]=join(" ",$class_array);
	$a["taxes"]=join(' ',$a['tax']);

	$image = get_the_post_thumbnail( $a["id"], $a["pic_size"] );
	$thumb2_ID=MultiPostThumbnails::get_post_thumbnail_id('myproducts', 'secondary-image', $a["id"]);

	if($thumb2_ID){
		$image .= wp_get_attachment_image( $thumb2_ID, $size = $a["pic_size"],$icon = false,  $attr = ["class" =>"thumb_2"] );
	}
 
	$image = apply_filters("product-thumnail-html",$image,$a["id"]);

	$html="<div class='".$a["class"]."' data-tax='".$a["taxes"]."'>
			<div class='product-thumnail'>".$image."</div>
			<h3>".get_the_title($a["id"])."</h3>
			".showprice($atts_price)."
			<div class='product-des'>
			".nl2br(get_the_excerpt( $a["id"] ))."
			</div>
			".linktoproduct($atts_link)."
			".addtocart($atts_btn)."			
		   </div>
	";

	return apply_filters("MySingleProductBlock",$html,$id=$a["id"]);

}

add_shortcode( "my_product_list" , "my_product_list" );
function my_product_list($atts){
	global $post;
	$default=array(
		'class'=>'',
		'posts_per_page'=>-1,
		'post_parent'=>0,
		'cat'=>'',
		'tag'=>'',
		'paginate'=>0,
		'row'=>4,
	);


	$a = shortcode_atts($default , $atts );

	$a["row"]=$a["row"]>=5?5:$a["row"];

	$tax_query=array(
			'relation' => 'OR'
		);

	if($a["cat"] !=''){
		$cates=explode(",", $a["cat"]);
		$tax_query[]=array(
			'taxonomy' => 'product_cate',
			'field'    => 'slug',
			'terms'    => $cates,
		);		
	}

	if($a["tag"] !=''){
		$tags=explode(",", $a["tag"]);
		$tax_query[]=array(
			'taxonomy' => 'product_tag',
			'field'    => 'slug',
			'terms'    => $tags,
		);		
	}



	$args = array(
		'post_type' => 'myproducts',
		'posts_per_page'=>$a["posts_per_page"],
		'post_parent'=>$a["post_parent"],
		'tax_query' => $tax_query,
		'orderby'=>'menu_order',
		'order'=>'ASC'
	);

	


	/*
	$args = array(
		'post_type' => 'myproducts',
		'posts_per_page'=>2,
		'post_parent'=>93,
		'tax_query' => array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'product_cate',
				'field'    => 'slug',
				'terms'    => array('accessory'),
			),
			array(
				'taxonomy' => 'product_tag',
				'field'    => 'slug',
				'terms'    => array('hit'),
			)
		),
	);
	*/

	$html="<div class='my-product-list ".$a["class"]." list-row-".$a["row"]."'>";
	$query = new WP_Query( $args );
	
 	if ( $query->have_posts() ) : 
	$i=1;

 	while ( $query->have_posts() ) : 

 		$query->the_post(); 
 		//$post=get_the_post();
 		//var_dump($post);
 		//$pid=get_thie_ID();
 		//var_dump($post);
 		$atts=array(
 			"id"=>$post->ID,
 		);
 		$html.=single_product_block($atts);

 		if($i%$a["row"]==0){
 			$html.="<div class='clear' style='height:20px;'></div>";
 		}else{
 			$html.="<div class='gap'></div>";
 		} 		
 		$i++;
 	?>


 	<?php endwhile; 
 		$html.="<div class='clear'></div>";
 		if($a["paginate"]==1):
	 		$html.=paginate_links( array(
	            'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
	            'total'        => $query->max_num_pages,
	            'current'      => max( 1, get_query_var( 'paged' ) ),
	            'format'       => '?paged=%#%',
	            'show_all'     => false,
	            'type'         => 'plain',
	            'end_size'     => 2,
	            'mid_size'     => 10,
	            'prev_next'    => true,
	            'prev_text'    => sprintf( '<i></i> %1$s', "<<" ),
	            'next_text'    => sprintf( '%1$s <i></i>', ">>" ),
	            'add_args'     => false,
	            'add_fragment' => '',
	        ) );
	 	endif;
 		wp_reset_postdata();
	else : 
 		$html.="No data";
    endif; 
    $html.="</div>";
    return $html;
}



add_shortcode( "mycheckpage" , "mycheckpageform" );
function mycheckpageform(){
	ob_start();
?>
	<div class="check-list-container"><div class="my-cart-loading">Loading</div></div>
<?php	    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}


add_shortcode( "mycartpage" , "mycartpagetable" );

function mycartpagetable(){

	ob_start();
?>
	<div class="cart-list-container"><div class="my-cart-loading">Loading</div></div>	
<?php		    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}


add_shortcode( "mycouponform" , "mycouponform" );
function mycouponform(){
	$MyOpts=get_option('MyCoupon');
	if(!isset($MyOpts["active"]) || $MyOpts["active"]==0){
		return;
	}
	$useCouponWord=MyCartWords("useCoupon");
	$applyCouponWord = MyCartWords("applyCoupon");
	ob_start();
?>
<div class="clear"></div>
<div id="coupon_block">
	<h3><?php echo $useCouponWord;?></h3>
	<div class="col_half">
		<input id="couponStr" type="text" value="" class="sm-form-control" placeholder="<?php echo MyCartWords("CouponPlaceholder");?>" />
	</div>
	<div class="col_half">	
		<button onclick="MyCart.applyCoupon()" class="button button-3d button-black nomargin"><?php echo $applyCouponWord;?></button>
	</div>
	<div class="clear"></div>	
</div>
<?php		    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}
//*============================country selector===============================================
function MyCountrySelector($class="required",$placeholder=true){
	$plsSelectCountryWord=MyCartWords("plsSelectCountry");
	$countries=GetCountryOptions();
	ob_start();
	if(empty($countries)){
		echo '<input type="text" id="the_country" name="country" value="" class="sm-form-control" readonly/>';
	}else{
	?>
		<select id="the_country" name="country" class="<?php echo $class;?>" <?php if(count($countries)==1){echo "readonly";}?> >
	<?php
		if($placeholder && count($countries) > 1){
			echo '<option value="" disabled selected>'.$plsSelectCountryWord.'</option>';
		}

		foreach ($countries as $key => $value) {
			
			if(count($countries)==1){
				echo '<option value="'.$value["CountryCode"].'" selected>'.$value["CountryName"].'</option>';
			}else{
				echo '<option value="'.$value["CountryCode"].'">'.$value["CountryName"].'</option>';
			}
			
		}	
	?>		
		</select>	
	<?php	
	}
	$contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}


//*============================shipping selector===============================================
function MyShippingSelector($class="",$placeholder=true){
	$ShippingOptionsWord=MyCartWords("ShippingOptions");
	$shippingOtps=GetShippingOptions();
	ob_start();
	if(empty($shippingOtps)){
		echo '<input type="hidden" id="shipping_method" name="shipping_method" value="" class="sm-form-control" readonly/>';
	}else{
	?>
		<select id="shipping_method" name="shipping_method" class="<?php echo $class;?>" <?php if(count($shippingOtps)==1){echo "style='display:none !important;'";}?> >
	<?php
		
		// if($placeholder && count($shippingOtps) > 1){
		// 	echo '<option value="" disabled selected>'.$ShippingOptionsWord.'</option>';
		// }

		foreach ($shippingOtps as $key => $value) {
			
			if(count($shippingOtps)==1){
				echo '<option value="'.$value["value"].'" selected>'.$value["text"].'</option>';
			}else{
				if($key == 0){
					$selected == "selected";
				}else{
					$selected = "";
				}
				echo '<option value="'.$value["value"].'" {$selected}>'.$value["text"].'</option>';
			}
			
		}	
	?>		
		</select>	
	<?php	
	}
	$contents = ob_get_contents();
    ob_end_clean();

    return $contents;
}

//============================check form ===================================================
add_shortcode( "mycheckform" , "mycheckform" );
function mycheckform(){
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

	$MyInsertPort=get_option('MyInsertPort');
	$MyInsertPort=$MyInsertPort==""?"#":$MyInsertPort;
	
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
			</div>

			<div class="col_half col_last" <?php echo $shippingOtpsStyle; ?>>
				<label for="billing-form-address"><?php echo MyCartWords("ShippingOptions");?>:</label>
				<?php echo MyShippingSelector();?>								
			</div>
			<div class="clear"></div>

			<div class="col_half">
				<label for="ReceiverName"><?php echo $FormWords["name"];?>:<small>*</small></label>
				<input type="text" id="ReceiverName" name="order[ReceiverName]" value="" class="sm-form-control required" />
			</div>

			<div class="col_half col_last">
				<label for="ReceiverTel"><?php echo $FormWords["phoneNumber"];?>:<small>*</small></label>
				<input type="text"  maxlength="10" id="ReceiverTel" name="order[ReceiverTel]" value=""  class="sm-form-control tel required" />
			</div>

			<div class="clear"></div>								

			<div class="col_one_fourth">
				<label for="zip"><?php echo $FormWords["zip"];?>:<small>*</small></label>
				<input type="text" id="zip" name="buyer[zip]" value="" maxlength="5"  class="sm-form-control required" />
			</div>

			<div class="col_three_fourth col_last">
				<label for="address"><?php echo $FormWords["address"];?>:<small>*</small></label>							
				<input type="text"  id="address" name="buyer[address]" value="" class="sm-form-control required" />
			</div>
			<div class="clear"></div>

			<div class="col_full">
				<label for="ReceiverEmail"><?php echo $FormWords["email"];?>:<small>*</small></label>
				<input type="email" id="ReceiverEmail" name="order[ReceiverEmail]" value="" class="sm-form-control required email" />
			</div>

			<div class="col_half">
				<label for="receipt"><?php echo $FormWords["taxIdNumber"];?>:</label>
				<input type="text"  maxlength="8" minlength="8"  id="receipt" name="buyer[receipt]" value="" class="sm-form-control" />
			</div>

			<div class="col_half col_last">
				<label for="company"><?php echo $FormWords["CompanyName"];?>:</label>
				<input type="text"  id="company" name="buyer[company]" value="" class="sm-form-control" />
			</div>

			<div class="clear"></div>
			<div class="col_full">
				<label for="shipping-form-paytype"><?php echo $FormWords["PayType"];?></label>		
				<div>
<?php
			$MyPaytype = get_option("MyPaytype")===false?["01","03","05"]:get_option("MyPaytype");
			foreach ($MyPaytype as $key => $val) {
				if($key == 0){
					$checked = 'checked="checked"';
				}else{
					$checked = '';
				}

				switch ($val) {
					case '01':
					?>
					<input id="radio-4" class="radio-style" name="order[PayType]" type="radio" value="01" <?php echo $checked;?>>
					<label for="radio-4" class="radio-style-2-label radio-small"><?php echo $FormWords["CreditCard"];?></label>
					<?php		
						break;

					case '11':
					?>
					<input id="radio-8" class="radio-style" name="order[PayType]" type="radio" value="11" <?php echo $checked;?>>
					<label for="radio-8" class="radio-style-2-label radio-small">信用卡分期</label>
					<?php		
						break;		

					case '03':
					?>
					<input id="radio-5" class="radio-style" name="order[PayType]" type="radio" value="03"  <?php echo $checked;?>>
					<label for="radio-5" class="radio-style-2-label radio-small"><?php echo $FormWords["ATM"];?></label>
					<?php		
						break;

					case '05':
					?>
					<input id="radio-7" class="radio-style" name="order[PayType]" type="radio" value="05" <?php echo $checked;?>>
					<label for="radio-7" class="radio-style-2-label radio-small"><?php echo $FormWords["Ibon"];?></label>
					<?php		
						break;

					case '10':
					?>
					<input id="radio-6" class="radio-style" name="order[PayType]" type="radio" value="10" <?php echo $checked;?>>
					<label for="radio-6" class="radio-style-2-label radio-small">超商條碼</label>
					<?php		
						break;			
					
					default:
						# code...
						break;
				}
			}
?>											
					
					
					
					
				</div>
			</div>	
			<div class="col_full">
				<label for="Note1"><?php echo $FormWords["note"];?></label>
				<textarea class="sm-form-control" id="Note1" name="order[Note1]" rows="6" cols="30"></textarea>
			</div>
	<?php
		if(get_option('DifferentReceiver') == 1 ):			
	?>
		<div class="col_full" style="margin:20px 0;">
			<input id="useReceiver" name="receiver" type="checkbox" value="1" onclick="DifferentReceiver();">
			<label style="font-size: 16px;">若您收件資訊與上方訂購資訊 <strong style="color:red;">不同</strong> 請勾選，並填妥以下資料。</label>			
		</div>
		<div id="DifferentReceiver" style="display: none;">
			<h3>收貨人資料</h3>
			<div class="col_half">
				<label>姓名</label>
				<input name="receiverdata[rname]" type="text" value="" class="required_when_check">
			</div>
			<div class="col_half col_last">				
    			<label>電話</label>
    			<input name="receiverdata[rphone]" type="text" value="" class="required_when_check">
    		</div>
    		<div class="clear"></div>

   			<div class="col_half">
		    	<label>郵遞區號</label>
		    	<input name="receiverdata[rzip]" type="text" value="" class="required_when_check">		    	
		    </div>
		    <div class="clear"></div>
		    <div class="col_full">
		    	<label>地址</label>
		    	<input name="receiverdata[raddress]" type="text"  value=""  class="required_when_check">
		    </div>
		    <div class="col_full">	
		    	<label>E-Mail</label>
		    	<input name="receiverdata[remail]" type="text" value="" class="required_when_check" > 
		    </div>		
		</div>	
		   	

	<?php
		endif;
	?>
		<!--</form>-->
	</div>
	
	<input type="hidden" id="countryname" name="countryname" value=""  />
	<input type="hidden" id="return_url" name="return_url" value="<?php echo $thanks_url;?>"  />
	<input type="hidden" id="OrderInfo" name="order[OrderInfo]" value=""  />
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

	function DifferentReceiver(){
		
		if($("#useReceiver").length == 0){
			return;
		}

		if( $("#useReceiver").prop("checked") == true){
			$("#DifferentReceiver").show();
			$("input.required_when_check").addClass("required");
		}else{
			$("#DifferentReceiver").hide();
			$("input.required_when_check").removeClass("required");
		}
	}


	DifferentReceiver();



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
		console.log(receipt.length);

		if(receipt.length==0 || receipt.length==8){
			$("#receipt").removeAttr("style");
		}else{
			alert("<?php echo $FormWords["error_taxId"];?>");
			$("#receipt").css("border","2px solid red");
			return false;				
		}

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
    return apply_filters("checkoutFormWithValidation",$contents);
}



add_shortcode( "paynowinfo" , "show_paynowinfo" );
function show_paynowinfo($atts){
	
	$nobuyerinfo_word=MyCartWords("nobuyerinfo");

	if(!isset($_REQUEST["super"]) && (!isset($_REQUEST["email"]) && !isset($_REQUEST["OrderNo"]))){
		return "<h3>".$nobuyerinfo_word."</h3>";
	}

	//$pid=get_option('MyOrderApiUrl');
	$url=get_option('MyOrderApi');
	$url=apply_filters( 'MyOrderApiUrl', $url);

	if(isset($_REQUEST["super"]) && $_REQUEST["super"] !=""){
			$url.="?super=".$_REQUEST["super"];
	}

	if(isset($_REQUEST["email"]) && isset($_REQUEST["OrderNo"])){
		$url.="?email=".$_REQUEST["email"]."&OrderNo=".$_REQUEST["OrderNo"];
	}

	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	return urldecode($info["paynow"]);
}

add_shortcode( "buyerinfo" , "show_buyerinfo" );
function show_buyerinfo($atts){

	
	$nobuyerinfo_word=MyCartWords("nobuyerinfo");
	
	if(!isset($_REQUEST["super"]) && (!isset($_REQUEST["email"]) && !isset($_REQUEST["OrderNo"]))){
		return "<h3>".$nobuyerinfo_word."</h3>";
	}

	//$pid=get_option('MyOrderApiUrl');
	$url=get_option('MyOrderApi');
	$url=apply_filters( 'MyOrderApiUrl', $url);

	if(isset($_REQUEST["super"]) && $_REQUEST["super"] !=""){
			$url.="?super=".$_REQUEST["super"];
	}

	if(isset($_REQUEST["email"]) && isset($_REQUEST["OrderNo"])){
		$url.="?email=".$_REQUEST["email"]."&OrderNo=".$_REQUEST["OrderNo"];
	}

	$curl = curl_init(); //开启curl
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	return urldecode($info["buyer"]);
}

add_shortcode( "orderlistinfo" , "show_orderlistinfo" );
function show_orderlistinfo($atts){
	
	$noshoppinginfo_word=MyCartWords("noshoppinginfo");

	if(!isset($_REQUEST["super"]) && (!isset($_REQUEST["email"]) && !isset($_REQUEST["OrderNo"]))){
		return "<h3>".$noshoppinginfo_word."</h3>";
	}

	//$pid=get_option('MyOrderApiUrl');
	$url=get_option('MyOrderApi');
	$url=apply_filters( 'MyOrderApiUrl', $url);

	if(isset($_REQUEST["super"]) && $_REQUEST["super"] !=""){
			$url.="?super=".$_REQUEST["super"];
	}

	if(isset($_REQUEST["email"]) && isset($_REQUEST["OrderNo"])){
		$url.="?email=".$_REQUEST["email"]."&OrderNo=".$_REQUEST["OrderNo"];
	}

	$curl = curl_init(); //开启curl
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	return urldecode($info["orderlist"]).urldecode($info["payInfo"]);
}

//完整的查詢資料===============================================================================================
$full_Orderinfo=NULL;
add_shortcode( "full_Orderinfo" , "full_Orderinfo" );
function full_Orderinfo($atts){
	global $full_Orderinfo;

	$nodata_word=MyCartWords("nodata");

	if(!isset($_REQUEST["super"]) && (!isset($_REQUEST["email"]) && !isset($_REQUEST["OrderNo"]))){
		return $nodata_word;
	}
	
	if($full_Orderinfo==NULL){

		$url=get_option('MyOrderApi');
		$url=apply_filters( 'MyOrderApiUrl', $url);

		if(isset($_REQUEST["super"]) && $_REQUEST["super"] !=""){
			$url.="?type=full_info&super=".$_REQUEST["super"];
		}

		if(isset($_REQUEST["email"]) && isset($_REQUEST["OrderNo"])){
			$url.="?type=full_info&email=".$_REQUEST["email"]."&OrderNo=".$_REQUEST["OrderNo"];
		}
	
		$curl = curl_init(); //开启curl		
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);			
		curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
		$obj = curl_exec($curl); //执行curl操作
		curl_close($curl);
		
		$info=json_decode($obj,true);

		$full_Orderinfo=$info;
	
	}

	if(count($full_Orderinfo)==0){
		return $nodata_word;
	}
		
	

	$default=array(
		'class'=>'',
		'col'=>'OrderNo',
		'tag'=>'span',
	);

	$a = shortcode_atts($default , $atts );

	extract($a);

	if($col==""){
		return "";
	}


	if($col=="buyerinfo"){
		
		//訂購人
		$buyer=unserialize($full_Orderinfo["buyer"]);
		$buyer=array_map("urldecode",$buyer);

		$buyerinfo_company="";
		if($buyer["receipt"]!=""){

			$buyerinfo_company_name=$buyer["company"]==""?"":" ,".$buyer["company"];
			$buyerinfo_company="<li>".MyCartWords("taxIdNumber")."<span>".$buyer["receipt"].$buyerinfo_company_name."</span>"."</li>";
		}
		
		$buyerInfo ="<ul class=\"orderinfo\">";
		$buyerInfo.="<li>".$buyer["bname"]."</li>";
		$buyerInfo.="<li>".MyCartWords("phoneNumber")."<span>".$buyer["bphone"]."</span>"."</li>";
		$buyerInfo.="<li>".MyCartWords("email")."<span>".$buyer["bemail"]."</span>"."</li>";
		//$buyerInfo.=$buyer["zip"]!=""?"<li>"."郵遞區號"."<span>".$buyer["zip"]."</span>"."</li>":"";
		$buyerInfo.="<li>".MyCartWords("address")."<span>".$buyer["zip"].", ".$buyer["address"]."</span>"."</li>";
		$buyerInfo.=$buyerinfo_company;
		$buyerInfo.="</ul>";

		return $buyerInfo;

	}

	if($col=="receiverinfo"){
		
		$receiver=unserialize($full_Orderinfo["receiver"]);
		$receiver=array_map("urldecode",$receiver);

		if(count($receiver)==0){
			$receiverInfo="<ul class=\"orderinfo\">";
			$receiverInfo.="<li>".MyCartWords("sameWithBuyer")."</li>";
			$receiverInfo.="</ul>";
		}else{
			$receiverInfo="<ul class=\"orderinfo\">";
			$receiverInfo.="<li>".MyCartWords("name")."<span>".$receiver["rname"]."</span>"."</li>";
			$receiverInfo.="<li>".MyCartWords("phoneNumber")."<span>".$receiver["rphone"]."</span>"."</li>";
			$receiverInfo.="<li>".MyCartWords("email")."<span>".$receiver["remail"]."</span>"."</li>";
			//$receiverInfo.=$receiver["rzip"]!=""?"<li>"."郵遞區號"."<span>".$receiver["rzip"]."</span>"."</li>":"";
			$receiverInfo.="<li>".MyCartWords("address")."<span>".$receiver["rzip"].", ".$receiver["raddress"]."</span>"."</li>";
			$receiverInfo.="</ul>";
		}

		return $receiverInfo;
	}

	if($col=="productlist"){
		$CargoList=unserialize($full_Orderinfo["CargoList"]);
		$list="";
		foreach ($CargoList as $key => $val) {
			$title= urldecode($val["title"]);			
			$list.="<li>".$title." (".PriceFormat("$",$val["price"]).") x".number_format($val["amount"])."</li>";
		}

		$porductlist="<ul class='CargoList'>";
		$porductlist.=$list;
		$porductlist.="</ul>";
		return $porductlist;
	}

	if($col=="Note1"){
		if($full_Orderinfo[$col]==""){
			return MyCartWords("empty");
		}
	}

	if($col=="PayInfo"){
		//付款資訊

	    $NewDate=urldecode($full_Orderinfo["NewDate"]);
	    $NewDate=date_create($NewDate);
	    $NewDate=date_format($NewDate,'Y-m-d');

	    $DueDate=urldecode($full_Orderinfo["DueDate"]);
	    $DueDate=date_create($DueDate);
	    $DueDate=date_format($DueDate,'Y-m-d H:i:s');

	    //$TotalPrice="$".number_format($full_Orderinfo["TotalPrice"]);
	    $TotalPrice=PriceFormat("$",$full_Orderinfo["TotalPrice"]);

	    $payInfo="";

	    $payInfo = $full_Orderinfo["ErrDesc"]==""?"":"<div>".MyCartWords("errorMsg")."<span style='color:red'>".urldecode($orderinfo["ErrDesc"])."</span>"."</div>";


	    switch ($full_Orderinfo["PayType"]) {	    		    	
	    	case '03':
	    		
	    		$payInfo.="<ul class=\"orderinfo\">";
	    		$payInfo.="<li>".MyCartWords("BankCode")."<span>".$full_Orderinfo["BankCode"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("ATMNo")."<span>".$full_Orderinfo["ATMNo"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("TotalPrice")."<span>".$TotalPrice."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("TransferDeadline")."<span>".$DueDate."</span>"."</li>";
	    		$payInfo.="<li><span style='color:#ff5c4b;'>*金額超過 $30,000 請至臨櫃匯款。</span></li>";
	    		$payInfo.="</ul>";

	    		break;
	    	case '05':
	    		
	    		$payInfo.="<ul class=\"orderinfo\">";
	    		$payInfo.="<li>".MyCartWords("IBONNO")."<span>".$full_Orderinfo["IBONNO"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("TotalPrice")."<span>".$TotalPrice."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("Deadline")."<span>".$DueDate."</span>"."</li>";

	    		$payInfo.="</ul>";
	    		break;
	    	case '10':
	    		
	    		$payInfo.="<ul class=\"orderinfo\">";
	    		$payInfo.="<li>".MyCartWords("BarCode")."1"."<span>".$full_Orderinfo["BarCode1"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("BarCode")."2"."<span>".$full_Orderinfo["BarCode2"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("BarCode")."3"."<span>".$full_Orderinfo["BarCode3"]."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("TotalPrice")."<span>".$TotalPrice."</span>"."</li>";
	    		$payInfo.="<li>".MyCartWords("Deadline")."<span>".$DueDate."</span>"."</li>";
	    		$payInfo.="</ul>";
	    		break;			
	    	
	    	default:
	    		$payInfo="";
	    		break;
	    }

	    return $payInfo;
	}

	if($col=="ShippingStatus"){

		
	   	if(strrpos($full_Orderinfo["SendStatus"],"KTJ") != false || strrpos($full_Orderinfo["SendStatus"],"SF") != false){
   			$SentStatus=MyCartWords("shipped");
   		}else{
   			$SentStatus=MyCartWords("notShipped");
   		}


	   	return $SentStatus;
	}



	$class=$class==""?$col:$class." info-".$col;

	$content=urldecode($full_Orderinfo[$col]);
	$content = nl2br($content);

	if($col=="PayType"){
		$TransCode["PayType"]=array(
			"01"=>MyCartWords("CreditCard"),
			"11"=>MyCartWords("CreditCardStag"),
			"03"=>MyCartWords("ATM"),
			"05"=>MyCartWords("Ibon"),
			"10"=>MyCartWords("BarCode")
		);
		$PayType = $TransCode["PayType"][$full_Orderinfo["PayType"]];
		$content=$PayType;
	}

	if($col=="TranStatus"){
		$TransCode["TranStatus"]=array(
			"S"=>"交易成功",
			"F"=>"交易未完成",
			""=>"未回傳"
		);
		$content=$TransCode["TranStatus"][$full_Orderinfo["TranStatus"]];
	}

	if(in_array($col,["shippingfee","TotalPrice"])){
		if($col == "shippingfee"){
			$col = "shippingFee";
		}

		
		$content = $full_Orderinfo[$col];

		if(!isset($full_Orderinfo[$col]) || !$content){
			$content = 0;
		}
		
		$content = PriceFormat("",$content);

		return $content;
	}

	return "<".$tag." class='".$class."'>".$content."</".$tag.">";

}

//search Form 訂單搜索表單===============================================================
add_shortcode("searchform","mysearchform");
function mysearchform($atts){
	$pid=get_option('MyOrderSearchPage');
	$action_url=get_permalink($pid);

	$default=array(
		'button'=>MyCartWords("SearchOrder"),
		'label1'=>MyCartWords("OrderNo"),
		'label2'=>MyCartWords("BuyerEmail")
    );
		
	$a = shortcode_atts($default , $atts );

	ob_start();
?>
	<div class="my_searchform" >
		<form id="my_searchform" method="POST" action="<?php echo $action_url;?>">
			<label><?php echo $a["label1"];?></label>
			<input type="text" name="OrderNo" required="required">
			<label><?php echo $a["label2"];?></label>
			<input type="email" name="email" required="required">
			<input type="submit" value="<?php echo $a["button"];?>">
		</form>	
	</div>		
<?php	    
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;


}

//cate post widget by cate==============================================================
add_shortcode("my_widget_product_list","my_widget_product_list");
function my_widget_product_list($atts){
	$default=array(
		'cat'=>'',
		'posts_per_page'=>-1,
    );
		
	$a = shortcode_atts($default , $atts );


	$tax_query=array('relation' => 'OR');
	if(!isset($a["cat"]) || $a["cat"]==""){		
		return;
	}else{
		$cates=explode(",", $a["cat"]);
		$tax_query[]=array(
			'taxonomy' => 'product_cate',
			'field'    => 'slug',
			'terms'    => $cates,
		);	
	}

	$args = array(
		'post_type' => 'myproducts',
		'posts_per_page'=>$a["posts_per_pag"],
		'post_parent'=>0,
		'fields'=>'ids',
		'tax_query' => $tax_query,
		'orderby'=>'menu_order',
		'order'=>'ASC'
	);


	$posts=get_posts($args);
	
  if(count($posts)==0){
    return ;
  }
  ob_start();
?>

  <div class='my-relative-post'>
    <ul>     
  <?php
  foreach ($posts as $key => $post) {
    $fixed_key=$key+1;
  ?>
    
    <li class='my-relative-post-single'>
      <a href="<?php echo get_permalink($post);?>">
        <div class="my-relative-post-img">
          <?php echo get_the_post_thumbnail( $post, $size = 'thumbnail' ); ?>
        </div>  
      	<div class="my-relative-post-title">
        	<?php echo get_the_title(  $post ); ?><br>
        	<?php echo showprice(array("id"=>$post));?>
      	</div>          
      </a>         
    </li> 
    
  <?php
  }
  ?>
    </ul>
  </div>
  <style>
  .my-relative-post-img,.my-relative-post-title{
  	display: inline-block;
  	vertical-align: middle;
  }
  .my-relative-post-img{
  	max-width: 60px;
  }
  .my-relative-post-title{
  	margin-left: 10px;
  }

  .my-relative-post-single a {
	    text-decoration: none !important;
	}

  </style>  
<?php

	$contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}



//relative post============================================================================
add_shortcode("my_relative_product","my_relative_product");
function my_relative_product($atts){

	$default=array(
		'id'=>'',
    );

	if(!isset($atts["id"])){
		$pid=get_the_ID();
		$default["id"]=$p_id;
	}else{
		$pid=$atts["id"];
	}
		
  $post_type="myproducts";
  $post_term="product_cate";

  if(get_post_type($pid)!=$post_type || is_single($pid)==false){    
    return ;
  }

  get_the_terms($pid, $post_term);
  $terms=wp_get_post_terms( $pid, $taxonomy = $post_term,$args = array("fields"=>"ids") );

  $args=array(
      'post_type' => $post_type,
      'numberposts' => 4,
      'fields'=>'ids',
      'post__not_in'=>array($pid),
      'tax_query'=>array(
          array(
            'taxonomy' => $post_term,
            'terms' => $terms,
            'field' => 'term_id',
            'operator' => 'IN',
            'include_children' => true
          ),
        ),
      'meta_query' => array( array( 
        'key' => '_thumbnail_id',
        'value' => '0',
        'compare' => '>=',
        )
    )
  );


  $posts=get_posts(  $args );

  if(count($posts)==0){
    return ;
  }
  ob_start();
?>

  <div class='my-relative-post'>
    <ul>     
  <?php
  foreach ($posts as $key => $post) {
    $fixed_key=$key+1;
  ?>
    
    <li class='my-relative-post-single'>
      <a href="<?php echo get_permalink($post);?>">
        <div class="my-relative-post-img">
          <?php echo get_the_post_thumbnail( $post, $size = 'thumbnail' ); ?>
        </div>  
      	<div class="my-relative-post-title">
        	<?php echo get_the_title(  $post ); ?><br>
        	<?php echo showprice(array("id"=>$post));?>
      	</div>          
      </a>         
    </li> 
    
  <?php
  }
  ?>
    </ul>
  </div>
  <style>
  .my-relative-post-img,.my-relative-post-title{
  	display: inline-block;
  	vertical-align: middle;
  }
  .my-relative-post-img{
  	max-width: 60px;
  }
  .my-relative-post-title{
  	margin-left: 10px;
  }

  .my-relative-post-single a {
	    text-decoration: none !important;
	}

  </style>  
<?php

	$contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

//funding cart form ===================================================================================
function funding_cart_rendering_single($p_id,$value=0){
	
	$title = get_the_title($p_id);
	$price = showprice($atts=["id"=>$p_id]);
	$image = get_the_post_thumbnail( $p_id ,  'myproduct' );
	$excerpt   = get_the_excerpt( $p_id );
	$product_cates = get_the_terms( $p_id, "product_cate" );

	

	if(count($product_cates) > 0){
		$the_class = [];
		foreach ($product_cates as $key => $cate) {
			$the_class[]="myproduct-".$cate->slug;
		}

		$the_class = join(" ",$the_class);
	}else{
		$the_class = "";
	}

	$the_id = "myproduct_".$p_id;	

	if($excerpt){
		$title.="<div style='color:#7e7e7e;font-size:15px'>".$excerpt."</div>";
	}
?>
	<?php
	if(wp_is_mobile() == false):			
	?>	
		    		
					<div id="<?php echo $the_id;?>" class="item-single-line <?php echo $the_class;?>">			    		
						<div class="cell col-img">
							<?php echo $image;?>
						</div>				        
						<div class="cell col-name">
							<?php echo $title;?>			        
						</div>				        
						<div class="cell col-price">
							<?php echo $price;?>		        
						</div>				        
						<div class="cell col-num">				        	
							<div class="quantity clearfix">								
								<input type="button" value="-" index="0" class="minus">								
								<input type="text" name="item_qty[]" pattern="^[0-9]*$" data-id="<?php echo $p_id;?>" data-origin="<?php echo $value;?>" value="<?php echo $value;?>" class="qty">								
								<input type="button" value="+" index="0" class="plus">
								<input type="hidden" name="item_id[]" value="<?php echo $p_id;?>"> 							
							</div>				       
						</div>				        
						<div class="cell col-total"><?php echo $price;?></div>				       
					</div>
	<?php
	else:
	?>
				<div id="<?php echo $the_id;?>" class="item-single-line <?php echo $the_class;?>">
		    		<div class="cell col-img">
		    			<?php echo $image;?>
		    		</div>
			        <div class="cell col-num">
			        	<?php echo $title;?><br>
			        	<?php echo $price;?><br>
			        	<div class="quantity clearfix">
							<input type="button" value="-"  class="minus">
							<input type="text" name="item_qty[]" pattern="^[0-9]*$" data-id="<?php echo $p_id;?>" data-origin="<?php echo $value;?>" value="<?php echo $value;?>" class="qty">	
							<input type="button" value="+"  class="plus">
							<input type="hidden" name="item_id[]" value="<?php echo $p_id;?>"> 	
						</div>
			        </div>
		    	</div>
	<?php
	endif;
}


add_shortcode("funding_cart_form","funding_cart_form");
function funding_cart_form(){
	ob_start();

	$CheckPage = get_option('MyCheckPage') ;
	$CheckPageUrl = get_page_link($CheckPage);
?>
	
<form action="<?php echo $CheckPageUrl;?>" method="POST">
	
	<div class="cart-list-container">
<?php



$args=array(
	'post_type' => 'myproducts',
	'orderby'   => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),

);



$args_all = $args;


if(isset($_GET["product"] ) && $_GET["product"] != ""){
	
	$post_in = [$_GET["product"]];
	$post__not_in = [$_GET["product"]];

	$args["post__in"] = $post_in;
	
	$args = apply_filters("funding_page_args",$args);

	$args_all["post__not_in"] = $args["post__in"];

	

	$have_project = true;

}else{

	$have_project = false;
}

$args_all = apply_filters("funding_page_args_all",$args_all);
$products_query = new WP_Query( $args );

if ( $products_query->have_posts() && $have_project):
?>		
		<h4>已選購商品</h4>
		<div id="cart-list" class="cart-list-fund cart item-list">
<?php
if(wp_is_mobile() == false):			
?>	
			<div class="list-head">
				<div class="cell col-img">&nbsp;</div>
				<div class="cell col-name">商品</div>
				<div class="cell col-price">單價</div>
				<div class="cell col-num">購買數量</div>
				<div class="cell col-total">小計</div>
			</div>
<?php
else:
?>
			<div class="list-head">
		        <div class="cell col-name">商品</div>
		        <div class="cell col-num">&nbsp;</div>
		    </div>
<?php
endif;
?>			
			<div class="list-body">	
<?php
	// The Loop
	$i=1;
	while ( $products_query->have_posts() ) {

		$products_query->the_post();
		$post = $products_query->post;
		$p_id = get_the_ID();

		funding_cart_rendering_single($p_id,1);

		$i++;
	}
	
	wp_reset_postdata();
?>

			</div>
		</div>
<?php	
endif;

?>
<?php
$products_query = new WP_Query( $args_all );

if ( $products_query->have_posts() ):
?>		
		<h4>可加購商品</h4>
		<div  class="cart-list-fund cart item-list">
<?php
if(wp_is_mobile() == false):			
?>	
			<div class="list-head">
				<div class="cell col-img">&nbsp;</div>
				<div class="cell col-name">商品</div>
				<div class="cell col-price">單價</div>
				<div class="cell col-num">購買數量</div>
				<div class="cell col-total">小計</div>
			</div>
<?php
else:
?>
			<div class="list-head">
		        <div class="cell col-name">商品</div>
		        <div class="cell col-num">&nbsp;</div>
		    </div>
<?php
endif;
?>			
			<div class="list-body">	
<?php
	// The Loop
	$i=1;
	while ( $products_query->have_posts() ) {

		$products_query->the_post();
		$post = $products_query->post;
		$p_id = get_the_ID();
		funding_cart_rendering_single($p_id,0);
		$i++;
	}
	
	wp_reset_postdata();
?>

			</div>
		</div>
<?php	
endif;

?>



		<div id="cart-count">
			<div class="the_country_selector"></div>
			<div class="total-bill">總金額: <span class="amount_product"></span></div>
			<div class="total-bill">運費: <span class="amount_shipping-bill"></span></div>
			<div class="total-bill total-bill-discount">折扣: <span class="amount_discount-bill"></span></div>
			<input type="submit"  class="button"  name="funding_check" value="結帳">
		</div>

	</div>		
</form>	
<?php	
	$output = ob_get_contents();
	ob_clean();
	return $output;
}


