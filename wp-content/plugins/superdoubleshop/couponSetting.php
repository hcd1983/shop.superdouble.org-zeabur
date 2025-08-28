<?php
add_action( 'admin_menu', 'add_coupon_setting' );
function add_coupon_setting(){
    $parent_slug = "edit.php?post_type=mycoupon";
    $page_title = "Coupon Generator";
    $menu_title = "Coupon Generator";
    $capability = "administrator";
    $menu_slug = "coupon_generator";
    $function = "coupon_generator_page";

//    add_submenu_page($page_title , $menu_title, $capability, $menu_slug, $function , $icon_url = '', $position = null );
    add_submenu_page( $parent_slug, $page_title,  $menu_title,  $capability, $menu_slug,  $function ,$position = null );
}

function coupon_generator_page(){

    $terms = get_terms( 'mycoupon_cate', array(
        'hide_empty' => false,
    ) );

?>
    <style>
        [v-cloak]{display: none;}
    </style>
    <div class="wrap" id="couponForm" v-cloak>
        <h1>{{title}}</h1>
        <div v-if="message" class="notice notice-success is-dismissible">
            <p>{{message}}</p>
        </div>
        <form method="post" action="?action=coupon_generator" @submit.prevent @submit="submit(e)">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">coupon 類型</th>
                    <td>
                        <select v-model="coupon_type">
                            <option v-for="term in CategoryOpts" :value="term.term_id">{{term.name}}</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        Coupon 開頭
                        <p>(產生器產生的 coupon 開頭，最多 3 字)</p>
                    </th>
                    <td>
                        <input type="text" v-model="prefix" maxlength="3"/>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">生成數量</th>
                    <td>
                        <select v-model="number">
                            <option v-for="i in NumberOpts" :value="i">{{i}}</option>
                        </select>
                    </td>
                </tr>

            </table>
            <button v-if="!loading" class="button button-primary" type="submit">送出</button>
            <div v-else>Loading</div>
<!--            <div>number: {{number}}</div>-->
<!--            <div>category: {{coupon_type}}</div>-->
            
            <div v-if="coupons.length > 0">
                <h2>產生的 coupon</h2>
                <table>
                    <tr v-for="coupon in coupons">
                        <td>{{coupon}}</td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/vue@next"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const AppSetting = {
            data() {
                const NumberOpts = this.range();
                const CategoryOpts = <?php echo json_encode($terms);?>;
                return {
                    Api:window.location.origin + "/wp-admin/",
                    loading:false,
                    title: "Coupon 生成器",
                    prefix:"",
                    NumberOpts:NumberOpts,
                    CategoryOpts:CategoryOpts,
                    coupon_type:CategoryOpts[0].term_id,
                    number:NumberOpts[0],
                    message:"",
                    coupons:[]
                }
            },
            methods:{
                range(){
                    return [10,20,30,40,50,60,70,80,90,100];
                },
                submit(){
                    const self = this;
                    this.loading = true;
                    axios({
                        method: 'post',
                        url: this.Api,
                        data: {
                            action:"coupon_generator",
                            prefix: this.prefix,
                            coupon_type: this.coupon_type,
                            coupon_number: this.number,
                        }
                    })
                    .then(function (response) {
                        self.message = "coupon 產生成功!";
                        alert("coupon 產生成功!");
                        self.coupons = response.data;
                    })
                    .catch(function (error) {
                        self.message = "coupon 產生失敗!"
                        alert("coupon 產生失敗!");
                    })
                    .then(function () {
                        self.loading = false;
                    });
                }
            }
        }

        Vue.createApp(AppSetting).mount('#couponForm')
    </script>
<?php
}