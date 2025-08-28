<?php
add_action("admin_init","coupon_generator",99);
function coupon_generator(){
	if ( ! function_exists( 'post_exists' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/post.php' );
	}

    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body,true);

    if( isset( $data["action"]) && $data["action"] == "coupon_generator" ){

        extract($data);
        $coupons = [];
        $coupon_cate = $coupon_type;

        for ($i=0; $i < $coupon_number ; $i++) {

            $coupon = $prefix.GeaerateACoupon();
            $coupons[] = $coupon;
            $coupon_exist = post_exists(  $coupon, $content = '', $date = '', $type = 'mycoupon' );

            while ($coupon_exist !== 0) {
                $coupon = GeaerateACoupon();
                $coupon_exist = post_exists(  $coupon, $content = '', $date = '', $type = 'mycoupon' );
            }

            $custom_tax = array(
                'mycoupon_cate' =>[$coupon_cate]
            );

            $postarr = [];
            $postarr["post_title"] = $coupon;
            $postarr["post_type"] = 'mycoupon';
            $postarr['post_status'] = 'publish';
            $pid = wp_insert_post($postarr);
            $term = wp_set_object_terms($pid, [$coupon_cate],'mycoupon_cate' , true);
        }

        echo json_encode($coupons);

        exit;
    }

    if( isset($_GET["coupon_type"]) &&  $_GET["coupon_type"] != "" && isset($_GET["action"]) && $_GET["action"] == "coupon_generator"){

		if(!isset($_GET["coupon_number"])){
			$coupon_number = 1;
		}else{
			$coupon_number = $_GET["coupon_number"];
		}

		$coupon_cate = $_GET["coupon_type"];
		$accept_type = ["oneoneoneone"];

		if(!in_array($coupon_cate,$accept_type)){
			return;
		}

		$coupons = [];

		for ($i=0; $i < $coupon_number ; $i++) { 
			
			$coupon = GeaerateACoupon();

			$coupons[] = $coupon;
			$coupon_exist = post_exists(  $coupon, $content = '', $date = '', $type = 'mycoupon' );

			while ($coupon_exist !== 0) {			
				$coupon = GeaerateACoupon();
				$coupon_exist = post_exists(  $coupon, $content = '', $date = '', $type = 'mycoupon' );
			}

			$custom_tax = array(
			    'mycoupon_cate' =>[$coupon_cate]
			);

			$postarr = [];
			$postarr["post_title"] = $coupon;
			$postarr["post_type"] = 'mycoupon';
			$postarr['post_status'] = 'publish';
			$pid = wp_insert_post($postarr);
			$term = wp_set_object_terms($pid, [$coupon_cate],'mycoupon_cate' , true);
		}

?>				
<table>
	<tr>
		<th>No</th><th>Coupon</th>
	</tr>
<?php
	foreach ($coupons as $_key => $_coupon) {
		echo "<tr>";
		echo "<td>".($_key + 1 )."</td>";
		echo "<td>".$_coupon."</td>";
		echo "</tr>";
	}
?>	
</table>
<?php
		exit;
	}

}

function GeaerateACoupon($leng=5){
	$seed = str_split('1234567890abcdefghijklmnopqrstuvwxyz'); // and any other characters
	shuffle($seed); // probably optional since array_is randomized; this may be redundant
	$rand = '';
	foreach (array_rand($seed, $leng) as $k) $rand .= $seed[$k];
	return strtoupper($rand);
}


add_action("init","view_coupon",99);
function view_coupon(){
	if(isset($_GET["view_coupon"]) && $_GET["view_coupon"]){
		$coupon_type_slug = $_GET["view_coupon"];
		$args = [];
		$args["post_type"] = "mycoupon";
		$args["posts_per_page"] = -1;
		$args['tax_query'] = array(
            array(
                'taxonomy' => 'mycoupon_cate',
                'field' => 'slug',
                'terms' => $coupon_type_slug,
            )
        );

		$coupon_type = $_GET["view_coupon"];
		$coupons = get_posts($args);
?>				
<table>
	<tr>
		<th>No</th><th>Coupon</th>
	</tr>
<?php
	foreach ($coupons as $_key => $_coupon) {

		$title =get_the_title($_coupon);
		echo "<tr>";
		echo "<td>".($_key + 1 )."</td>";
		echo "<td>".$title."</td>";
		echo "</tr>";
	}
?>	
</table>
<?php
		exit;
	}
}