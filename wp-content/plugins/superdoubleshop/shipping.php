<?php

add_filter("themoneylogo","changeMoneyLogo",10,1);
function changeMoneyLogo($logo){
   return "TWD ";
}

add_filter("CountryOptions","Hkgogogo",20,1);
function Hkgogogo($CountryOptions){
    $CountryOptions[]=array(
        "CountryCode"=>"HK",
        "CountryName"=>"香港",
    );

    $CountryOptions[]=array(
        "CountryCode"=>"MO",
        "CountryName"=>"澳門",
    );

    $CountryOptions[]=array(
        "CountryCode"=>"CN",
        "CountryName"=>"中國",
    );

    $CountryOptions[]=array(
        "CountryCode"=>"MY",
        "CountryName"=>"馬來西亞",
    );

    $CountryOptions[]=array(
        "CountryCode"=>"SG",
        "CountryName"=>"新加坡",
    );

    return $CountryOptions;
}

add_filter("fixshippingfee","shipping_by_country",11,2);
function shipping_by_country($shippingfee=0,$items=[],$coupon=""){
    
    if(isset($_POST["country"]) && $_POST["country"] && $_POST["country"] != "TW"){
        $country=$_POST["country"];
        $shippingfee = CountryShipping($country,$shippingfee,$items,$coupon);
    }else{
        $total = 0;
        if (isset($_POST['groupProductItems']) && !empty($_POST['groupProductItems'])) {
            $more_items = $_POST['groupProductItems'];
            foreach ($more_items as $key => $more_item) {
                $total += $more_item["price"] * $more_item["amount"];
            }
        }
        foreach ($items as $key => $item) {
            $total += $item["price"] * $item["amount"];
        }
        if ($total === 0 ) {
            $shippingfee = 0;
        } else {
            $shippingfee_setting = get_option('shipping_fee');
            $shippingfee = $shippingfee_setting["basic"];
            if( $shippingfee_setting != false && isset($shippingfee_setting["over"]) ){
                if( $shippingfee_setting["over"] == "-1" ){
//                    return $shippingfee;
                }
                if( $total >= $shippingfee_setting["over"]){
                    $shippingfee = 0;
                } else {
//                    return $shippingfee;
                }
            }else{
                $shippingfee = 0;
            }
        }
    }
    
    return $shippingfee;
}

function CountryShipping($country="",$shippingfee=0,$items=[],$coupon=""){

    if(!$country){
        return $shippingfee;
    }

    if (isset($_POST['groupProductItems']) && !empty($_POST['groupProductItems'])) {
        $groupProductItems = $_POST['groupProductItems'];
        $groupProductData = array_map(function ($_item) {
            return [
                "id" => $_item["id"],
                "amount" => $_item["amount"],
            ];
        }, $groupProductItems);
    }else{
        $groupProductData = [];
    }

    switch ($country) {
        case 'HK':
            $shipping_1150  = 1150;
            $shipping_700  = 700;
            $shipping_250  = 250;
            $shipping_200  = 200;

            break;

        case 'MO':
            $shipping_1150  = 1200;
            $shipping_700  = 750;
            $shipping_250  = 300;
            $shipping_200  = 250;

            break;  
        case 'CN':
            $shipping_1150  = 2050;
            $shipping_700  = 1300;
            $shipping_250  = 400;
            $shipping_200  = 350;

            break;

        case 'MY':
            $shipping_1150  = 1900;
            $shipping_700  = 1150;
            $shipping_250  = 700;
            $shipping_200  = 650;

            break; 

        case 'SG':
            $shipping_1150  = 1900;
            $shipping_700  = 1150;
            $shipping_250  = 700;
            $shipping_200  = 650;

            break;                
        
        default:
            $shipping_1150  = 1150;
            $shipping_700  = 700;
            $shipping_250  = 250;
            $shipping_200  = 200;
            
            break;
    }

    $shippingfee = 0;
    if( count($items) === 0 && count($groupProductData) === 0){
        return $shippingfee;
    }
    $w = 0;
    $x = 0;
    $y = 0;
    $z = 0;



    foreach ($items as $key => $item) {

        $shippingGroups = get_the_terms($item["id"], "SDshippingGroup");

        $taxonomy_ids = array();

        foreach ($shippingGroups as $key => $group) {
            $taxonomy_ids[] = $group->slug;
        }

        if( in_array("s1150",$taxonomy_ids )){
            $w += $item["amount"];
            continue;
        }

        if( in_array("s700",$taxonomy_ids )){
            $x += $item["amount"];
            continue;
        }

        if( in_array("s250",$taxonomy_ids )){
            $y += $item["amount"];
            continue;
        }

        if( in_array("s200",$taxonomy_ids )){
            $z += $item["amount"];
            continue;
        }

        $x += $item["amount"];
    }

    foreach($groupProductData as $key => $item) {

        $shippingGroups = get_the_terms($item["id"], "SDshippingGroup");
        $taxonomy_ids = array();
        foreach ($shippingGroups as $key => $group) {
            $taxonomy_ids[] = $group->slug;
        }

        if( in_array("s1150",$taxonomy_ids )){
            $w += $item["amount"];
            continue;
        }

        if( in_array("s700",$taxonomy_ids )){
            $x += $item["amount"];
            continue;
        }

        if( in_array("s250",$taxonomy_ids )){
            $y += $item["amount"];
            continue;
        }

        if( in_array("s200",$taxonomy_ids )){
            $z += $item["amount"];
            continue;
        }

        $x += $item["amount"];
    }

    $discount_shipping = $w + $x;

    $shippingfee = ( $w * $shipping_1150);

    $shippingfee += ( $x * $shipping_700 );

    $shippingfee +=  $discount_shipping > $y ? 0:( $y - $discount_shipping ) * $shipping_250;

    $shippingfee +=  $discount_shipping > $z ? 0:( $z - $discount_shipping ) * $shipping_200;

    return $shippingfee;
}


add_action('wp_footer','Hk_alert',50);
function Hk_alert(){

    global $post;
    if($post->ID != get_option('MyCheckPage')){
        return;
    }

?>
    <script>
        $=jQuery
        $("#the_country").change(function(){
            var country=$(this).val();
            // if( country=="HK"){
            if( $.inArray(country , ["CN","MO","HK"]) != -1){    
                //alert("香港客戶請填中文地址，若無法到付請填順豐站地址。");
                $("#address_note").remove();
                $("label[for=address]").append("<span id='address_note' style='color:red'> 請填中文地址</span>");
                $("#zip").removeClass("required");
                $("label[for=zip] small").hide();   
            } else{
                $("#address_note").remove();
                $("#zip").addClass("required");
                $("label[for=zip] small").show();  
            }

        });
       // $("#the_country").change();
    </script>    
<?php  
/*  
    if(is_user_logged_in()){
        echo "<pre>";
        var_dump($_COOKIE);
        echo "</pre>";        
    }
*/    
}