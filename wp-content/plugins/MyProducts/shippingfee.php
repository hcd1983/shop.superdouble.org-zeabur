<?php
function MyShippingfee($items=[],$coupon=""){
	if( $items==NULL ){
	    $shippingfee = 0;
		return $shippingfee = apply_filters("fixshippingfee",$shippingfee,$items,$coupon);;
	}
	$shippingfee_setting=get_option('shipping_fee');   
	if($shippingfee_setting != false){
		$shippingfee=$shippingfee_setting["basic"];	
	}else{
		$shippingfee=0;
	}

	$shippingfee = apply_filters("fixshippingfee",$shippingfee,$items,$coupon);
	return $shippingfee;
}

function GetCountryOptions(){
	$CountryOptions=array();
	$CountryOptions[]=array(
		"CountryCode"=>"TW",
		"CountryName"=>"台灣",
	);
	

	$CountryOptions=apply_filters("CountryOptions",$CountryOptions);
	return $CountryOptions=array_values($CountryOptions);
}


function GetShippingOptions(){
	$ShippingOptions=array();
	$ShippingOptions[]=array(
		"value"=>"",
		"text"=>"Regular Shipping",
	);
	

	$ShippingOptions=apply_filters("ShippingOptions",$ShippingOptions);
	return $ShippingOptions=array_values($ShippingOptions);
}



add_filter("fixshippingfee","shipping_discount",10,10);
function shipping_discount($shippingfee,$items,$coupon){
	
	$total_price=0;

	foreach ($items as $key => $item) {
		$total_price+=($item["price"]*$item["amount"]);
	}

	if($total_price==0){
		return 0;
	}

	$shippingfee_setting=get_option('shipping_fee');  
	if($shippingfee_setting != false && isset($shippingfee_setting["over"])){
		if($shippingfee_setting["over"]== "-1"){
			return $shippingfee;
		}
		if( $total_price >= $shippingfee_setting["over"]){
			return 0;
		}else{
			return $shippingfee;
		}
	}
	return $shippingfee;
}

add_action( 'wp_ajax_nopriv_SheepingFee', 'ajax_SheepingFee' );
add_action( 'wp_ajax_SheepingFee', 'ajax_SheepingFee' );
function ajax_SheepingFee() {
    //echo var_dump($_POST["items"]);
    //echo 999;
    $output=array(
    	"shippingfee"=>MyShippingfee($_POST["items"],$coupon=""),
    	"discount"=>MyDiscount($_POST["items"]),
    );
    //echo MyShippingfee($_POST["items"],$coupon="");
    echo json_encode($output);
    exit;
}









