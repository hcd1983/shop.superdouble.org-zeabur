<?php
add_filter("ajax_Everything","groupProductCoupon",100,6);
function groupProductCoupon($output,$items,$shippingfee,$coupon,$discount,$shipping_method){

    if (isset($output['coupon']) && isset($output['coupon']['status']) && $output['coupon']['status'] === 'S') {
        if (isset($_POST['groupProductItems']) && is_array($_POST['groupProductItems']) && !empty($_POST['groupProductItems'])) {
            $groupProductItems = $_POST['groupProductItems'];
        }else{
            return $output;
        }

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

        if ($term_slug === 'zombiedad') {
            $discount += zombieDadCoupon($groupProductItems);
        }

        if ($term_slug === 'meiwenwang') {
            $discount += meiwenWangCoupon($groupProductItems);
        }

        if ($term_slug === 'book') {
            $discount += bookCoupon($groupProductItems);
        }

        $output['discount'] = $discount;

        return $output;
    } else {
        return $output;
    }
}

function zombieDadCoupon($groupProductItems) {
    $allowedGroupItems = [5392, 5475];
    $discount = 0;

    foreach ($groupProductItems as $key => $groupProductItem) {
        $_discount = 0;
        // 八五折
        if ( in_array($groupProductItem['id'], $allowedGroupItems) ) {
            $_discount += ceil($groupProductItem['price'] * 0.15);
        }
        $discount += $_discount * $groupProductItem['amount'];
    }

    return $discount;
}


// meiwenWang coupon

function meiwenWangCoupon ($groupProductItems) {
    //    九折主體 配件 九五折
//    3918: 全省激配
    $ninetyWithAndAccNinetyFive = [3918];
//    九五折含配件
//    3801：標準版 4139 墨綠限量 4098 日本經典 4225 快卡經典 4172 配件
    $ninetyFiveWithAcc = [3801, 4139, 4098, 4225, 4172];

    $discount = 0;

    foreach ($groupProductItems as $key => $groupProductItem) {
        $_discount = 0;
//                九折主體 配件 九五折
        if ( in_array($groupProductItem['id'], $ninetyWithAndAccNinetyFive) ) {
            $_discount += ceil($groupProductItem['group']['price'] * 0.1);
            $plan_item = $groupProductItem['plan_item'];
            foreach ($plan_item as $_key => $item) {
                if ($item['price'] === 0) continue;
                $_discount += ceil($item['price'] * 0.05);
            }
        }
//                九五折含配件
        if ( in_array($groupProductItem['id'], $ninetyFiveWithAcc) ) {
            $_discount += ceil($groupProductItem['price'] * 0.05);
        }

        $discount += $_discount * $groupProductItem['amount'];
    }

    return $discount;
}

// meiwenWang coupon

function bookCoupon ($groupProductItems) {
    //    九折主體 配件 九五折
//    3918: 全省激配
    $ninetyWithAndAccNinetyFive = [3918];
//    九五折含配件
//    3801：標準版 4139 墨綠限量 4098 日本經典 4225 快卡經典 4172 配件
    $ninetyFiveWithAcc = [3801, 4139, 4098, 4225, 4172];

    $discount = 0;

    foreach ($groupProductItems as $key => $groupProductItem) {
        $_discount = 0;
//                九折主體 配件 九五折
        if ( in_array($groupProductItem['id'], $ninetyWithAndAccNinetyFive) ) {
            $_discount += ceil($groupProductItem['group']['price'] * 0.1);
            $plan_item = $groupProductItem['plan_item'];
            foreach ($plan_item as $_key => $item) {
                if ($item['price'] === 0) continue;
                $_discount += ceil($item['price'] * 0.05);
            }
        }
//                九五折含配件
        if ( in_array($groupProductItem['id'], $ninetyFiveWithAcc) ) {
            $_discount += ceil($groupProductItem['price'] * 0.05);
        }

        $discount += $_discount * $groupProductItem['amount'];
    }

    return $discount;
}
