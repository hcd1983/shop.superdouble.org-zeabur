<?php

add_action('admin_enqueue_scripts', 'fa_custom_css_font');
function fa_custom_css_font() {
   wp_enqueue_script( $handle="fontawesome-all", $src =plugins_url("js/fontawesome-all.js",__FILE__), $deps = array(), $ver = false, $in_footer = false );
   //wp_enqueue_style("kv_fontawesome", 'https://use.fontawesome.com/releases/v5.0.13/css/all.css');
}

add_action('admin_head', 'icon_custom_css_font');
function icon_custom_css_font() {
   echo "<style type='text/css' media='screen'>
       #adminmenu #toplevel_page_shipping_fee div.wp-menu-image:before {
           display:none;
        }


     </style>";
    ?>
    <script>
    	$=jQuery;
    	$(document).ready(function(){
    		$("#adminmenu #toplevel_page_shipping_fee div.wp-menu-image").prepend("<i class='fas fa-truck' style='padding:10px 0;'></i>");
    		
    	})    	
    </script>
    <?php 
}




add_action('admin_menu', 'MyProductOpts');
function MyProductOpts() {	
	add_submenu_page( "edit.php?post_type=myproducts", "產品頁面設定","產品頁面設定",$capability="administrator", "MyProduct_setting", 'MyProduct_settingFn' );

	add_action( 'admin_init', 'register_MyProductsPages_setting' );

	add_menu_page( $page_title="FB Pixel", $menu_title="FB Pixel", $capability="administrator", $menu_slug="my_fb_pixel", $function = 'my_fb_pixel_settingFn', $icon_url = 'dashicons-images-alt2', $position = 2 );

	add_menu_page( $page_title="wordpress 登入偵測", $menu_title="WP登入偵測", $capability="administrator", $menu_slug="detect_user_login", $function = 'detect_user_login_settingFn', $icon_url = 'dashicons-shield', $position = 2 );

	add_menu_page( $page_title="訂單信件設定", $menu_title="訂單信件設定", $capability="administrator", $menu_slug="shop_mail", $function = 'shop_mail_settingFn', $icon_url = 'dashicons-format-aside', $position = 2 );

	add_menu_page( $page_title="運費設計", $menu_title="運費設計", $capability="administrator", $menu_slug="shipping_fee", $function = 'shipping_fee_settingFn', $icon_url = '', $position = 3 );

    
	if(get_option('detect_user_login')!=false ){
		add_menu_page( $page_title="訂單管理後台", $menu_title="訂單管理後台", "read", $menu_slug="orderlist_admin", $function = 'orderlist_admin', $icon_url = 'dashicons-cart', $position = 1 );
	}
}



function register_MyProductsPages_setting() {
	
	//帳號設定
	register_setting( 'MyProducts', 'ProductsPages' );
	register_setting( 'MyProducts', 'MyCheckPage' );
	register_setting( 'MyProducts', 'MyCartPage' );
	register_setting( 'MyProducts', 'MyFundingCartPage' );
	register_setting( 'MyProducts', 'MyThanksPage' );
	register_setting( 'MyProducts', 'MyOrderSearchPage' );
	register_setting( 'MyProducts', 'MyInsertPort' );
	register_setting( 'MyProducts', 'MyOrderApi' );
	register_setting( 'MyProducts', 'MyFbProducts' );
	register_setting( 'MyProducts', 'MyGoogleProductCategory' );
	register_setting( 'MyProducts', 'UseStripe' );
	register_setting( 'MyProducts', 'UseProductCate' );	
	register_setting( 'MyProducts', 'MyBrand' );
	register_setting( 'MyProducts', 'CollectMailWhenLack' );	
	register_setting( 'MyProducts', 'MyCartLang' );	
	register_setting( 'MyProducts', 'MyDebugger' );	
	register_setting( 'MyProducts', 'MyPaytype' );
	register_setting( 'MyProducts', 'FundingMode' );
	register_setting( 'MyProducts', 'DifferentReceiver' );
	register_setting( 'MyProducts', 'product_selector_single' );
	register_setting( 'MyProducts', 'product_selector_list' );
	register_setting( 'MyPixel', 'pixelcode' );
	register_setting( 'detect_user_login', 'detect_user_login' );
	register_setting( 'shop_mail', 'shop_mail' );
	register_setting( 'shop_mail', 'shop_mail_msg' );
	register_setting( 'shipping_fee', 'shipping_fee' );
	register_setting( 'MyCountry', 'MyCourtry' );
}


function MyProduct_settingFn(){
	include_once plugin_dir_path( __FILE__ ).'option_pages/pages.php';
}

function my_fb_pixel_settingFn(){
	include_once plugin_dir_path( __FILE__ ).'option_pages/fbpixel.php';
}

function detect_user_login_settingFn(){
	include_once plugin_dir_path( __FILE__ ).'option_pages/detect_user_login.php';
}

function shop_mail_settingFn(){
	include_once plugin_dir_path( __FILE__ ).'option_pages/shop_mail.php';
}

function shipping_fee_settingFn(){
	include_once plugin_dir_path( __FILE__ ).'option_pages/shipping_fee.php';
}

function orderlist_admin(){
	$location=get_option('detect_user_login')["url"]."login.php?wp_login";
?>
	<script>
		 window.location.href = "<?php echo $location;?>"; 
	</script>	
<?php	
}