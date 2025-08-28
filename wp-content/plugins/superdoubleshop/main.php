<?php
/**
 * @package SuperDoubleShop
 * @version 1.0
 */
/*
Plugin Name: Super Double Shop
Author: Dean Huang
Version: 1.0
*/

date_default_timezone_set('Asia/Taipei');
define("superdouble_plugin_url",plugin_dir_url( __FILE__));
define("superdouble_plugin_path",plugin_dir_path( __FILE__ ));


// Method 1: Filter.
function my_acf_google_map_api( $api ){
    $api['key'] = 'AIzaSyAapkzTq3dA7dgJjQh78EQMSuG_J_bJWLg';
    return $api;
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

// Method 2: Setting.
function my_acf_init() {
    acf_update_setting('google_api_key', 'AIzaSyAapkzTq3dA7dgJjQh78EQMSuG_J_bJWLg');
}
add_action('acf/init', 'my_acf_init');

require_once superdouble_plugin_path."shipping.php";
require_once superdouble_plugin_path."ShippingCate.php";
require_once superdouble_plugin_path."cronjob.php";
require_once superdouble_plugin_path."ajax_page.php";
require_once superdouble_plugin_path."onsale.php";
require_once superdouble_plugin_path."funding.php";
require_once superdouble_plugin_path."coupon.php";
require_once superdouble_plugin_path."CouponGenerator.php";
require_once superdouble_plugin_path."productGallery.php";
require_once superdouble_plugin_path."redirect.php";
require_once superdouble_plugin_path."couponSetting.php";
require_once superdouble_plugin_path."thumbinal_for_slider.php";
require_once superdouble_plugin_path."groupPrdouctCoupon.php";

add_action( 'wp_ajax_nopriv_foobar', 'my_ajax_foobar_handler' );
add_action( 'wp_ajax_foobar', 'my_ajax_foobar_handler' );


add_action('wp_ajax_nopriv_Everything','corss_everything',1);
function corss_everything(){
    header("Access-Control-Allow-Origin: *");
}

//function add_cors_http_header(){
//    header("Access-Control-Allow-Origin: *");
//}
//add_action('init','add_cors_http_header');

function add_custom_headers() {

    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Headers: Authorization, X-WP-Nonce,Content-Type, X-Requested-With');
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
        header( 'Access-Control-Allow-Credentials: true' );

        return $value;
    } );
}
add_action( 'rest_api_init', 'add_custom_headers', 15 );



add_filter('avf_logo_link','av_change_logo_link');
function av_change_logo_link($link)
{
    $link = "https://superdouble.org/";
    return $link;
}

add_action('wp_footer','logo_effect');
function logo_effect(){
?>
    <script>
        $=jQuery
        $(document).ready(function(){
            $("div#footer_icon .av_font_icon").hover(
                function(){
                    $("div#footer_icon .av_font_icon").css("color","#8a8a8a");
                    $(this).css("color","#ffffff");
                },function(){
                    $("div#footer_icon .av_font_icon").css("color","#ffffff");
                }
            )
        })
    </script>
<?php
}

add_filter( 'acf/fields/wysiwyg/toolbars' , 'my_toolbars'  );
function my_toolbars( $toolbars ) {
	array_unshift( $toolbars['Basic' ][1], 'forecolor' );
	return $toolbars;
}








