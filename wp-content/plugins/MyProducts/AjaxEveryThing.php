<?php
add_action( 'wp_ajax_nopriv_Everything', 'ajax_Everything' );
add_action( 'wp_ajax_Everything', 'ajax_Everything' );
function ajax_Everything() {

	if(!isset($_POST["items"])){
		return;
	}

   	$items=$_POST["items"];
   	$coupon=$_POST["coupon"];
    $shipping_method = $_POST["shipping_method"];
   	$shippingfee=MyShippingfee($items);
   	$discount=MyDiscount($items);  	
   	$result=new MyCoupon($items,$shippingfee,$discount,$coupon);  
   	$items=$result->items;
   	$shippingfee=$result->shippingfee;
   	$discount=$result->discount;

    if($coupon == ""){
      $_coupon = null;
    }else{
       $_coupon = [
         "status"=> $result->status,
         "message"=> $result->message,
      ];
    }

   // $shippingfee=(int)$shippingfee;
   // $discount=(int)$discount;
   	$output=array(
   		"items"=>$items,
   		"shippingfee"=>$shippingfee, 
   		"discount"=> $discount, 
      "coupon" => $_coupon
	);


    $output = apply_filters("ajax_Everything",$output,$items,$shippingfee,$coupon,$discount,$shipping_method);
    echo json_encode($output);
    exit;
}