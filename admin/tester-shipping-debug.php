<?php
ini_set('display_errors', 1);
require_once("functions.php" );

// 已經出貨的單號
$shippedOrderNos = ["SD2108179e","SD210817mR","SD210817ma","SD2108170x","SD2108174f","SD210817H0","SD210816ro","SD21081606","SD21081556","SD2108158j","SD210815K8","SD210815xS","SD2108159R","SD2108152B","SD2108155M","SD2108141Y","SD210814oc","SD210814jY","SD2108131F","SD210813ca","SD210813HX","SD210813yj","SD21081342","SD210813ow","SD21081351","SD210812YK","SD21081227","SD210812EJ","SD210812yn","SD2108123M","SD2108126n","SD210811pK","SD210811gR","SD21081141","SD210811TW","SD2108114c","SD21081079","SD210810N6","SD2108109g","SD2108104y","SD2108098g","SD2108095a","SD210809H1","SD2108099i","SD210809XA","SD210809n1","SD2108091M","SD210809WI","SD210809fE","SD210809c6","SD21080975","SD210808lf","SD210808f3","SD210808Ut","SD2108084V","SD2108088W","SD2108083w","SD2108089V","SD210808O8","SD210807T8","SD210807WA","SD210807M3","SD210807oI","SD2108078f","SD210807u7","SD2108062Q","SD210806L2","SD210806nd","SD210806dG","SD2108068M","SD210806CS","SD2108069f","SD210805CY","SD210805sW","SD210805t4","SD210805H0","SD210805u5","SD2108051j","SD21080471","SD210804Ju","SD2108047d","SD21080415","SD210804A9","SD21080466","SD2108045O","SD210804Ys","SD210804o6","SD210804vF","SD21080452","SD210804Xh","SD210804IT","SD2108038W","SD210803FS","SD2108038r","SD210803JO","SD210803gx","SD21080317","SD210803el","SD210803OO","SD2108031J","SD2108035b","SD210802wp","SD2108020M","SD210802G0","SD210802gv","SD210802HM","SD210802YW","SD210801Kh","SD210801ug","SD2108012f","SD210801Yw","SD210801IL","SD210801v0","SD2108018F","SD210731wC","SD2107314E","SD2107313w","SD210731g6","SD210731kQ","SD210731kB","SD210731jc","SD210731uZ","SD210731p4","SD21073061","SD21073093","SD2107302H","SD210730Bb","SD210730wH","SD210730hA","SD210730da","SD2107293m","SD2107291J","SD21072971","SD2107293q","SD210728nW","SD210728NG","SD210728I4","SD210728Oq","SD210728TP","SD210728AH","SD2107285d","SD210728ME","SD210727m4","SD210727hx","SD210727hb","SD210727xl","SD210727mb","SD2107278L","SD210727JU","SD210727nW","SD21072739","SD210726YS","SD21072648","SD210726w5","SD210726jZ","SD210726PW","SD210726H1","SD210726KC","SD2107261k","SD2107258W","SD210725w6","SD210725Jc","SD210725wb","SD210725eN","SD2107259X","SD2107253h","SD210725g6","SD210725BR","SD210725Nb","SD21072499","SD210724Hu","SD210724RY","SD210724Lp","SD210724vo","SD2107242C","SD210724gP","SD210724ao","SD210723z4","SD210723lz","SD2107234c","SD21072314","SD210723JG","SD210723C4","SD21072355","SD210723Tc","SD210723L4","SD21072344","SD210723fk","SD21072378","SD210722u3","SD210722Wk","SD2107226K","SD21072222","SD210722bP","SD2107222v","SD210722xj","SD210722n3","SD210722Dn","SD210722Gf","SD210722CT","SD21072237","SD2107224W","SD210722m3","SD210721rL","SD2107213R","SD21072129","SD210721oj","SD210721t4","SD210721jE","SD210721S0","SD2107217x","SD21072138","SD2107211w","SD210721AO","SD210721xI","SD21072133","SD21072136","SD210721e1","SD2107217q","SD210721CO","SD2107210m","SD210721Ia","SD2107216s","SD210721hp","SD2107218o","SD210721RF","SD210721oG","SD210721OI","SD210721zw","SD21072145","SD21072071","SD210720WN","SD210720nu","SD210720S0","SD2107204V","SD2107206u","SD21072075","SD210720mB","SD210720v8","SD210720c0","SD210720A7","SD21072030","SD210720ET","SD210720F3","SD210720j4","SD2107203E","SD210720iU","SD2107201k","SD2107201y","SD2107193y","SD210719Q3","SD2107194Q","SD21071946","SD21071968","SD210719h8","SD210719C9","SD21071910","SD210719ij","SD210719y4","SD210719nK","SD210719B8","SD210719p0","SD210719yb","SD210719He","SD210719rD","SD210719m5","SD21071977","SD2107194O","SD21071945","SD210719ds","SD210719vx","SD210719y1","SD210719br","SD210719d7","SD2107183t","SD210718T2","SD2107187f","SD21071856","SD210718A7","SD21071849","SD210718kv","SD210718He","SD210718JY","SD210718vj","SD210718ow","SD210718iK","SD210718jB","SD210718y6","SD210718E5","SD210718f1","SD210718Og","SD2107186H","SD210718L9","SD210718dU","SD210718lM","SD210718Av","SD210718C7","SD210718O0","SD210718Fw","SD210718yn","SD210718WP","SD210718pd","SD210718G8","SD2107185A","SD210718Xh","SD2107184G","SD21071882","SD210718PC","SD210718hO","SD210718lQ","SD210718Ts","SD210718S0","SD210718o7","SD210718Xx","SD210718n2","SD21071814","SD2107186m","SD2107184e","SD210717DO","SD2107178r","SD210717oy","SD210717E9","SD2107171x","SD210717pT","SD210717t8","SD2107176H","SD210717lh","SD210717Yz","SD210717vr","SD210717Ii","SD210717XQ","SD210717e4","SD210717l8","SD210717v0","SD21071788","SD2107170C","SD210717Mp","SD210717A6","SD2107178g","SD210717P0","SD210717uo","SD210717CR","SD210717Mo","SD21071787","SD21071713","SD210717D0","SD210717Hq","SD21071792","SD210717h1","SD210717EV","SD210717Z1","SD210717u1","SD21071745","SD210717PG","SD210717kc","SD210717eK","SD21071783","SD210717vP","SD21071772","SD2107177d","SD2107174C","SD2107170J","SD2107171W","SD210717Lr","SD210717R3","SD210717im","SD210717S3","SD2107172N","SD210717Ji","SD210717X4","SD210717Zc","SD210717cA","SD210717l6","SD210717w2","SD210717MT","SD210717Hx","SD210717xO","SD21071781","SD210717zE","SD210717X3","SD210717Ny","SD210717Z5","SD21071776","SD21071789","SD21071701","SD210717ec","SD210717ih","SD21071743","SD210717H7","SD210717Jc","SD210717Ys","SD210717cB","SD210717Wu","SD210717s8","SD2107162y","SD2107169S","SD2107162c","SD2107157a","SD21071557","SD2107151Y","SD210715fG","SD210715O3","SD210715iV","SD210715bm","SD210714Lk","SD2107144b","SD21071499","SD210714Tg","SD210714N6","SD210713Oz","SD2107132k","SD2107137v","SD2107132M","SD2107120G","SD210712mp","SD210712E6","SD210711C6","SD210711tp","SD2107114m","SD210711Y7","SD210711H7","SD210711Vm","SD21071182","SD2107112H","SD21071138","SD210711k4","SD210711v8","SD210710CL","SD210710PG","SD2107101Z","SD210710p6","SD2107106I","SD210710GR","SD210710UM","SD21071002","SD2107107q","SD210710N9","SD210709Pv","SD210709ET","SD210709A7","SD210709p3","SD2107091I","SD210709Ie","SD210709w5","SD2107092z","SD21070986","SD210709Dx","SD210709wy","SD210709YB","SD21070980","SD210709y3","SD210708DS","SD210708lH","SD210708X0","SD2107082D","SD210708wC","SD210708sj","SD210708L2","SD210707l2","SD210707d9","SD2107074z","SD21070702","SD210707dT","SD210707aa","SD210706H8","SD210706Fw","SD210706Gz","SD210706SU","SD210706PG","SD210706RN","SD210706hT","SD2107060n","SD210706gD","SD2107069u","SD21070580","SD21070521","SD2107053F","SD210704dz","SD21070470","SD210703Or","SD210703Mk","SD210703m7","SD210703lS","SD210703B6","SD210703fU","SD210703dg","SD2107024m","SD2107020M","SD210701r0","SD210701yp","SD2107014o","SD210701Q8","SD210701x4","SD210701l5","SD210701p3","SD21070122"];
$shippedOrderNosFixed = "'".join("','", $shippedOrderNos)."'";
function key_search($array, $key, $value)
{
    $results = [];

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, key_search($subarray, $key, $value));
        }
    }
    return $results;
//    return count($results) ? true : false;
}

$nowTime = time();
$defaultSetting = [
  "start_at" => "2021-06-22",
  "end_at" => "2021-08-28"
];

$start_at = isset($_GET["start_at"]) && $_GET["start_at"] ? $_GET["start_at"] : $defaultSetting["start_at"];
$end_at = isset($_GET["end_at"]) && $_GET["end_at"] ? $_GET["end_at"] : $defaultSetting["end_at"];

// 取得 組合商品
$baseUrl = "https://shop.superdouble.org/wp-json/wp/v2/group_product";
$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
$result = curl_exec($ch);
curl_close($ch);
$groupProducts = json_decode($result, true);
$groupProductsModified = [];
foreach ($groupProducts as $key => $groupProduct) {
    $groupProductsModified[$groupProduct["id"]] = [
        "id" => $groupProduct["id"],
        "title" => $groupProduct["title"]["rendered"],
        "total" => 0,
    ];
}

// 取得 wp 產品
$baseUrl = "https://shop.superdouble.org/product_json/";
$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
$result = curl_exec($ch);
curl_close($ch);
$products = json_decode($result, true);

//整理 products 格式
$productsModified = [];
foreach ($products as $key => $product) {
    $partNumber = $product['part_number'] ? $product['part_number'] : 'not provided';
    $productsModified[$product['id']] = [
      "id" => $product['id'],
      "title" => html_entity_decode($product['title']),
      "partNumber" => $partNumber,
      "total" => 0,
    ];
}

// 手動加入官網全配組，id 用貨號
$productsModified["BS21010802BK"] = [
    "id" => "BS21010802BK",
    "title" => "官網全配組",
    "partNumber" => "BS21010802BK",
    "total" => 0,
];

$productsModified["SD21010323BK"] = [
    "id" => "SD21010323BK",
    "title" => "深灰羊毛氈雙面筆袋+2布貼",
    "partNumber" => "SD21010323BK",
    "total" => 0,
];

$productsModified["QZ21010325"] = [
    "id" => "QZ21010325",
    "title" => "2入布貼",
    "partNumber" => "QZ21010325",
    "total" => 0,
];

$productsModified["BS21010107RD"] = [
    "id" => "BS21010107RD",
    "title" => "日式經典旗艦款-紅色",
    "partNumber" => "BS21010107RD",
    "total" => 0,
];

$productsModified["BS21010107BK"] = [
    "id" => "BS21010107BK",
    "title" => "日式經典旗艦款-黑色",
    "partNumber" => "BS21010107BK",
    "total" => 0,
];


// 取得訂單
$sql = "SELECT * FROM `orders` WHERE `reg_date` >= '{$start_at} 00:00:00' AND `reg_date` <= '{$end_at} 23:59:59'  AND `TranStatus` LIKE 'S' AND `OrderNo` IN ({$shippedOrderNosFixed}) ORDER BY `id` DESC LIMIT 999";
$data = doSQLgetRow($sql);
$_data = array_map(function($order, $key) {
    global $productsModified, $groupProductsModified;
    $itemsIgnore = [4073, 3869];
    $groupWithBag = [3801, 3918, 4098, 4139, 4225];
    $bagItem = [3385, 3412, 3521, 3523, 3525 ];
    $suitItem = [3897, "BS21010107BK", "BS21010107RD"];
    extract($order);
    $buyer = unserialize($buyer);
    $receiver = unserialize($receiver);
    extract($buyer);
    extract($receiver);
    $has_receiver = isset($rname);
    $name =  $has_receiver ? $rname : $bname;
    $tel = $has_receiver ? $rphone : $bphone;
    $zip = $has_receiver ? $rzip : $zip;
    $_addresss = $has_receiver ? $raddress : $address;
    $_addresss = trim(str_replace("台灣","", urldecode($_addresss)));
    $CargoList = unserialize($CargoList);
    $items = [];
    $group_item_count = 0;
    $group_item_list = [];
    $japan_different_color = false;
    $admin_memo = [];
    $shipping_memo = [];
    // 計算所有有包包的方案數量
    foreach ($groupWithBag as $key => $gWithBag) {
        $withBag = key_search($CargoList, "id", $gWithBag);
        if(!empty($withBag)) {
            $shipping_memo[] = "反光雨罩在書包背後口袋";
            break;
        }
    }


    foreach ($CargoList as $key => $cargo) {
        $_item = $cargo;
        $_item["title"] = html_entity_decode(urldecode($cargo["title"]));
        if (isset($_item["is_group_item"]) && $_item["is_group_item"] == true) {
//            if(!in_array($_item["id"], [3801,3918,4098,4139,4172])) {
//                var_dump($_item);
//                exit;
//            }
            // 不是 激省全配 全部跳過
            if( $_item["id"] !== 3918) {
                continue;
            }
            $groupProductsModified[$_item["id"]]["total"] += $_item["amount"];
            $group_item_count += $_item["amount"];
            $group_item_list[] = "{$_item["title"]} x {$_item["amount"]}";
            $all_item = $_item["all_item"];

// 激省全配組合的處理 3918
            if( $_item["id"] === 3918) {
//                日期超過 8/5 含 8/5 當天
                if($reg_date >= "2021-08-05 00:00:00"){
//                    加回大選項內的 3910 - 真皮提把
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemAccept = [3910];
                        // 排除不是這兩項裡面的
                        if(!in_array($_single_item["id"], $itemAccept)) {
                            return false;
                        }
                        // 排除不是大選項裡面的
                        if( !isset($_single_item["group_title"]) || !$_single_item["group_title"]) {
                            return false;
                        }
                        return true;
                    });

                    if(count($all_item) > 0) {
                        $admin_memo[] = "8/5 後加購";
                    }

                }else {
//                    加回大選項內的 4001 - 可機洗午餐袋, 3910 - 真皮提把
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemAccept = [4001, 3910];
                        // 排除不是這兩項裡面的
                        if(!in_array($_single_item["id"], $itemAccept)) {
                            return false;
                        }
                        // 排除不是大選項裡面的
                        if( !isset($_single_item["group_title"]) || !$_single_item["group_title"]) {
                            return false;
                        }
                        return true;
                    });

                    if(count($all_item) > 0) {
                        $admin_memo[] = "8/5 前加購";
                    }
                }
            }

            foreach ($all_item as $_key => $child_item) {
                if(in_array($child_item["id"], $itemsIgnore)) continue;
                $_memo = "";
                $productsModified[$child_item["id"]]["total"] += $child_item["amount"] * $_item["amount"];
                $items[] = [
                    "id" => $child_item["id"],
                    "title" =>  html_entity_decode($child_item["title"]).$_memo,
                    "amount" => $child_item["amount"] * $_item["amount"],
                    "partNumber" => $productsModified[$child_item["id"]]["partNumber"]
                ];
            }
        }else{
//            忽略一般商品
            continue;
//            $productsModified[$_item["id"]]["total"] += $_item["amount"];
//            $items[] = [
//                "id" => $_item["id"],
//                "title" => $_item["title"],
//                "amount" => $_item["amount"],
//                "partNumber" => $productsModified[$_item["id"]]["partNumber"]
//            ];
        }
    }
    // 最後整合
    $items_mixed = [];
    foreach ($items as $key => $item) {
        $item_id = $item["id"];
        if(!isset($items_mixed[$item_id])){
            $items_mixed[$item_id] = $item;
        }else{
            $items_mixed[$item_id]["amount"] += $item["amount"];
        }
    }
    if($japan_different_color) {
        $admin_memo[] = "日式背包顏色和蓋片不一樣";
    }
    return [
            "key" => $key,
            "id" => $id,
            "OrderNo" => $OrderNo,
            "name" => urldecode($name),
            "zip" => urldecode($zip),
            "address" => $_addresss,
            "tel" => urldecode($tel),
            "items" => $items,
            "itemsMixed" => array_values($items_mixed),
            "hasGroupItem" => $group_item_count ? true : false,
            "groupItemCount" => $group_item_count,
//        記錄購買的方案
            "groupItemList" => $group_item_list,
            "japanDifferentColor" => $japan_different_color,
            "adminMemo" => $admin_memo,
            "shippingMemo"=>$shipping_memo,
            "shippingCompany" => $shippingCompany,
            "ShippingNum" => $ShippingNum

//            "buyer"=> $buyer,
//            "receiver" => $receiver,
    ];
}, $data, array_keys($data));

$_data = array_values(array_filter($_data, function ($order){
//    1.要有群組商品 2.群舞商品數量大於 0 3.篩選過後的物件組數要大於 0
    return $order["hasGroupItem"] && $order["groupItemCount"] > 0 && count($order["items"]) > 0;
}));

$_productsData = array_values(array_filter($productsModified, function ($product){
    return $product["total"] > 0;
}));

$_groupsData = array_values(array_filter($groupProductsModified, function ($product){
    return $product["total"] > 0;
}));

?>
<!doctype html>
<html lang="zh-TW">
<head>
<meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                 <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>Download</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <!--Regular Datatables CSS-->
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <!--Responsive Extension Datatables CSS-->
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <!--Datatables -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/supan_example@1.0.6/lib/PikazJsExcel.umd.min.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        const originData = <?php echo json_encode($_data); ?>

        const productsData = <?php echo json_encode($_productsData); ?>

        const groupData = <?php echo json_encode($_groupsData); ?>
    </script>
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>
<body>
  <div id="loading" class="w-screen h-screen flex items-center justify-center">
      <h3 class="text-3xl font-bold">Loading...</h3>
  </div>
  <div id="app" class="pb-20 pt-20" v-cloak>
        <div class="container mx-auto mb-10">
            <h3 class="text-3xl">8/28 Debug 補配件</h3>
            <p class="text-base whitespace-pre-line text-red-500 mb-10">錯誤原因：
                8/5(含當日) 後： 漏算加購的 真皮提把
                8/5 前：漏算加購的 可機洗午餐袋, 真皮提把
            </p>
            <p class="text-base whitespace-pre-line text-green-500">處置方式：
                8/5(含當日) 後： 有加購真皮提把 補寄 真皮提把
                8/5 前：有加購 可機洗午餐袋, 真皮提把 補寄 可機洗午餐袋, 真皮提把
            </p>
        </div>
        <div class="container mx-auto mb-10">
            <div class="mb-5">
                <a href="tester-shipping.php">
                    <div class="py-3 px-3 text-white rounded-lg bg-purple-500 shadow-lg block inline-block">回到總表</div>
                </a>
            </div>
        </div>
        <div class="container mx-auto mb-10">
            <div class="div max-w-2xl">
                <excel-export class="inline-block mr-3" filename="輕輕背萬華出貨-補寄(8/23)" bookType="xlsx" :sheet="wanHuaSheet">
                    <button class="py-3 px-6 text-white rounded-lg bg-green-400 shadow-lg block md:inline-block">萬華出貨</button>
                </excel-export>
                <excel-export class="inline-block" filename="輕輕背凱耀出貨-補寄(8/23)" bookType="xlsx" :sheet="kyfSheet">
                    <button class="py-3 px-6 text-white rounded-lg bg-green-500 shadow-lg block md:inline-block">凱耀出貨</button>
                </excel-export>
            </div>
        </div>
        <div class="container mx-auto mb-10">
            <div class="bg-white">
                <nav class="flex flex-col sm:flex-row">
                    <button
                            class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none"
                            :class="{
                                'text-blue-500 border-b-2 font-medium border-blue-500': view === 'orders'
                            }"
                            @click="view = 'orders'"
                    >
                        訂單 ({{ dataLength }})
                    </button>
                    <button
                            class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none"
                            :class="{
                                'text-blue-500 border-b-2 font-medium border-blue-500': view === 'products'
                            }"
                            @click="view = 'products'"
                    >
                        產品統計
                    </button>
<!--                    <button-->
<!--                            class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none"-->
<!--                            :class="{-->
<!--                                'text-blue-500 border-b-2 font-medium border-blue-500': view === 'groups'-->
<!--                            }"-->
<!--                            @click="view = 'groups'"-->
<!--                    >-->
<!--                        組合統計-->
<!--                    </button>-->
                </nav>
            </div>
        </div>

        <div v-show="view==='groups'" class="container mx-auto">
          <div class="max-w-5xl">
              <table id="groupsData" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                  <thead>
                  <tr>
                      <th data-priority="1">ID</th>
                      <th data-priority="2">品項</th>
                      <th data-priority="4">數量</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="{id, title, total} in groupData">
                      <td>{{ id }}</td>
                      <td>{{ title }}</td>
                      <td>{{ total }}</td>
                  </tr>
                  </tbody>
              </table>
          </div>
        </div>

        <div v-show="view==='products'" class="container mx-auto">
            <div class="max-w-5xl">
                <table id="porductsData" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead>
                    <tr>
                        <th data-priority="1">ID</th>
                        <th data-priority="2">品項</th>
                        <th data-priority="3">貨號</th>
                        <th data-priority="4">數量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="{id, title, partNumber, total} in productsData">
                        <td>{{ id }}</td>
                        <td>{{ title }}</td>
                        <td>{{ partNumber }}</td>
                        <td>{{ total }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-show="view==='orders'" class="container mx-auto">
            <table id="orderData" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                <thead>
                    <tr>
                        <th data-priority="1">ID</th>
                        <th data-priority="2">單號</th>
                        <th data-priority="3">姓名</th>
                        <th data-priority="4">郵遞區號</th>
                        <th data-priority="5">住址</th>
                        <th data-priority="6">電話</th>
                        <th data-priority="7">物件</th>
                        <th data-priority="8">方案備註</th>
                        <th data-priority="8">出貨</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="{ id, OrderNo, name, zip, address, tel, itemsMixed, groupItemList, adminMemo, shippingCompany, ShippingNum } in originData">
                        <td>{{ id }}</td>
                        <td>{{ OrderNo }}</td>
                        <td>{{ name }}</td>
                        <td>{{ zip }}</td>
                        <td>{{ address }}</td>
                        <td>{{ tel }}</td>
                        <td>
                            <div v-for="({id, title, partNumber, amount}, idx) in itemsMixed">
                                {{ title }} ({{ partNumber }}) x {{ amount }}
                            </div>
                        </td>
                        <td>
                            <div v-for="(gItem, idx) in groupItemList">
                               {{ gItem }}
                            </div>
                            <div v-for="memo in adminMemo" class="text-red-500">
                                {{ memo }}
                            </div>
                        </td>
                        <td>
                            <div v-if="shippingCompany">{{ shippingCompany }}</div>
                            <div v-if="ShippingNum">{{ ShippingNum }}</div>
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
    <script>
        const ExcelExport = PikazJsExcel.ExcelExport
        const globalStyle = {
                    font: {
                        name: "黑体",
                    },
                    alignment:{
                        horizontal: "left",
                    }
                }
        const _app = {
            el:"#app",
            components: { ExcelExport },
            data() {
                return {
                    view:'orders',
                    productsData,
                    originData,
                    groupData,
                }
            },
            mounted(){
                this.loaded()
                this.$refs.productsTable = $('#groupsData').DataTable( {
                    responsive: true,
                    paging: false,
                } ).columns.adjust().responsive.recalc();

                this.$refs.productsTable = $('#porductsData').DataTable( {
                    responsive: true,
                    paging: false,
                } ).columns.adjust().responsive.recalc();

                this.$refs.orderTable = $('#orderData').DataTable( {
                    order: [[ 0, 'desc' ]],
                    pageLength: 100,
                    responsive: true
                } ).columns.adjust().responsive.recalc();

                $( "#start_at" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });

                $( "#end_at" ).datepicker({
                    dateFormat: "yy-mm-dd"
                });
            },
            methods: {
                loaded() {
                    document.getElementById('loading').remove()
                },
                colName(n){
                    let ordA = 'a'.charCodeAt(0);
                    let ordZ = 'z'.charCodeAt(0);
                    let len = ordZ - ordA + 1;

                    let s = "";
                    while(n >= 0) {
                        s = String.fromCharCode(n % len + ordA) + s;
                        n = Math.floor(n / len) - 1;
                    }
                    return s;
                },
                headColStyle(tHeader) {
                    const titleCellStyle = tHeader.map((val, idx) => {
                        const colName = this.colName(idx) + '1'
                        return {
                            cell: colName.toUpperCase(),
                            font:{
                                color: {
                                    rgb: "ff0000",
                                },
                                bold: true,
                            }
                        }
                    })
                    return titleCellStyle
                }
            },
            computed: {
                dataLength() {
                    return this.originData.length
                },
                productSheet() {
                    const productsData = this.productsData
                    const tHeader = productsData.map(({title, partNumber}) => {
                        return `${title}(${partNumber})`;
                    })

                    let data = {}
                    productsData.forEach(({total}, idx) => {
                        data[idx] = total
                    })
                    const tbData = [data]
                    const titleCellStyle = this.headColStyle(tHeader)
                    const dataKeys = [];
                    for(let i=0; i < tHeader.length ; i ++) {
                        dataKeys.push(i)
                    }
                    return {
                        tHeader,
                        table:tbData,
                        keys: dataKeys,
                        sheetName: "商品數量",
                        globalStyle,
                        cellStyle: titleCellStyle,
                    }

                },
                wanHuaSheet() {
                    const originData = this.originData
                    const tHeader = ["編號", "收件人姓名", "收件人電話1", "收件人電話2", "收件人地址", "件數", "才數", "配送時間", "指定到貨日期", "貨品內容", "貨品SKU", "Brew問卷備註", "嘖嘖備註", "系統資訊", "訂單編號", "贊助選項", "加購總金額", "訂單成立時間", "訂單總金額", "加碼贊助", "管理員備註"]
                    const tbData = originData.map((data, idx) => {
                        const { id, OrderNo, name, zip, address, tel, itemsMixed, groupItemList, adminMemo } = data
                        const addressFixed = `${zip} ${address}`
                        const itemContent = itemsMixed.map((item,_idx) => {
                            return `${item['title']} X${item['amount']}`
                        }).join(' / ')
                        const itemSKU = itemsMixed.map((item,_idx) => {
                            return `${item['partNumber']} X${item['amount']}`
                        }).join(' / ')
                        const adminMemoRender = adminMemo.join("\r\n")
                        return {
                            number: idx + 1,
                            name,
                            tel,
                            zip,
                            addressFixed,
                            itemContent,
                            itemSKU,
                            OrderNo,
                            adminMemoRender,
                            empty:""
                        }
                    })

                    const titleCellStyle = this.headColStyle(tHeader)

                    return [
                        {
                            tHeader,
                            table:tbData,
                            keys:["number", "name", "tel", "empty", "addressFixed", "empty", "empty", "empty", "empty", "itemContent", "itemSKU", "empty", "empty", "empty", "OrderNo", "empty", "empty", "empty", "empty", "empty", "adminMemoRender"],
                            sheetName:"出貨單",
                            globalStyle,
                            cellStyle: titleCellStyle,
                        },
                        this.productSheet
                    ]
                },
                kyfSheet() {
                    const originData = this.originData
                    const tHeader = ["出貨單號", "姓名", "郵遞區號", "地址", "電話", "商品編號", "商品名稱", "銷售金額(單價)", "折扣金額", "統一編號", "發票收件人姓名", "發票郵遞區號", "發票收件人地址", "急件程度", "是否安裝DM", "併件編號", "發票列印方式", "發票號碼", "發票檢查號碼", "數量", "發票備註", "發票日期", "客戶訂單編號", "發票抬頭", "夜間電話", "行動電話", "供應廠商代號", "供應廠商email", "流水號", "會員編號", "會員名稱", "發票未稅金額合計", "發票稅額合計", "發票金額合計", "代收貨款總金額", "空值1", "空值2", "空值3", "出貨單備註"]
                    const allItems = [];
                    originData.forEach(({OrderNo, name, zip, address, tel, itemsMixed, adminMemo}, idx) => {

                        itemsMixed.forEach((item, _idx) => {
                            const { title, partNumber, amount, id } = item
                            const itemMemo = _idx === 0 ? adminMemo.join("\r\n") : ''
                            const basicItem = {
                                id,
                                OrderNo,
                                name,
                                zip,
                                address,
                                tel,
                                title,
                                partNumber,
                                amount,
                                itemMemo
                            };
                            allItems.push(basicItem)
                        })
                    })

                    const tbData = allItems.map((data, idx) => {
                        const { id, OrderNo, name, zip, address, tel, title, partNumber, itemMemo, amount } = data
                        return {
                            number: idx + 1,
                            name,
                            tel,
                            zip,
                            address,
                            OrderNo,
                            itemMemo,
                            partNumber,
                            title,
                            amount,
                            empty:""
                        }
                    })

                    const titleCellStyle = this.headColStyle(tHeader)

                    return [
                        {
                            tHeader,
                            table:tbData,
                            keys:["OrderNo", "name", "zip", "address", "tel", "partNumber", "title", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "amount", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "empty", "itemMemo"],
                            sheetName:"出貨單",
                            globalStyle,
                            cellStyle: titleCellStyle,
                        },
                        this.productSheet
                    ]
                }
            }
        }

        var app = new Vue(
            _app
        )
    </script>
</body>
</html>
<?php
exit;
