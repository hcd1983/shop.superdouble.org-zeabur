<?php
Class MyCoupon{
    public $items;
    public $shippingfee;
    public $coupon;
    public $coupon_id=NULL;
    public $status="F";
    public $message="優惠代碼錯誤。";
    public $coupon_type_id=NULL;
    public $discount=0;
    public $feedback = [];
    public $onsaleItemsCount = 0;


    function __construct($items=[],$shippingfee=0,$discount=0,$coupon=""){

        date_default_timezone_set("Asia/Taipei");

        $this->items=$items;
        $this->shippingfee=$shippingfee;
        $this->coupon=$coupon;

        $message=$this->message;
        $message=MyCartWords("coupon_wrong");
        $status=$this->status;

        $this->CheckStatus();
        $this->ActiveCoupon();

        //$status=apply_filters("CouponStatus",$items,$shippingfee,$coupon);
        //$items=apply_filters("CouponItems",$items,$shippingfee,$coupon);
        //$shippingfee=apply_filters("CouponShippingfee",$items,$shippingfee,$coupon);
        //$message=apply_filters("CouponMessage",$items,$shippingfee,$coupon);

        $feedback=array(
            "status"=>$this->status,
            "message"=>$this->message,
            "shippingfee"=>$this->shippingfee,
            "items"=>$this->items,
            "coupon"=>$this->coupon,
            "coupon_id"=>$this->coupon_id,
            "coupon_type_id"=>$this->coupon_type_id,
            "discount"=>$this->discount,
            "tester"=>"yes",
        );

        $this->feedback = $feedback;

        return 	$feedback;
    }

    function CheckStatus(){

        if($this->coupon==""){
            $this->status="F";
            $this->message=MyCartWords("coupon_empty");
            return;
        }

        $coupon_id=get_page_by_title($this->coupon ,"ARRAY_A",  'MyCoupon' );
        $coupon_id=$coupon_id["ID"];

        if($coupon_id==NULL){
            $this->status="F";
            $this->message = MyCartWords("coupon_wrong_notexist");
            return;
        }
        if ('publish' !== get_post_status( $coupon_id )) {
            $this->status="F";
            $this->message=MyCartWords("coupon_wrong_notexist");
            return;
        }
        $this->coupon_id= $coupon_id;
        $t_id=wp_get_post_terms( $coupon_id, "mycoupon_cate",$args=array( 'fields' => 'ids' )  )[0];
        $this->coupon_type_id=$t_id;

        $active=get_term_meta( $t_id, 'active', true );
        $message=get_term_meta( $t_id, 'message', true );

        $from = get_term_meta( $t_id, 'from', true );
        $to = get_term_meta( $t_id, 'to', true );
        $threshold = get_term_meta( $t_id, 'threshold', true );
        $limit = get_term_meta( $t_id, 'limit', true );
        $products_in = get_term_meta( $t_id, 'products_in', true );



        if($active==0){
            $this->status="F";
            $this->message="優惠代碼已停用。";
            return ;
        }

        $now_time=time();

        if($from !=""){
            $from_time=strtotime($from);
            if($from_time > $now_time){
                $this->status="F";
                $this->message="優惠碼尚未啟用。".$from;
                return ;
            }
        }

        if($to !=""){
            $to_time=strtotime($to."+1 day");
            if($to_time < $now_time){
                $this->status="F";
                $this->message="優惠碼過期。".$to;
                return ;
            }
        }

        if($threshold != "" && $threshold > 0){
            $total_price=0;
            foreach ($this->items as $key => $item) {
                $total_price+=($item["price"]*$item["amount"]);
            }

            if($threshold > $total_price){
                $this->status="F";
                $this->message="消費金額不足。";
                return;
            }
        }

        if($limit >= 0){
            $used=get_post_meta( $this->coupon_id, $key = 'used',true );
            $used=$used==""?0:$used;
            if($limit <= $used){
                $this->status="F";
                $this->message="優惠碼已失效。";
                return;
            }
        }

        if(is_array($products_in) && count($products_in)>0){
            $_total_products=count($products_in);
            $_good=0;
            foreach ($products_in as $key => $product) {
                foreach ($this->items as $_key => $item) {
                    $pos=strpos(strtolower($item["title"]), strtolower($product["title"]));

                    if($pos !== false && $item["price"]*$item["amount"] >= $product["amount"]){
                        $_good++;
                        break;
                    }
                }
            }

            //只要一個符合就好
            if($_good == 0){
                $this->status="F";
                $this->message="購買商品未符合資格。";
                return;
            }
            //需要全部符合
            /*
            if($_total_products > $_good){
              $this->status="F";
              $this->message="購買商品未符合資格。";
              return;
            }
            */

        }

//        if(!$allow_onsale){
//            foreach ($this->items as $key => $item) {
//                if(isset($item["onsale"]) && $item["onsale"] == "1"){
//                    $this->onsaleItemsCount ++;
//                    break;
//                }
//            }
//
//            if($this->onsaleItemsCount > 0){
//                $this->status="F";
//                $this->message="本優惠不適用已特價之商品";
//                return;
//            }
//        }

        $this->status="S";
        $this->message=$message;

    }

    function ActiveCoupon(){

        if($this->status != "S" || $this->coupon_id == null || $this->coupon_type_id == null){
            $this->status="F";
            //$this->message="發生不明錯誤。";
            return;
        }

        $t_id=$this->coupon_type_id;
        $discount=get_term_meta( $t_id, 'discount', true );
        $percent_off=get_term_meta( $t_id, 'percent_off', true );
        $shippingfee_discount=get_term_meta( $t_id, 'shippingfee_discount', true );
        $gifts=get_term_meta( $t_id, 'gifts', true );

        $allow_onsale =get_term_meta( $t_id, 'allow_onsale', true );


        if($percent_off < 100){
            foreach ($this->items as $key => $item) {
                if(!$allow_onsale && isset($item["onsale"]) && $item["onsale"] == "1"){
                    
                } else {
                    $this->items[$key]["price"]= ceil($item["price"]*($percent_off/100));
                }

            }
        }

        if(is_array($gifts) && count($gifts) >0){
            foreach ($gifts as $key => $gift) {
                $imageUrl=wp_get_attachment_image_src( $gift["image"], $size = 'thumbnail', $icon = false );
                $imageUrl=$imageUrl==false?"":$imageUrl[0];
                $this->items[]=array(
                    "title"=>$gift["title"],
                    "price"=>$gift["price"],
                    "imageUrl"=>$imageUrl,
                    "id"=>null,
                    "store"=>-1,
                    "amount"=>$gift["amount"],
                );
            }
        }

        if($discount>0){
            $this->discount=$discount;
        }

        if($this->shippingfee > 0){
            if($shippingfee_discount== "-1"){
                $this->shippingfee=0;
            }

            if($shippingfee_discount > 0){
                $this->shippingfee=$this->shippingfee-$shippingfee_discount;
                $this->shippingfee=$this->shippingfee < 0?0:$this->shippingfee;
            }
        }

    }

}

//coupon setting page
add_action('admin_menu', 'MyCouponOpts');
function MyCouponOpts() {
    add_submenu_page( "edit.php?post_type=mycoupon", "Coupon設定","Coupon設定","administrator", "MyCoupon_setting", 'MyCoupon_settingFn' );
}

add_action( 'admin_init', function(){
    register_setting( 'MyCoupon', 'MyCoupon' );
} );

function MyCoupon_settingFn(){
    include_once plugin_dir_path( __FILE__ ).'option_pages/coupon.php';
}

//auto fill================================================================================================
add_action( 'admin_enqueue_scripts', 'ja_global_enqueues' );
function ja_global_enqueues() {
    wp_enqueue_style(
        'jquery-auto-complete',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.css',
        array(),
        '1.0.7'
    );
    wp_enqueue_script(
        'jquery-auto-complete',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-autocomplete/1.0.7/jquery.auto-complete.min.js',
        array( 'jquery' ),
        '1.0.7',
        true
    );
    wp_enqueue_script(
        'global',
        plugins_url("js/global.js",__FILE__),
        array( 'jquery' ),
        time(),
        true
    );
    wp_localize_script(
        'global',
        'global',
        array(
            'ajax' => admin_url( 'admin-ajax.php' ),
        )
    );
}

add_action( 'wp_ajax_search_site',        'ja_ajax_search' );
//add_action( 'wp_ajax_nopriv_search_site', 'ja_ajax_search' );
function ja_ajax_search() {
    $post_type=["myproducts"];
    $results = new WP_Query( array(
        'post_type'     => $post_type,
        'post_status'   => 'publish',
        'nopaging'      => true,
        'posts_per_page'=> 100,
        's'             => stripslashes( $_POST['search'] ),
    ) );
    $items = array();
    if ( !empty( $results->posts ) ) {
        foreach ( $results->posts as $result ) {
            $items[] = $result->post_title;
        }
    }
    wp_send_json_success( $items );
}




//POSTTYPE==================================================================================================
add_action( 'init', 'create_post_type_coupon' );
function create_post_type_coupon() {
    register_post_type( 'mycoupon',
        array(
            'labels' => array(
                'name' =>  'Coupon' ,
                'singular_name' => 'Coupon',
                'add_new'=>'新增 Coupon',
                'add_new_item'=>'新增 Coupon',
            ),
            'public' => false,
            'has_archive' => false,
            'show_in_admin_bar'=>false,
            'show_ui'=>true,
            'menu_position'=>21,
            'menu_icon'=>'dashicons-format-chat',
            'exclude_from_search'=>true,
            'hierarchical'=>false,
            'supports'=>['title','editor'],
            'query_var'=>false,
            'can_export'=>false
        )
    );
}

$coupon_tax_slug="mycoupon_cate";

add_action( 'init', 'create_tax_coupon' );
function create_tax_coupon(){
    global $coupon_tax_slug;
    $tax_name="Coupon 類型";
    $tax_slug=$coupon_tax_slug;

    $labels=array(
        'name'              => __($tax_name, $tax_slug),
        'singular_name'     => __($tax_name, $tax_slug),
        'add_new_item'      => __("新增類別", $tax_slug),
        'add_new'      => __("新增類別", $tax_slug),
        'not_found'    => __("無資料", $tax_slug),
        'edit_item'    => __("編輯coupon", $tax_slug),
    );
    $args=array(
        "labels"=>$labels,
        "show_in_quick_edit"=>true,
        "show_admin_column"=>true,
        'public'            =>  true,
        'hierarchical'      =>  false,
        'parent_item'  => null,
        'parent_item_colon' => null,
        'show_in_nav_menus' =>  true,
        'has_archive'       =>  false,
        'publicly_queryable'=> false
    );
    register_taxonomy($tax_slug,"mycoupon",$args);

}

//METABOX===============================================================================
add_action( 'add_meta_boxes', "metabox_mycoupon" );
function metabox_mycoupon(){
    add_meta_box(
        "my_coupon_meta",
        "Coupon 設定",
        "mycoupon_meta_ui",
        "mycoupon",
        'advanced',
        "high"
    );
}

function mycoupon_meta_ui($post){
    wp_nonce_field( plugin_basename( __FILE__ ), 'safecode' );
    $used=get_post_meta( $post->ID, "used", $single = true );
    $used=$used==""?0:$used;
    ?>
    <div class="coupon_setting_metabox">
        <div class="row">
            <label>已使用次數</label><br>
            <input type="number"  name="used" min="0" value="<?php echo $used;?>">
        </div>
    </div>

    <?php
    $OrderNos=get_post_meta( $post->ID, $key = 'OrderNos', false );
    if(count($OrderNos) > 0){
        ?>
        <div class="coupon_setting_metabox">
            <div class="row" style="margin-top:20px;">
                <label>已用訂單</label><br>
                <ul>
                    <?php
                    foreach ($OrderNos as $key => $val) {
                        echo "<li>".$val."</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <?php
    }
    ?>
    <?php
    $FailedOrderNos=get_post_meta( $post->ID, $key = 'FailedOrderNos', false );
    if(count($FailedOrderNos) > 0){
        ?>
        <div class="coupon_setting_metabox">
            <div class="row" style="margin-top:20px;">
                <label>失敗訂單</label><br>
                <ul>
                    <?php
                    foreach ($FailedOrderNos as $key => $val) {
                        echo "<li>".$val."</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <?php
    }
    ?>

    <?php
}

add_action( 'save_post', "save_metabox_mycoupon" );
function save_metabox_mycoupon(){
    if( ! isset( $_POST['safecode'] ) || ! wp_verify_nonce( $_POST['safecode'], plugin_basename( __FILE__ ) ) )
        return;

    $post_ID = $_POST['post_ID'];
    update_post_meta($post_ID, "used", $_POST["used"]);


}
//METABOX FOR mycoupon_cate
//metabox for portfolio==================================================================


add_action( $coupon_tax_slug.'_add_form_fields', $coupon_tax_slug.'_add_metabox', 10, 2 );
add_action( $coupon_tax_slug.'_edit_form_fields', $coupon_tax_slug.'_edit_metabox', 10, 2 );
// A callback function to add a custom field to our "presenters" taxonomy

function mycoupon_cate_edit_metabox( $term ) {

    // put the term ID into a variable
    $t_id = $term->term_id;

    $active=get_term_meta( $t_id, 'active', true );
    $discount=get_term_meta( $t_id, 'discount', true );
    $percent_off=get_term_meta( $t_id, 'percent_off', true );
    $shippingfee_discount=get_term_meta( $t_id, 'shippingfee_discount', true );
    $message=get_term_meta( $t_id, 'message', true );
    $gifts=get_term_meta( $t_id, 'gifts', true );
    $from = get_term_meta( $t_id, 'from', true );
    $to = get_term_meta( $t_id, 'to', true );
    $threshold = get_term_meta( $t_id, 'threshold', true );
    $limit = get_term_meta( $t_id, 'limit', true );
    $products_in=get_term_meta( $t_id, 'products_in', true );
    $number_count_logic = get_term_meta( $t_id, 'number_count_logic', true );
    $allow_onsale = get_term_meta( $t_id, 'allow_onsale', true ) == false ? 0 : 1;
    // $uni_user = get_term_meta( $t_id, 'uni_user', true );
    if($number_count_logic == false || $number_count_logic==""){
        $number_count_logic="check";
    }

    ?>
    <style>
        .form-field{
            margin-bottom: 20px;
        }
        .form-field label{
            display: block;
            margin-bottom: 5px;
            font-weight: 700;
        }
    </style>
    <tr class="form-field">
        <th>
            <label>啟用</label>
        </th>
        <td>
            <select name="active">
                <option value="1"<?php if ( $active == 1 ) echo 'selected="selected"'; ?>>啟用</option>
                <option value="0"<?php if ( $active == 0 ) echo 'selected="selected"'; ?>>停用</option>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th>
            <label>允許優惠商品使用</label>
        </th>
        <td>
            <select name="allow_onsale">
                <option value="1"<?php if ( $allow_onsale == 1 ) echo 'selected="selected"'; ?>>允許優惠商品使用</option>
                <option value="0"<?php if ( $allow_onsale == 0 ) echo 'selected="selected"'; ?>>禁止優惠商品使用</option>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th>
            <label>優惠內容</label>
        </th>
        <td>
            <div class="form-field">
                <label>折扣金額</label>
                <input type="number" value="<?php echo $discount;?>" min="0"  name="discount">
            </div>
            <div class="form-field">
                <label>金額打折 *商品消費金額 x 以下百分比</label>
                <select name="percent_off">

                    <?php
                    echo "<option value='100'>不打折</option>";
                    //            echo "<option value='88' ".($percent_off==88 ? "selected":"").">88折</option>";
                    for ($i=99; $i >= 10 ; $i--) {
                        if( $i%10 == 0){
                            $fix= $i/10;
                        }else{
                            $fix=$i;
                        }
                        if($percent_off==$i){
                            $selected='selected="selected"';
                        }else{
                            $selected='';
                        }
                        echo "<option value='".$i."' ".$selected.">".$fix."折</option>";
                    }
                    ?>

                </select>
                <p>說明:<br> 打折設定優先，如果設定折扣為95折，折扣金額為 200
                    (商品金額 x 95%) - 200</p>
            </div>

            <div class="form-field">
                <label>運費折扣 *-1 為免運費</label>
                <input type="number"  min="-1"  name="shippingfee_discount" value="<?php echo $shippingfee_discount;?>">
            </div>
            <div class="form-field form-required">
                <label >贈品</label>

                <table id="mytable" width="100%" border="1" cellspacing="0" cellpadding="2">
                    <tbody>
                    <tr>
                        <td>照片</td>
                        <td>名稱</td>
                        <td>數量</td>
                        <td>金額</td>
                        <td>控制</td>
                    </tr>
                    <?php
                    if(is_array($gifts) && count($gifts) >0){
                        foreach ($gifts as $key => $gift) {
                            $pic_id=intval($gift["image"]);
                            ?>
                            <tr class="gifts">
                                <td class="image_td"><?php $imguploader=new ImageUploader('gites',"thumbnail","options",$pic_id);?></td>
                                <td><input type="text" data-name="title" value="<?php echo $gift["title"];?>" name="gifts[0][title]" required="required"aria-required="true"/></td>
                                <td><input type="text" data-name="amount" value="<?php echo $gift["amount"];?>" name="gifts[0][amount]" min="0" value="1" required="required" aria-required="true"/></td>
                                <td><input type="text" data-name="price" value="<?php echo $gift["price"];?>" name="gifts[0][price]" min="0" value="0" required="required" aria-required="true"/></td>
                                <td><a   href="javascript:void(0);" class="remove_row button">刪除</a></td>
                            </tr>
                            <?php
                        }

                    }
                    ?>
                    </tbody>
                </table>
                <a  id="add" href="javascript:void(0);" class="button" style="margin-top:10px;">新增</a>
                <div id="image_uploader_html" class="hidden">
                    <?php  $labeluploader=new ImageUploader("gifts_image","thumbnail","options","");?>
                </div>
            </div>
            <div class="form-field">
                <label>成功訊息</label>
                <textarea name="message" rows="5" cols="40"><?php echo $message;?></textarea>
            </div>
        </td>
    </tr>
    <tr class="form-field">
        <th>
            <label>觸發條件</label>
        </th>
        <td>
            <div class="form-field">
                <label for="from">開始日</label>
                <input type="text" id="from" name="from" value="<?php echo $from;?>">
                <label for="to">結束日</label>
                <input type="text" id="to" name="to" value="<?php echo $to;?>">
            </div>
            <div class="form-field">
                <label >金額門檻 *消費超過門檻時啟用</label>
                <input type="number"  min="0"  name="threshold" value="<?php echo $threshold;?>">
            </div>
            <div class="form-field">
                <label >使用次數* -1為不限次數。</label>
                <input type="number"  min="-1" name="limit" value="<?php echo $limit;?>">
            </div>

            <div class="form-field">
                <label >計次邏輯</label>
                <select name="number_count_logic">
                    <option value="pay" <?php if($number_count_logic=="pay"){echo "selected";} ?>>扣款後計次</option>
                    <option value="check" <?php if($number_count_logic=="check"){echo "selected";} ?>>結帳後計次</option>
                </select>
            </div>
            <div class="form-field">
                <label >產品關鍵字 *不分大小寫，產品內容須包含此設定</label>
                <table id="mytable2" width="100%" border="1" cellspacing="0" cellpadding="2">
                    <tbody>
                    <tr>
                        <td>關鍵字</td>
                        <td>金額 <br>*購買此項產品超過此金額啟用</td>
                        <td>控制</td>
                        <?php
                        if(is_array($products_in) && count($products_in) >0){
                        foreach ($products_in as $key => $product) {
                        ?>

                    <tr class="products_in">
                        <td>
                            <input type="text" data-name="title" value="<?php echo $product["title"];?>" class="form-control search-autocomplete" name="products_in[0][title]">
                        </td>
                        <td>
                            <input type="number" min="0" data-name="amount" value="<?php echo $product["amount"];?>" name="products_in[0][amount]" required="required" aria-required="true"/>
                        </td>
                        <td><a   href="javascript:void(0);" class="remove_row button">刪除</a></td>
                    </tr>
                    <?php
                    }

                    }
                    ?>
                    </tr>
                    </tbody>
                </table>
                <a  id="add2" href="javascript:void(0);" class="button" style="margin-top:10px;">新增</a>

            </div>
        </td>
    </tr>

    <?php
    my_coupon_cate_setting_script();
}

function mycoupon_cate_add_metabox( $term ) {

    ?>
    <div class="form-field">
        <h3>啟用</h3>
        <select name="active">
            <option value="1">啟用</option>
            <option value="0">停用</option>
        </select>
    </div>
    <div class="form-field">
        <h3>允許優惠商品使用</h3>
        <select name="allow_onsale">
            <option value="1">允許優惠商品使用</option>
            <option value="0">禁止優惠商品使用</option>
        </select>
    </div>

    <h3>優惠內容</h3>
    <div class="form-field">
        <label>折扣金額</label>
        <input type="number" value="0" min="0"  name="discount">
    </div>
    <div class="form-field">
        <label>金額打折 *商品消費金額 x 以下百分比</label>
        <select name="percent_off">

            <?php
            echo "<option value='100'>不打折</option>";
            for ($i=95; $i > 5 ; $i-=5) {
                if( $i%10 == 0){
                    $fix= $i/10;
                }else{
                    $fix=$i;
                }
                echo "<option value='".$i."'>".$fix."折</option>";
            }
            ?>

        </select>
        <p>說明:<br> 打折設定優先，如果設定折扣為95折，折扣金額為 200
            (商品金額 x 95%) - 200</p>
    </div>

    <div class="form-field">
        <label>運費折扣 *-1 為免運費</label>
        <input type="number" value="0" min="-1"  name="shippingfee_discount">
    </div>
    <div class="form-field form-required">
        <label >贈品</label>

        <table id="mytable" width="100%" border="1" cellspacing="0" cellpadding="2">
            <tbody>
            <tr>
                <td>照片</td>
                <td>名稱</td>
                <td>數量</td>
                <td>金額</td>
                <td>控制</td>
            </tr>
            </tbody>
        </table>
        <a  id="add" href="javascript:void(0);" class="button" style="margin-top:10px;">新增</a>
        <div id="image_uploader_html" class="hidden">
            <?php  $labeluploader=new ImageUploader("gifts_image","thumbnail","options","");?>
        </div>
    </div>

    <div class="form-field">
        <label>成功訊息</label>
        <textarea name="message" rows="5" cols="40"></textarea>
    </div>
    <h3>觸發條件</h3>
    <div class="form-field">
        <label for="from">開始日</label>
        <input type="text" id="from" name="from">
        <label for="to">結束日</label>
        <input type="text" id="to" name="to">
    </div>
    <div class="form-field">
        <label >金額門檻 *消費超過門檻時啟用</label>
        <input type="number" value="0" min="0"  name="threshold">
    </div>
    <div class="form-field">
        <label >使用次數* -1為不限次數。</label>
        <input type="number" value="-1" min="-1" name="limit">
    </div>

    <div class="form-field">
        <label >計次邏輯</label>
        <select name="number_count_logic">
            <option value="pay">扣款後計次</option>
            <option value="check">結帳後計次</option>
        </select>
    </div>
    <div class="form-field">
        <label >產品關鍵字 *不分大小寫，產品內容須包含此設定</label>
        <table id="mytable2" width="100%" border="1" cellspacing="0" cellpadding="2">
            <tbody>
            <tr>
                <td>關鍵字</td>
                <td>金額 <br>*購買此項產品超過此金額啟用</td>
                <td>控制</td>
            </tr>
            </tbody>
        </table>
        <a  id="add2" href="javascript:void(0);" class="button" style="margin-top:10px;">新增</a>
        <!--<div class='in_products'>
          <input type="text"  class="form-control search-autocomplete" name="products_in[]">
        </div>-->
    </div>
    <?php
    my_coupon_cate_setting_script();
}

function my_coupon_cate_setting_script(){
    ?>
    <script>
        $( function() {
            var dateFormat = "mm/dd/yy",
                from = $( "#from" )
                    .datepicker({
                        defaultDate: "+1w",
                        changeMonth: true,
                        numberOfMonths: 3
                    })
                    .on( "change", function() {
                        to.datepicker( "option", "minDate", getDate( this ) );
                    }),
                to = $( "#to" ).datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    numberOfMonths: 3
                })
                    .on( "change", function() {
                        from.datepicker( "option", "maxDate", getDate( this ) );
                    });

            function getDate( element ) {
                var date;
                try {
                    date = $.datepicker.parseDate( dateFormat, element.value );
                } catch( error ) {
                    date = null;
                }

                return date;
            }
        } );

        function reset_gifts_row(){
            if($(".gifts").length==0){
                $("#mytable").hide();
            }else{
                $("#mytable").show();
            }
            $(".image_td input").data("name","image");
            $(".gifts").each(function(i){
                $(this).attr("data-row",i);
                var inputs=$(this).find("input");
                $(inputs).each(function(j){
                    var name="gifts["+i+"]"+"["+$(this).data("name")+"]";
                    $(this).attr("name",name);

                })
            })
        }

        $(document).ready(function() {
            var image_uploader_temp=$("#image_uploader_html").html();
            var backup='<input type="text" data-name="image" name="gifts[0][image]" required="required" aria-required="true"/>';
            var row_temp='\
      <tr class="gifts">\
        <td class="image_td">'+image_uploader_temp+'</td>\
        <td><input type="text" data-name="title" name="gifts[0][title]" required="required"aria-required="true"/></td>\
        <td><input type="text" data-name="amount" name="gifts[0][amount]" min="0" value="1" required="required" aria-required="true"/></td>\
        <td><input type="text" data-name="price" name="gifts[0][price]" min="0" value="0" required="required" aria-required="true"/></td>\
        <td><a   href="javascript:void(0);" class="remove_row button">刪除</a></td>\
      </tr>\
      ';
            $("#add").click(function() {
                //$('#mytable tbody>tr:last').clone(true).insertAfter('#mytable tbody>tr:last');
                $(row_temp).insertAfter('#mytable tbody>tr:last');
                reset_gifts_row();
                return false;
            });

            reset_gifts_row();
        });


        $(document).on("click",".remove_row",function(){
            $(this).closest("tr").remove();
            reset_gifts_row()
        })

        function reset_products_in_row(){
            if($(".products_in").length==0){
                $("#mytable2").hide();
            }else{
                $("#mytable2").show();
            }

            $(".image_td input").data("name","image");
            $(".products_in").each(function(i){
                $(this).attr("data-row",i);
                var inputs=$(this).find("input");
                $(inputs).each(function(j){
                    var name="products_in["+i+"]"+"["+$(this).data("name")+"]";
                    $(this).attr("name",name);

                })
            })
        }

        $(document).ready(function() {
            var temp='<input type="text" data-name="title"  class="form-control search-autocomplete" name="products_in[0][title]">';
            var row_temp='\
      <tr class="products_in">\
        <td>'+temp+'</td>\
        <td><input type="number" min="0" data-name="amount" name="products_in[0][amount]" value="0" required="required" aria-required="true"/></td>\
        <td><a   href="javascript:void(0);" class="remove_row button">刪除</a></td>\
      </tr>\
      ';
            $("#add2").click(function() {
                //$('#mytable tbody>tr:last').clone(true).insertAfter('#mytable tbody>tr:last');
                $(row_temp).insertAfter('#mytable2 tbody>tr:last');
                $('#mytable2 tbody>tr:last .search-autocomplete').autoComplete({
                    minChars: 2,
                    source: function(term, suggest){
                        try { searchRequest.abort(); } catch(e){}
                        searchRequest = $.post(global.ajax, { search: term, action: 'search_site' }, function(res) {
                            console.log(res.data);
                            suggest(res.data);
                        });
                    }
                });
                reset_products_in_row();
                return false;
            });

            reset_products_in_row();
        });

        $(document).on("click",".remove_row2",function(){
            $(this).closest("tr").remove();
            reset_products_in_row();
        })


    </script>
    <?php
}

add_action( 'edited_'.$coupon_tax_slug, $coupon_tax_slug.'_save_meta' );
add_action( 'create_'.$coupon_tax_slug, $coupon_tax_slug.'_save_meta' );
function mycoupon_cate_save_meta( $term_id ) {
    $accept_values=["gifts","products_in","active","discount","percent_off","shippingfee_discount","message","from","to","threshold","limit","number_count_logic","allow_onsale"];

    if(!isset($_POST["message"])){
        return;
    }

    foreach ($accept_values as $key => $val) {
        if ( isset( $_POST[$val] )  ) {
            update_term_meta( $term_id, $val, $_POST[$val] );

        }else{
            update_term_meta( $term_id, $val, "" );
        }
    }


    /*
      if ( isset( $_POST['to'] ) ) {
          update_term_meta( $term_id, 'to', $_POST['to'] );
      }
    */

}



//MENU====================================================================================
add_action('admin_head', 'icon_custom_css_font_coupon');
function icon_custom_css_font_coupon() {
    echo "<style type='text/css' media='screen'>
      
        #adminmenu #menu-posts-mycoupon div.wp-menu-image:before {
           display:none;
        }
     </style>";
    ?>
    <script>
        $=jQuery;
        $(document).ready(function(){
            $("#adminmenu #menu-posts-mycoupon div.wp-menu-image").prepend("<i class='fas fa-money-bill-alt' style='padding:10px 0;'></i>");
        })
    </script>
    <?php
}
//類型+ _cate_row_actions=====
add_filter(
    'mycoupon_cate_row_actions',
    function($actions, $tag) {

        //var_dump($tag);
        unset($actions['view']);

        return $actions;
    },
    10,2
);

//AJAX===========================================================================================================

add_action( 'wp_ajax_nopriv_Coupon', 'ajax_Coupon' );
add_action( 'wp_ajax_Coupon', 'ajax_Coupon' );
function ajax_Coupon() {
    $items=$_POST["items"];
    $shippingfee=$_POST["shippingfee"];
    $coupon=$_POST["coupon"];
    $discount=$_POST["discount"];

    $feedback = new MyCoupon($items,$shippingfee,$discount,$coupon);
    $_feedback = $feedback->feedback;
    $_feedback = apply_filters("coupon_feedback",$_feedback);
    echo json_encode($_feedback);
    exit;
}

add_action( 'wp_ajax_nopriv_UpdateCoupon', 'ajax_UpdateCoupon' );
add_action( 'wp_ajax_UpdateCoupon', 'ajax_UpdateCoupon' );
function ajax_UpdateCoupon() {
    if(!isset($_REQUEST["OrderNo"]) || $_REQUEST["OrderNo"] ==""){
        exit;
    }
    if(!isset($_REQUEST["coupon"]) || $_REQUEST["coupon"] ==""){
        exit;
    }
    $coupon=$_REQUEST["coupon"];
    $OrderNo=$_REQUEST["OrderNo"];
    $coupon_id=get_page_by_title($coupon ,"ARRAY_A",  'MyCoupon' );
    if($coupon_id==NULL){
        exit;
    }
    $coupon_id=$coupon_id["ID"];

    $used=get_post_meta( $coupon_id, "used", $single = true );
    $used_Order=get_post_meta( $coupon_id, $key = 'OrderNos', false );
    $Failed_Order=get_post_meta( $coupon_id, $key = 'FailedOrderNos', false );


    $t_id=wp_get_post_terms( $coupon_id, "mycoupon_cate",$args=array( 'fields' => 'ids' )  )[0];

    $number_count_logic = get_term_meta( $t_id, 'number_count_logic', true );

    if($number_count_logic == false || $number_count_logic==""){
        $number_count_logic="check";
    }

    echo $number_count_logic;

    if(isset($_REQUEST["failed_order"]) ){
        if($number_count_logic == "check"){
            if(!in_array($OrderNo, $used_Order)){
                update_post_meta( $coupon_id, "used",$used+1 );
                add_post_meta($coupon_id,"OrderNos", $OrderNo, false);
                echo "C";
                exit;
            }
        }else{

            echo "F";
            exit;
            /*
            if(!in_array($OrderNo, $used_Order)){
              add_post_meta($coupon_id,"OrderNos", $OrderNo, false);
            }
            if(!in_array($OrderNo, $Failed_Order)){
              add_post_meta($coupon_id,"FailedOrderNos", $OrderNo, false);
              echo "F";
              exit;
            }
            */
        }



        echo "Some thing else";
        exit;
    }else{
        if(!in_array($OrderNo, $used_Order)){
            update_post_meta( $coupon_id, "used",$used+1 );
            add_post_meta($coupon_id,"OrderNos", $OrderNo, false);
            echo "S";
        }
        exit;
    }

    /*
      if(!in_array($OrderNo, $used_Order)){
        update_post_meta( $coupon_id, "used",$used+1 );
        add_post_meta($coupon_id,"OrderNos", $OrderNo, false);
        echo "S";
      }else{
        if(isset($_REQUEST["failed_order"]) ){

          if($number_count_logic == "check"){
            if(!in_array($OrderNo, $used_Order)){
              update_post_meta( $coupon_id, "used",$used+1 );
              add_post_meta($coupon_id,"OrderNos", $OrderNo, false);
              echo "C";
              exit;
            }
          }

          if(!in_array($OrderNo, $Failed_Order)){
            if($used>0){
              update_post_meta( $coupon_id, "used",$used-1 );
            }
            add_post_meta($coupon_id,"FailedOrderNos", $OrderNo, false);
          }

        }
        echo "F";
      }
    */
    exit;
}










