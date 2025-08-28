<?php
ini_set('display_errors', 1);
require_once("functions.php" );

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
  "end_at" => date("Y-m-d", $nowTime)
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
$sql = "SELECT * FROM `orders` WHERE `reg_date` >= '{$start_at} 00:00:00' AND `reg_date` <= '{$end_at} 23:59:59'  AND `TranStatus` LIKE 'S' ORDER BY `id` DESC LIMIT 999";
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
            $groupProductsModified[$_item["id"]]["total"] += $_item["amount"];
            $group_item_count += $_item["amount"];
            $group_item_list[] = "{$_item["title"]} x {$_item["amount"]}";
            $all_item = $_item["all_item"];

            // 輕輕背 | 墨綠限量 50 組 4139
            if( $_item["id"] === 4139) {
                // 拿掉 4001 可機洗午餐袋 (BS21010322YE), 3910 真皮提把, 3997 酒精瓶證件夾組
                $all_item = array_filter($all_item, function ($_single_item) {
                    $itemIgnore = [4001, 3910, 3997];
                    return !in_array($_single_item["id"], $itemIgnore);
                });
            }

            // 輕輕背 | 限量日式經典旗艦款 的處理 4098
            if( $_item["id"] === 4098) {
                // 拿掉 4004 深灰羊毛氈雙面筆袋+2布貼 和 4001 可機洗午餐袋 (BS21010322YE), 3910 真皮提把, 3997 酒精瓶證件夾組
                $all_item = array_filter($all_item, function ($_single_item) {
                    $itemIgnore = [4004, 4001, 3910, 3997];
                    return !in_array($_single_item["id"], $itemIgnore);
                });
                // 合併黑包
                // 判斷是否兩個黑 3523 黑包, 3404 黑蓋
                $blackBag = key_search($all_item, "id", 3523 );
                $blackPad = key_search($all_item, "id", 3404 );
                $sameColor = false;
                if( !empty($blackBag) && !empty($blackPad)) {
                    $sameColor = true;
                    // 兩個黑
                    $combined = [
                        "id" => "BS21010107BK",
                        "title" => "日式經典旗艦款-黑色",
                        "partNumber" => "BS21010107BK",
                        "amount" => $blackBag[0]["amount"],
                    ];
                    array_unshift($all_item, $combined);
                    // 移除原本的兩件
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemIgnore = [3523, 3404];
                        return !in_array($_single_item["id"], $itemIgnore);
                    });
                }
                $redBag = key_search($all_item, "id", 3385 );
                $redPad = key_search($all_item, "id", 3393 );
                if(!empty($redBag) && !empty($redPad)) {
                    $sameColor = true;
                    // 兩個紅
                    $combined = [
                        "id" => "BS21010107RD",
                        "title" => "日式經典旗艦款-紅色",
                        "partNumber" => "BS21010107RD",
                        "amount" => 1,
                    ];
                    array_unshift($all_item, $combined);
                    // 移除原本的兩件
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemIgnore = [3385, 3393];
                        return !in_array($_single_item["id"], $itemIgnore);
                    });
                }
                if(!$sameColor) {
                    $japan_different_color = true;
                }
            }
// 激省全配組合的處理 3918
            if( $_item["id"] === 3918) {
//                日期超過 8/5 含 8/5 當天
                if($reg_date >= "2021-08-05 00:00:00"){
                    // 拿掉 3910 - 真皮提把, 4104 - 深灰羊毛氈零錢包,3997 - 酒精瓶證件夾組
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemIgnore = [3910, 4104, 3997];
                        // 如果是選項內的 就不要排除
                        if( isset($_single_item["group_title"]) && $_single_item["group_title"]) {
                            return true;
                        }
                        return !in_array($_single_item["id"], $itemIgnore);
                    });

                    $all_item[] = [
                        "id" => 4104,
                        "title" => "深灰羊毛氈零錢包",
                        "partNumber" => "20010316BK",
                        "amount" => 1,
                    ];

                    $all_item[] = [
                        "id" => 3997,
                        "title" => "酒精瓶證件夾組",
                        "partNumber" => "BS21010321BK",
                        "amount" => 1,
                    ];
                }else {
//                    拿掉 4001 - 可機洗午餐袋, 3997 - 酒精瓶證件夾組, 3910 - 真皮提把
                    $all_item = array_filter($all_item, function ($_single_item) {
                        $itemIgnore = [4001, 3997, 3910];
                        // 如果是選項內的 就不要排除
                        if( isset($_single_item["group_title"]) && $_single_item["group_title"]) {
                            return true;
                        }
                        return !in_array($_single_item["id"], $itemIgnore);
                    });
                    $all_item[] = [
                        "id" => "BS21010802BK",
                        "title" => "官網全配組",
                        "partNumber" => "BS21010802BK",
                        "amount" => 1,
                    ];
                }
            }

//           $all_items 裡面有 4004 深灰羊毛氈雙面筆袋+2布貼 要加入兩件 SD21010323BK + QZ21010325
            foreach ($all_item as $_key => $child_item) {
                if($child_item["id"] === 4004) {
                    $all_item[] = [
                        "id" => "SD21010323BK",
                        "title" => "深灰羊毛氈雙面筆袋+2布貼",
                        "partNumber" => "SD21010323BK",
                        "amount" => 1,
                    ];
                    // 加入布貼
//                    $all_item[] = [
//                        "id" => "QZ21010325",
//                        "title" => "2入布貼",
//                        "partNumber" => "QZ21010325",
//                        "amount" => 1,
//                    ];
                }
            }
//            刪掉 4004 深灰羊毛氈雙面筆袋+2布貼
            $all_item = array_filter($all_item, function ($_single_item) {
                $itemIgnore = [4004];
                return !in_array($_single_item["id"], $itemIgnore);
            });

            foreach ($all_item as $_key => $child_item) {
                if(in_array($child_item["id"], $itemsIgnore)) continue;
                // 把所有背包的管款式加入備註
                $_memo = "";
                if(in_array($child_item["id"], $bagItem)){
                    $_memo = "(含反光雨罩)";
                }
                if(in_array($child_item["id"], $suitItem)){
                    $_memo = "(含長蓋片與雨罩)";
                }

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
    return $order["hasGroupItem"] && $order["groupItemCount"] > 0;
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
            <div class="mb-5">
                <a href="tester-shipping-uploader.php">
                    <div class="py-3 px-3 text-white rounded-lg bg-purple-500 shadow-lg block inline-block">單號上傳器</div>
                </a>
                <a href="tester-shipping-debug.php">
                    <div class="py-3 px-3 text-white rounded-lg bg-yellow-500 shadow-lg block inline-block">8/28 debug 補件</div>
                </a>
            </div>
            <form action="" method="get">
                <div class="flex flex-wrap">
                    <div class="flex flex-wrap">
                        <h3 class="font-bold text-lg mr-2">開始日</h3>
                        <input
                            id="start_at"
                            name="start_at"
                            class="border border-black p-1"
                            type="text"
                            autocomplete="off"
                            value="<?php echo $start_at; ?>" />
                    </div>
                    <div class="px-2"> ~ </div>
                    <div class="flex flex-wrap">
                        <h3 class="font-bold text-lg mr-2">結束日</h3>
                        <input
                            id="end_at"
                            name="end_at"
                            class="border border-black p-1"
                            type="text"
                            autocomplete="off"
                            value="<?php echo $end_at; ?>" />
                    </div>
                    <div class="pl-2">
                        <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded">提交</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="container mx-auto mb-10">
            <div class="grid grid-cols-2 max-w-2xl">
                <excel-export filename="輕輕背萬華出貨(<?php echo "{$start_at} ~ {$end_at}";?>)" bookType="xlsx" :sheet="wanHuaSheet">
                    <button class="py-3 px-6 text-white rounded-lg bg-green-400 shadow-lg block md:inline-block">萬華出貨(<?php echo "{$start_at} ~ {$end_at}";?>)</button>
                </excel-export>
                <excel-export filename="輕輕背凱耀出貨(<?php echo "{$start_at} ~ {$end_at}";?>)" bookType="xlsx" :sheet="kyfSheet">
                    <button class="py-3 px-6 text-white rounded-lg bg-green-500 shadow-lg block md:inline-block">凱耀出貨(<?php echo "{$start_at} ~ {$end_at}";?>)</button>
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
                    <button
                            class="text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none"
                            :class="{
                                'text-blue-500 border-b-2 font-medium border-blue-500': view === 'groups'
                            }"
                            @click="view = 'groups'"
                    >
                        組合統計
                    </button>
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
