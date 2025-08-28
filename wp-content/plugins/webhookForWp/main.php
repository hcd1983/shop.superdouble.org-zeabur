<?php
/**
 * Plugin Name:       MyWebhook
 * Version:           0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Dean Huang
 */

add_filter('acf/update_value/type=date_time_picker', 'my_update_value_date_time_picker', 10, 3);

function my_update_value_date_time_picker( $value, $post_id, $field ) {
    return strtotime( $value );
}

$hookList = [
    "https://staging.superdouble.org/recache",
    "https://superdouble.org/recache",
];

add_action('publish_post', 'call_the_endpoint',99,0);
add_action('post_updated', 'call_the_endpoint',99,0);
add_action('save_updated', 'call_the_endpoint',99,0);
function call_the_endpoint(){
    global $hookList;
    foreach($hookList as $key => $hook) {
        $ch = curl_init();
// 設定擷取的URL網址
        curl_setopt($ch, CURLOPT_URL, $hook);
        curl_setopt($ch, CURLOPT_HEADER, false);

//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
// 執行
        $temp=curl_exec($ch);
// 關閉CURL連線
        curl_close($ch);
    }
}

add_action( 'store_updated', 'store_updated_callback', 10, 2 );
function store_updated_callback( $id, $amount ) {
    global $hookList;
    if (get_post_type($id) !== 'group_product' ) return ;

    foreach($hookList as $key => $hook) {
        $hookFixer = $hook."/plan/".$id;
        $ch = curl_init();
// 設定擷取的URL網址
        curl_setopt($ch, CURLOPT_URL, $hookFixer);
        curl_setopt($ch, CURLOPT_HEADER, false);

//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
// 執行
        $temp=curl_exec($ch);
// 關閉CURL連線
        curl_close($ch);
    }
}
