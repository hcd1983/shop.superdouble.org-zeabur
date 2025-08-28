<?php

function GetMyStore($pid){
  $ProductsInfo=get_post_meta( $pid, $key = "ProductsInfo", true );
  $ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$pid);
  if($ProductsInfo["usestore"]!=1 || !isset($ProductsInfo["usestore"])){
    return 99999;
  }else{
    if($ProductsInfo["store"] <= 0 || $ProductsInfo["store"]==""){
      return 0;
    }else{
      return $ProductsInfo["store"];
    }
  }
}

add_action( 'wp_ajax_nopriv_UpdateStore', 'ajax_UpdateStore' );
add_action( 'wp_ajax_UpdateStore', 'ajax_UpdateStore' );
function ajax_UpdateStore() {
  
  if(!isset($_REQUEST["items"]) || $_REQUEST["items"] =="" || !is_array($_REQUEST["items"])){
    exit("F");
  }else{
    $items=$_REQUEST["items"];
  }

  if(count($items)==0){
    exit("F");
  }

  foreach ($items as $key => $item) {
  
    if(!isset($item["id"]) || $item["id"] == null || empty($item["id"])){
      continue;
    }

    if (get_post_type($item["id"]) === 'group_product' ) {
        $status = get_field( "status", $item["id"] );
        $store = get_field( "store", $item["id"] );
        if ($status !== 'auto') continue;
        if(isset($_REQUEST["addback"])){
            $new_store = intval($store) + intval($item["amount"]);
        }else{
            $new_store = intval($store) - intval($item["amount"]);
        }
        update_field( 'store', $new_store, $item["id"] );
    } else {
        $ProductsInfo = GetProductInfo($item["id"]);

        if($ProductsInfo["usestore"] == 0 || $ProductsInfo["usestore"]==""){
            continue;
        }

        if(isset($_REQUEST["addback"])){
            $new_store = intval($ProductsInfo["store"]) + intval($item["amount"]);
        }else{
            $new_store = intval($ProductsInfo["store"]) - intval($item["amount"]);
        }

        $ProductsInfo["store"]=$new_store;

        update_post_meta( $item["id"], "ProductsInfo", $ProductsInfo);
    }
    $id = $item["id"];
    $amount = item["amount"];
    do_action( 'store_updated', $id, $amount );

  }

  echo "S";

  exit;
}
