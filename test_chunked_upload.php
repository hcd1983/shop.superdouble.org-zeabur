<?php
/**
 * 測試分塊上傳配置
 */

header('Content-Type: text/html; charset=UTF-8');

// 檢查各項設定
$checks = [
    'PHP 版本' => PHP_VERSION,
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'session 狀態' => session_status() === PHP_SESSION_ACTIVE ? '啟用' : '未啟用',
    'temp 目錄' => sys_get_temp_dir(),
    'temp 目錄可寫' => is_writable(sys_get_temp_dir()) ? '是' : '否',
];

$upload_dir = __DIR__ . '/wp-content/uploads/';
$temp_dir = __DIR__ . '/wp-content/uploads/temp/';

$dir_checks = [
    'uploads 目錄存在' => is_dir($upload_dir) ? '是' : '否',
    'uploads 可寫' => is_writable($upload_dir) ? '是' : '否',
    'temp 目錄存在' => is_dir($temp_dir) ? '是' : '否',
    'temp 可寫' => is_writable($temp_dir) ? '是' : '否',
];

// 嘗試建立 temp 目錄
if (!is_dir($temp_dir)) {
    @mkdir($temp_dir, 0777, true);
    $dir_checks['temp 目錄建立'] = is_dir($temp_dir) ? '成功' : '失敗';
}

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>分塊上傳測試</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
        .test-area {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background: #0073aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #005a87;
        }
    </style>
</head>
<body>
    <h1>分塊上傳測試</h1>
    
    <h2>系統設定</h2>
    <table>
        <?php foreach ($checks as $key => $value): ?>
        <tr>
            <th><?php echo $key; ?></th>
            <td><?php echo $value; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>目錄權限</h2>
    <table>
        <?php foreach ($dir_checks as $key => $value): ?>
        <tr>
            <th><?php echo $key; ?></th>
            <td class="<?php echo $value === '是' || $value === '成功' ? 'success' : 'error'; ?>">
                <?php echo $value; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <div class="test-area">
        <h2>測試小檔案上傳</h2>
        <p>建立一個測試文字檔案並嘗試上傳：</p>
        <button onclick="testUpload()">測試上傳</button>
        <div id="result" style="margin-top: 20px;"></div>
    </div>
    
    <script>
        function testUpload() {
            const testData = new Blob(['測試檔案內容'], {type: 'text/plain'});
            const formData = new FormData();
            formData.append('action', 'upload_chunk');
            formData.append('filename', 'test.txt');
            formData.append('chunk_index', '0');
            formData.append('total_chunks', '1');
            formData.append('chunk', testData);
            
            fetch('chunked_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                document.getElementById('result').innerHTML = 
                    '<pre>回應內容：\n' + text + '</pre>';
                
                try {
                    const json = JSON.parse(text);
                    document.getElementById('result').innerHTML += 
                        '<pre>解析成功：\n' + JSON.stringify(json, null, 2) + '</pre>';
                } catch (e) {
                    document.getElementById('result').innerHTML += 
                        '<p class="error">JSON 解析失敗：' + e.message + '</p>';
                }
            })
            .catch(error => {
                document.getElementById('result').innerHTML = 
                    '<p class="error">錯誤：' + error + '</p>';
            });
        }
    </script>
</body>
</html>