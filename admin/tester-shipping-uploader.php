<?php
ini_set('display_errors', 1);
require_once("functions.php" );
if (!isLogin()):
?>
<h1>請先登入</h1>
<a href="<?php echo $wordpress_setting["url"].'wp-admin';?>">使用 WP 登入</a><br/>
<a href="login.php">使用 Admin 登入</a>
<?php
    exit();
endif;
if(isset($_POST['action']) && $_POST['action'] === 'execute') {
    $data = $_POST['data'];
    try {
        echo "<div><button onClick='ok()'>OK</button></div>";
        foreach ($data as $key => $single) {
            if(!isset($single['OrderNo']) || !$single['OrderNo']) continue;
            $sql = "UPDATE `orders` SET `isShipped` = 'S', `shippingCompany`='{$single['shippingCompany']}', `ShippingNum`='{$single['ShippingNum']}' WHERE `OrderNo` LIKE '{$single['OrderNo']}'";
            $result = mysqli_query($db_conn, $sql);
            echo $single['OrderNo'].' --  更新程序完成<br>';
        }
        echo '全部更新完成'."<br>";
    }catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit();
    }
?>
    <script>
        function ok() {
            location.href = ''
        }
        // location.href = ''
    </script>
<?php
    exit;
}
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
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
</head>
<body>
    <div id="app" class="pb-20 pt-20" v-cloak>
        <div class="container mx-auto mb-10">
            <div class="flex items-center gap-3">
                <a href="https://docs.google.com/spreadsheets/d/1DFQuCxtsHndhRJUqDj53V3rVDsZadvHV14hyhasw4aI/edit#gid=0" target="_blank">
                    <div class="py-3 px-3 text-white rounded-lg bg-purple-500 shadow-lg block inline-block">範本 1(請下載為 .xlsx 檔案進行編輯)</div>
                </a>
                <a href="https://docs.google.com/spreadsheets/d/19wVYiJsk2rUtCfapYXwQ1Q8jiHzaDeTGumwWzDxO2iA/edit#gid=1466289357" target="_blank">
                    <div class="py-3 px-3 text-white rounded-lg bg-purple-500 shadow-lg block inline-block">範本 2(請下載為 .xlsx 檔案進行編輯)</div>
                </a>
            </div>
            <div v-if="haveData">
<!--                <excel-import :on-success="onSuccess" :on-error="onError" class="mr-2 inline-block">-->
<!--                    <button class="py-3 px-6 text-white rounded-lg bg-green-400 shadow-lg block md:inline-block">匯入</button>-->
<!--                </excel-import>-->
                <button @click="clear" class="py-3 px-3 text-white rounded-lg bg-red-500 shadow-lg block inline-block">清除</button>
                <button @click="submit" class="my-3 py-3 px-6 text-white rounded-lg bg-blue-500 shadow-lg inline-block">確認無誤</button>
            </div>
        </div>
        <div class="container mx-auto mb-10">
            <div v-if="haveData">
                <form id="updateForm" action="#" method="post">
                    <input name="action" type="hidden" value="execute" />
                    <template v-for="(dt, idx) in sheetDataModified">
                        <input type="hidden" :name="`data[${idx}][OrderNo]`" :value="dt.OrderNo">
                        <input type="hidden" :name="`data[${idx}][ShippingNum]`" :value="dt.ShippingNum">
                        <input type="hidden" :name="`data[${idx}][shippingCompany]`" :value="dt.shippingCompany">
                    </template>
<!--                    <button type="submit" class="my-3 py-2 px-6 text-white rounded-lg bg-blue-500 shadow-lg block md">確認無誤</button>-->
                </form>
            </div>
            <div class="max-w-5xl">
                <div v-if="haveData" class="mb-3">
                    <h3 class="text-black text-lg">共 {{ sheetDataModified.length }} 筆資料</h3>
                </div>
                <table v-if="haveData" id="sheetData" class="stripe hover min-w-full divide-y divide-gray-200" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">訂單編號</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">物流編號</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">物流公司</th>
                    </tr>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="{OrderNo, ShippingNum, shippingCompany} in sheetDataModified">
                        <td class="px-6 py-4 whitespace-nowrap">{{ OrderNo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ ShippingNum }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ shippingCompany }}</td>
                    </tr>
                    </tbody>
                    </thead>
                </table>
                <excel-import v-else :on-success="onSuccess" :on-error="onError">
                    <div class="cursor-pointer bg-gray-100 py-32 px-5 flex justify-center items-center border border-gray-700 border-dashed rounded-2xl">
                        <div class="text-center">
                            <h3>請選擇檔案</h3>
                            <div v-if="sheetData.length" class="text-red-500">檔案有誤，請確定欄位是否包含 訂單編號</div>
                        </div>
                    </div>
                </excel-import>

            </div>
        </div>
    </div>
    <script>
        const ExcelExport = PikazJsExcel.ExcelExport
        const ExcelImport = PikazJsExcel.ExcelImport
        const _app = {
            el:"#app",
            components: { ExcelExport, ExcelImport },
            data() {
                return {
                    data: null,
                }
            },
            mounted(){
                // this.startTable()
            },
            methods: {
                submit() {
                  document.getElementById('updateForm').submit()
                },
                clear() {
                    this.data = null
                },
                onSuccess(data, file){
                    this.data = data
                    // console.log(data)
                    // console.log(this.$refs.productsTable)
                    // if(this.$refs.productsTable) this.$refs.productsTable.destroy()
                    // this.startTable()
                },
                onError() {
                    alert('上傳失敗')
                },
                startTable() {
                    this.$refs.productsTable = $('#sheetData').DataTable( {
                        responsive: true,
                        paging: false,
                        data: this.sheetDataModified,
                        columns: [
                            { data: 'OrderNo' },
                            { data: 'ShippingNum' },
                            { data: 'shippingCompany' },
                        ]
                    } ).columns.adjust().responsive.recalc();
                }
            },
            computed: {
                haveData() {
                  return this.sheetDataModified.length
                },
                sheetData() {
                    if( !this.data) return []
                    return this.data[0].data
                },
                sheetDataModified() {
                    return this.sheetData.map((data) => {
                        let shippingCompany = data['物流公司']
                        let ShippingNum = (data['物流編號'] || data['寄出單號']).trim().replace(/ +/g, ' ')
                        if (!shippingCompany) {
                            const numSplit = ShippingNum.split(' ')
                            shippingCompany = numSplit[0]
                            ShippingNum = numSplit[1] || ''
                        }
                        return {
                            OrderNo: data['訂單編號'],
                            ShippingNum,
                            shippingCompany
                        }
                    }).filter(({OrderNo}) => {
                        return typeof OrderNo !== 'undefined' && OrderNo.trim() != ''
                    })
                },
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
