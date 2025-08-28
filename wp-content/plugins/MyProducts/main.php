<?php
/**
 * @package MyProducts
 * @version 0.11
 */
/*
Plugin Name: A Product Post Type
Author: Dean Huang
Version: 0.11
Description: Product Post Type
*/


$MyProductsSetting=array(
  "UseProductCate"=>1,
);


date_default_timezone_set('Asia/Taipei');

define("MyProductsPluginPath",plugin_dir_path( __FILE__ ));

$MyCartLangOpt = get_option("MyCartLang"); 
global $MycartLang;
$MycartLang=array();
require_once MyProductsPluginPath."locales/zh.php";
switch ($MyCartLangOpt) {
  case 'en':
    require_once MyProductsPluginPath."locales/en.php";
    break;
  
  default:
    //require_once MyProductsPluginPath."locales/zh.php";
    break;
}



require_once MyProductsPluginPath."functions.php";
require_once MyProductsPluginPath."NewPostType/"."NewPostType.php";
require_once MyProductsPluginPath."NewPostType/"."NewTax.php";
require_once MyProductsPluginPath."NewPostType/"."NewMetaBox.php";
require_once MyProductsPluginPath."MultiPostThumbnails.php";
require_once MyProductsPluginPath."gallery-metabox/gallery.php";
require_once MyProductsPluginPath."imageuploader.php";
require_once MyProductsPluginPath."postpicker.php";
require_once MyProductsPluginPath."OptionPages.php";
require_once MyProductsPluginPath."enfold.php";
require_once MyProductsPluginPath."MyCartScript.php";
require_once MyProductsPluginPath."shippingfee.php";
require_once MyProductsPluginPath."discount.php";
require_once MyProductsPluginPath."coupon.php";
require_once MyProductsPluginPath."shortcodes.php";
require_once MyProductsPluginPath."picsize.php";
require_once MyProductsPluginPath."email_tpl.php";
require_once MyProductsPluginPath."radio_cate.php";
require_once MyProductsPluginPath."AjaxEveryThing.php";
require_once MyProductsPluginPath."store.php";

if(get_option("CollectMailWhenLack") == 1){
  require_once MyProductsPluginPath."sendmemail.php";
}

if(get_option("UseStripe") == 1){
  require_once MyProductsPluginPath."stripe.php";
  require_once(MyProductsPluginPath.'plugins/stripe/init.php');
}

if(isset($_GET["MyCartLang"])){
  add_action("init","MycartLanguageInit");  
}

function MycartLanguageInit(){
  global $MycartLang;
?>
  console.log("MyCart language loaded");
  var MycartLang=<?php echo json_encode($MycartLang);?>
<?php
  exit;
}

add_action("init","setMyProductsSetting");
function setMyProductsSetting(){
  global $MyProductsSetting;
  if(get_option('MyFbProducts')== false || get_option('MyFbProducts')==""){
    $UseProductCate=0;
  }else{
    $UseProductCate=get_option("UseProductCate")===false?1:get_option("UseProductCate");
  }
  
  $MyProductsSetting["UseProductCate"]=$UseProductCate;
  $MyProductsSetting=apply_filters("SetMyProductsSetting",$MyProductsSetting);
}



// child  thumbnail ===================================================================

add_filter( 'get_post_metadata', function ( $value, $post_id, $meta_key, $single ) {
  // We want to pass the actual _thumbnail_id into the filter, so requires recursion
  static $is_recursing = false;
  // Only filter if we're not recursing and if it is a post thumbnail ID
  if ( ! $is_recursing && $meta_key === '_thumbnail_id' ) {
    $is_recursing = true; // prevent this conditional when get_post_thumbnail_id() is called
    $value = get_post_thumbnail_id( $post_id );
    $is_recursing = false;
    $value = apply_filters( 'post_thumbnail_id', $value, $post_id ); // yay!
    if ( ! $single ) {
      $value = array( $value );
    }
  }
  return $value;
}, 10, 4);


add_filter("post_thumbnail_id","opt_thumbnail",10,2);
function opt_thumbnail($value,$post_id){

  $parent_id = wp_get_post_parent_id( $post_id );
  if( !$value && get_post_type($post_id) == "myproducts" && $parent_id){
     $value = get_post_thumbnail_id( $parent_id );
  }

  return $value;
}

// chile title ===========================================================

// add_filter('the_title','opt_title',10,2);
function opt_title($title, $post_id){
  $parent_id = wp_get_post_parent_id( $post_id );
  if(  get_post_type($post_id) == "myproducts" && $parent_id){
     $title = get_the_title($parent_id)." - ".$title;
  }

  return $title;
}


//WP_LOGIN_DETECT==========================================================
add_action('template_redirect','detect_if_login');
function detect_if_login(){ 
  
  if(!is_front_page()){
    return;
  }

  $key_setting=get_option('detect_user_login');
  
  if(isset($_REQUEST["login_key"]) ){
    

    if(is_user_logged_in() && $_REQUEST["login_key"]==$key_setting["key"]){
      $uid=get_current_user_id();

      $u_meta=array();      
      //$u_meta=get_user_meta( $uid, $key = '', $single = ture );
      $user=get_user_by( "ID",$uid);
      $u_meta["name"]=$user->display_name;
      $u_meta["email"]=$user->user_email;
      $u_meta["role"]=$user->roles[0];
      $u_meta["userid"]="wp_".$user->user_login;

      $u_meta["role"]="admin";
        $status=array(
          "status"=>"S",
          "datas"=>$u_meta
        );
                 
      //echo $u_meta;
    
    }else{
       $status=array(
        "status"=>"F",
        "datas"=>null
      );
    }

    echo json_encode($status);
    exit;
  }

  return;
}

//FB PIXEL=================================================================
add_action('wp_head', 'my_fbpixel',20,1);
function my_fbpixel(){
  if(get_option('pixelcode') == false){
    return;
  }
  echo $pixelcode=get_option('pixelcode');
}

//MENU======================================================================
add_action('wp_enqueue_scripts', 'my_product_script_frontend');
function my_product_script_frontend(){
  
  $langsrc=get_home_url()."?MyCartLang"; 
  wp_enqueue_script( $handle="MyCartLang", $langsrc, $deps = array(), $ver = time(), $in_footer = false ); 
  wp_enqueue_script( $handle="fontawesome-all", $src =plugins_url("js/fontawesome-all.js",__FILE__), $deps = array(), $ver = false, $in_footer = false );

  wp_enqueue_script( $handle="jscookie", $src =plugins_url("js/js.cookie.js",__FILE__), $deps = array("jquery"), $ver = false, $in_footer = false );



  if(is_ie()===false):

  wp_enqueue_script( $handle="MyCart", $src =plugins_url("js/MyCart.js",__FILE__), $deps = array("MyCartLang","jquery","jscookie"), $ver = time(), $in_footer = false ); 

  wp_enqueue_script( $handle="MyCartCustom", $src =plugins_url("js/MyCartCustomFunctions.js",__FILE__), $deps = array("jquery","jscookie"), $ver = time(), $in_footer = true );
  else:

  wp_enqueue_script( $handle="MyCart", $src =plugins_url("js/MyCart_IE.js",__FILE__), $deps = array("MyCartLang","jquery","jscookie"), $ver = time(), $in_footer = false ); 

  wp_enqueue_script( $handle="MyCartCustom", $src =plugins_url("js/MyCartCustomFunctions_IE.js",__FILE__), $deps = array("jquery","jscookie"), $ver = time(), $in_footer = true );

  endif;

  
  // wp_enqueue_script( $handle="MzoomImage", $src =plugins_url("js/zoom-image.js",__FILE__), $deps = array("jquery"), $ver = false, $in_footer = true );
  // wp_enqueue_style( $handle="MzoomImage",  $src = plugins_url("css/MzoomImage.css",__FILE__), $deps = array(),  $ver = time(), $media = 'all' );

  wp_enqueue_style( $handle="MyCart",  $src = plugins_url("css/cart_frontend.css",__FILE__), $deps = array(),  $ver = time(), $media = 'all' );

  wp_enqueue_style( $handle="ThemeFix",  $src = plugins_url("css/theme_menu_fix.css",__FILE__), $deps = array(),  $ver = time(), $media = 'all' );

  

}

add_action( 'admin_enqueue_scripts', 'enqueue_date_picker' );
function enqueue_date_picker(){
  wp_enqueue_script( 'jquery-ui-datepicker' );

  // You need styling for the datepicker. For simplicity I've linked to Google's hosted jQuery UI CSS.
  //wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
  wp_register_style('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
  
  wp_enqueue_style( 'jquery-ui' ); 
/*
  wp_enqueue_script(
            'field-date-js', 
            'Field_Date.js', 
            array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
            time(),
            true
        );  

        wp_enqueue_style( 'jquery-ui-datepicker' );
*/        
}


add_action('wp_head','cart_style');
function cart_style(){
?>
  <style>
    


  </style>
<?php
}


$my_cart_top_menu=new stdClass();
$my_cart_top_menu->title='<i class="fas fa-shopping-cart"></i><span class="total-amount">0</span>';
$my_cart_top_menu->classes=["top-cart"];
$my_cart_top_menu->url='javascript:void(0)';


$my_cart_top_menu_mobile=new stdClass();
$my_cart_top_menu_mobile->title='<i class="fas fa-shopping-cart"></i><span class="total-amount">0</span>';
$my_cart_top_menu_mobile->classes=["top-cart","top-cart-mobile"];
$my_cart_top_menu_mobile->url='javascript:void(0)';

add_filter( 'wp_nav_menu_objects', 'product_icon_menu',3,2 );
function product_icon_menu($sorted_menu_items,$args=""){

  if( myproducts_debugger()){
    return $sorted_menu_items;
  }

  if($args->menu->slug!="main_menu"){
     return $sorted_menu_items;
  }  
  if(wp_is_mobile()==true){
    return $sorted_menu_items;
  }
  if(get_the_id()== get_option('MyCheckPage') || get_the_id()== get_option('MyCartPage') || get_the_id()== get_option('StripePayPage')){
    return $sorted_menu_items;
  }
  
  global $my_cart_top_menu;
  $cart=$my_cart_top_menu;
  $cart=apply_filters("my_cart_top_menu",$cart);
  $sorted_menu_items[]=$cart;
  
  return $sorted_menu_items;
}



add_filter( 'wp_nav_menu_items', "product_sub_menu",3,2 );
function product_sub_menu($items="", $args=""){
  
  if($args->menu->slug!="main_menu"){
     return $items;
  } 

  if(wp_is_mobile()==true){
    return $items;
  }
  if(get_the_id()== get_option('MyCheckPage') || get_the_id()== get_option('MyCartPage') ||  get_the_id()== get_option('StripePayPage')){
    return $items;
  }

  $CartPage=get_option('MyCartPage') ;

  $CartPage=$CartPage==""?"#":get_permalink($CartPage);

  $items.='
    <div id="top-cart" class="top-cart-content">
      <div class="top-cart-title">
        <h4>'.MyCartWords("cart").'</h4>
      </div>
      <div class="top-cart-items" style="max-height: 65vh; overflow-y: auto;">
        <!--item from cart-->
      </div>
      <div class="top-cart-action clearfix">
        <span class="fleft top-checkout-price">$<span class="total-bill">0</span></span>
        <a id="goCheck" href="'.$CartPage.'" class="fright"><div class="button button-3d button-small nomargin fright">'.MyCartWords("gotocheck").'</div></a>
      </div>
    </div>';
  return $items;  
}

//THEME======================================================================

add_filter( 'template_include', 'portfolio_page_template', 99 );
function portfolio_page_template( $template ) {
  global $post;
  if($post->ID==get_option('ProductsPages')){
    $template=MyProductsPluginPath."theme/"."product_json.php";
  };

  if($post->ID==get_option('MyFbProducts')){
    $template=MyProductsPluginPath."theme/"."FB_Products.php";
  };

  if(is_post_type_archive( $post_types = 'myproducts' )){
    $template=MyProductsPluginPath."theme/"."enfold-taxonomy-product_cate.php";
  };
   
  return $template;
}

//term page======================================================================
add_filter( 'taxonomy_template', 'product_cate_template', 99 );
function product_cate_template( $template ) {
  //var_dump(get_query_var( 'taxonomy' ));
  var_dump(get_query_var( 'term' ));
  if(get_query_var( 'taxonomy' )=="product_cate"){
    $template=MyProductsPluginPath."theme/"."enfold-taxonomy-product_cate.php";
  }
  return $template;
}


//AJAX=============================================================
add_action( 'wp_ajax_SetPage', 'ajax_SetPage' );    // If called from admin panel
//add_action( 'wp_ajax_nopriv_my_action_name', 'my_ajax_callback_function' );    // If called from front end

$MyOpts=get_option('ProductsPages');

function ajax_SetPage() {
    $postID=wp_insert_post( array("post_title"=>"product_json",'post_type'=>"page","post_status"=>"publish"), false );
    if($postID !=0){
      update_option( "ProductsPages", $postID );
    } 
}

add_action( 'wp_ajax_SetCheckPage', 'ajax_SetCheckPage' );
function ajax_SetCheckPage() {
    $postID=wp_insert_post( array("post_title"=>"check",'post_type'=>"page","post_status"=>"publish","post_content"=>"[mycheckform][mycouponform][mycheckpage]"), false );
    if($postID !=0){
      update_option( "MyCheckPage", $postID );
    }
   
}

add_action( 'wp_ajax_SetCartPage', 'ajax_SetCartPage' );
function ajax_SetCartPage() {
    $postID=wp_insert_post( array("post_title"=>"cart",'post_type'=>"page","post_status"=>"publish","post_content"=>"[mycartpage]"), false );
    if($postID !=0){
      update_option( "MyCartPage", $postID );
    }
   
}

add_action( 'wp_ajax_SetThanksPage', 'ajax_SetThanksPage' );
function ajax_SetThanksPage() {
    $postID=wp_insert_post( array("post_title"=>"Thanks",'post_type'=>"page","post_status"=>"publish","post_content"=>"[orderlistinfo][paynowinfo][buyerinfo]"), false );
    if($postID !=0){
      update_option( "MyThanksPage", $postID );
    }
   
}

add_action( 'wp_ajax_SetMyOrderSearchPage', 'ajax_SetMyOrderSearchPage' );
function ajax_SetMyOrderSearchPage() {
    $postID=wp_insert_post( array("post_title"=>"Search Result",'post_type'=>"page","post_status"=>"publish","post_content"=>"[orderlistinfo][paynowinfo][buyerinfo]"), false );
    if($postID !=0){
      update_option( "MyOrderSearchPage", $postID );
    }
   
}

add_action( 'wp_ajax_SetFbProductsPage', 'ajax_SetFbProductsPage' );
function ajax_SetFbProductsPage() {
    $postID=wp_insert_post( array("post_title"=>"Fb_products",'post_type'=>"page","post_status"=>"publish"), false );
    if($postID !=0){
      update_option( "MyFbProducts", $postID );
    }
   
}

add_action( 'wp_ajax_SetFundingCartPagePage', 'ajax_SetFundingCartPagePage' );
function ajax_SetFundingCartPagePage() {
    $postID=wp_insert_post( array("post_title"=>"Funding Cart",'post_type'=>"page","post_content"=>"[funding_cart_form]","post_status"=>"publish"), false );
    if($postID !=0){
      update_option( "MyFundingCartPage", $postID );
    }
   
}




//主題圖片二==================================================================
if (class_exists('MultiPostThumbnails')) {
  
  $thumb = new MultiPostThumbnails(array(
    'label' => '精選圖片二',
    'id' => 'secondary-image',
    'post_type' => 'myproducts'
    )
  );
 
}
//POST TYPE--------------------------------------------------------------------------------------------------------
$PostType=new MyCustomPostType("myproducts","產品",$args=array(
   'menu_position'=>20,
   'rewrite' => array( 
        'slug' => 'products', // use this slug instead of post type name
        'with_front' => FALSE // if you have a permalink base such as /blog/ then setting this to false ensures your custom post type permalink structure will be /products/ instead of /blog/products/
    ),
));

$NewTax=new MyCustomTax("product_cate","產品分類","myproducts",$args=array(
  "show_in_quick_edit"=>true,
  "show_admin_column"=>true,

));
$NewTag=new MyCustomTax("product_tag","產品標籤","myproducts",$args=array(
  "hierarchical"=>false,
  "show_tagcloud"=>true,
  "show_in_quick_edit"=>true,
  "show_admin_column"=>true
));

$MetaBox=new MyMetaBox("ProductsInfo","產品資訊","myproducts","productsmeta","productsmetasave","side");

function productsmeta($post){
  wp_nonce_field( plugin_basename( __FILE__ ), 'safecode' );
  $ProductsInfo = get_post_meta( $post->ID, "ProductsInfo", $single = true );
  $ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$post->ID);

  if(!is_array($ProductsInfo)){
    $ProductsInfo=array();
  }

  if(!isset($ProductsInfo["usestore"]) || $ProductsInfo["usestore"]==""){
    $ProductsInfo["usestore"]=0;
  }

  if(!isset($ProductsInfo["intro_link"]) || $ProductsInfo["intro_link"]==""){
    $ProductsInfo["intro_link"] = 1;
  }

  $vars=["product_id","store","price","saleprice","onsale"];
  foreach ($vars as $key => $val) {
    if(!isset($ProductsInfo[$val])){
      $ProductsInfo[$val]="";
    }
  }  
?>
<div class="ProductsInfo_metabox">
  <div class="row">
      <label>產品代碼</label><br>
      <input type="text" name="ProductsInfo[product_id]" value="<?php echo $ProductsInfo['product_id'];?>">
  </div>
  
   <div class="row">
    <label>開啟預購模式?</label><br>
    <select name="ProductsInfo[preorder]">
      <option value="0" <?php selected( $ProductsInfo['preorder'], 0 ); ?>>否</option>
      <option value="1" <?php selected( $ProductsInfo['preorder'], 1 ); ?>>是</option>
    </select>  
  </div>
 
  <div class="row">
      <label>產品庫存</label><br>
      <input type="number" name="ProductsInfo[store]" value="<?php echo $ProductsInfo['store'];?>">
  </div>

   <div class="row">
    <label>是否使用庫存?</label><br>
    <select name="ProductsInfo[usestore]">
      <option value="0" <?php selected( $ProductsInfo['usestore'], 0 ); ?>>否</option>
      <option value="1" <?php selected( $ProductsInfo['usestore'], 1 ); ?>>是</option>
    </select>  
  </div> 

  <div class="row">
      <label>產品定價</label><br>
      <input type="number" name="ProductsInfo[price]" value="<?php echo $ProductsInfo['price'];?>">
  </div>

  <div class="row">
      <label>產品特價</label><br>
      <input type="number" name="ProductsInfo[saleprice]" value="<?php echo $ProductsInfo['saleprice'];?>">
  </div>

  <div class="row">
    <label>是否使用特價?</label><br>
    <select name="ProductsInfo[onsale]">
      <option value="0" <?php selected( $ProductsInfo['onsale'], 0 ); ?>>否</option>
      <option value="1" <?php selected( $ProductsInfo['onsale'], 1 ); ?>>是</option>
    </select>  
  </div>
  <div class="row">
    <label>是否在前端顯示產品介紹的連結</label><br>
    <select name="ProductsInfo[intro_link]">
      <option value="0" <?php selected( $ProductsInfo['intro_link'], 0 ); ?>>否</option>
      <option value="1" <?php selected( $ProductsInfo['intro_link'], 1 ); ?>>是</option>
    </select>  
  </div>
  <!--
  <div class="row">
      <label>產品選項</label><br>
      <select name="ProductsInfo[otherOptions]">
  -->      
        <?php
        //postpicker::generate_post_select( "myproducts",$ProductsInfo['otherOptions']);
        ?>
  <!--      
      </select>  
  </div>
  -->

</div>    
<?php    
}

function productsmetasave($post_id){
 
  if( ! isset( $_POST['safecode'] ) || ! wp_verify_nonce( $_POST['safecode'], plugin_basename( __FILE__ ) ) )
    return;
  $post_ID = $_POST['post_ID'];
  $ProductsInfo=$_POST["ProductsInfo"];

  update_post_meta($post_ID, "ProductsInfo", $ProductsInfo);
}



//LIST------------------------------------------------------------------------------------------------------------------//

add_filter( 'manage_posts_columns', 'themename_add_post_thumbnail_column', 99 ); // add the thumb column to posts
function themename_add_post_thumbnail_column( $cols ) { // add the thumb column
  // output feature thumb in the end
  //$cols['themename_post_thumb'] = __( 'Featured image', 'themename' );
  //return $cols;
  // output feature thumb in a different column position
  $cus_columns=array();
  
  
  if($_GET["post_type"]=="myproducts"){
    $cus_columns['post_thumb']="縮圖";
    //$cus_columns['post_thumb_two']="縮圖二";
    $cus_columns['shortcode']="Shortcode";
    $cus_columns['store']="庫存";
    $cols_start = array_slice( $cols, 0, 2, true );
    $cols_end   = array_slice( $cols, 2, null, true );
 
    $custom_cols = array_merge(
      $cols_start,
      $cus_columns,
      $cols_end
    );
  }else{
    $custom_cols=$cols;
  }


  return $custom_cols;
}




add_theme_support( 'post-thumbnails' );
add_action( 'manage_pages_custom_column', 'themename_display_post_thumbnail_column', 5, 2 );
//add_action( 'manage_posts_custom_column', 'themename_display_post_thumbnail_column', 5, 2 ); // add the thumb to posts
function themename_display_post_thumbnail_column( $col, $id ) { // output featured image thumbnail
  switch( $col ){

    case 'post_thumb':
      if( function_exists( 'the_post_thumbnail' ) ) {
        echo the_post_thumbnail( 'thumbnail' );

      } else {
        echo "";
      }
      break;
    case 'post_thumb_two':
      if( function_exists( 'the_post_thumbnail' ) ) {
        if (class_exists('MultiPostThumbnails')) : 
 
          MultiPostThumbnails::the_post_thumbnail(get_post_type(), 'secondary-image', NULL,  'thumbnail');
         
        endif;

      } else {
        echo "";
      }
      break;  

    case 'shortcode':
      
      echo "[addtocart id=".$id."]";
      break; 
    case 'store':
         $ProductsInfo=get_post_meta( $id, "ProductsInfo", $single = true );
         $ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$id);
         echo "使用庫存: ";
         echo $ProductsInfo["usestore"]==1?"是":"否";
         echo "<br>";
         echo "庫存: ";
         echo $ProductsInfo["store"]<1?"<span style='color:red;font-size:16px'>".$ProductsInfo["store"]."</sapn>":$ProductsInfo["store"];
      break;
  }

  return ;
}








