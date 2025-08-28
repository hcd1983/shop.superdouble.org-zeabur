<?php
ini_set('display_errors', 1);
include("functions.php" );

function send_buy_email_coupon_owner_email ($OrderNo) {
    global $db_conn,$dbset,$TransCode,$wordpress_setting;
    $useLaravel = useLaravelMail();

    if (!$useLaravel) {
        echo "This mail will not be sent.";
        return;
    }

    $sql = "SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."' LIMIT 1";
    $res = doSQLgetRow($sql);
    if (count($res) === 0) return ;
    $orderinfo =$res[0];
    $buyer = unserialize($orderinfo["buyer"]);
    $buyer = urldecodeArray($buyer);
    $buyerMail = $buyer["bemail"];
    $coupon = $orderinfo["coupon"];
    if (!$coupon) {
        echo "This order may not use coupon.";
        return;
    }

    $couponOwner = getCouponOwners();

    foreach ($couponOwner as $key => $owner) {
        $_coupon = $owner["coupon"];
        $_mail = $owner["email"];
        if ($coupon === $_coupon) {
            $res = laravelSystemMail($OrderNo, $buyerMail, $_mail);
        }
        echo $res;
    }
}

function getCouponOwners() {
    return [
        [
            "coupon" => "2023",
            "email" => "hcd@mojopot.com"
        ],
    ];
}

send_buy_email_coupon_owner_email ("SD2306119l");

echo "start test 2";

exit;
