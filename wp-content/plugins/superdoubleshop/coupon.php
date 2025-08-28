<?php

add_filter("ajax_Everything","discountEightyPercent",100,6);
function discountEightyPercent($output,$items,$shippingfee,$coupon,$discount,$shipping_method) {

    $_coupon = get_page_by_title($coupon ,"ARRAY_A",  'MyCoupon' );
    $_coupon_id = $_coupon["ID"];
    $args = [
        'fields' => 'slugs'
    ];
    $term_slugs = wp_get_post_terms( $_coupon_id, "mycoupon_cate", $args  );
    if (empty($term_slugs)) {
        return $output;
    }

    $term_slug = $term_slugs[0];

    if($term_slug === 'discounteightypercent') {

        $items = $_POST["items"];
        $itemsAllowed = 0;
        $output['items'] = $items;
        foreach ($items as $key => $item) {
            if ($item['onsale'] == 0) {
                $itemsAllowed += $item['amount'];
            }
        }

        if($itemsAllowed < 2) {
            $output['coupon'] = [
                'status' => 'F',
                'message' => '購買兩件原價商品取得折扣'
            ];
        } else {
            $totalPrice = 0;
            foreach ($items as $key => $item) {
                if ($item['onsale'] == 1) {
                    $totalPrice += $item['saleprice'] * $item['amount'];
                    continue;
                }
                $totalPrice += $item['price'] * $item['amount'];
                $discount += floor($item['price'] * $item['amount'] * 1/5);
            }

        }
        if ($totalPrice - $discount < 1200) {
            $output['shippingfee'] = 120;
        }
        $output['discount'] = $discount;
    }

    return $output;
}

add_filter("ajax_Everything","new_discount_type",200,6);

function new_discount_type($output,$items,$shippingfee,$coupon,$discount,$shipping_method) {

    $_coupon = get_page_by_title($coupon ,"ARRAY_A",  'MyCoupon' );
    $_coupon_id = $_coupon["ID"];
    $terms = wp_get_post_terms( $_coupon_id, "mycoupon_cate" );
    if (!count($terms)) {
        return $output;
    }
    $use_discount_group = get_field("use_discount_group", $terms[0]);
    if (!$use_discount_group) {
        return $output;
    }
    $discount_group = get_field("discount_group", $terms[0]);

    foreach ($discount_group as $key => $_discount) {
        $_price = $_discount["price"];
        $_ids = $_discount["products"];
        if ($_discount["discount_type"] === "set_discount") {
            foreach ($items as $_key => $item) {
                if (in_array($item["id"], $_ids)) {
                    $output["discount"] += ($_price * $item["amount"]);
                }
            }
        } elseif ($_discount["discount_type"] === "set_price") {
            foreach ($items as $_key => $item) {
                if (in_array($item["id"], $_ids)) {
                    $__price = $item["price"] - $_price;
                    $output["discount"] += ($__price * $item["amount"]);
                }
            }
        }
    }

    return $output;
}
