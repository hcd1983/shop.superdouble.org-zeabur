<?php
/**
 * 中文編碼測試工具
 * 測試系統對各種編碼的支援情況
 */

header('Content-Type: text/html; charset=UTF-8');

// 測試字串
$test_strings = [
    'UTF-8' => '測試中文檔名',
    'BIG5' => iconv('UTF-8', 'BIG5', '測試中文檔名'),
    'GBK' => iconv('UTF-8', 'GBK', '測試中文檔名'),
    'GB2312' => iconv('UTF-8', 'GB2312', '测试中文档名')
];

// 檢查系統資訊
$system_info = [
    'PHP Version' => PHP_VERSION,
    'Operating System' => PHP_OS,
    'Default Charset' => ini_get('default_charset'),
    'mbstring.internal_encoding' => ini_get('mbstring.internal_encoding'),
    'iconv Available' => function_exists('iconv') ? 'Yes' : 'No',
    'mbstring Available' => function_exists('mb_convert_encoding') ? 'Yes' : 'No',
    'ZipArchive Available' => class_exists('ZipArchive') ? 'Yes' : 'No'
];

// 檢查支援的編碼
$supported_encodings = [];
if (function_exists('mb_list_encodings')) {
    $all_encodings = mb_list_encodings();
    $check_encodings = ['UTF-8', 'BIG5', 'GBK', 'GB2312', 'GB18030', 'CP950', 'CP936'];
    foreach ($check_encodings as $enc) {
        $supported_encodings[$enc] = in_array($enc, $all_encodings) ? '✅' : '❌';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中文編碼測試工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
        h2 {
            color: #666;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .test-box {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin: 20px 0;
        }
        .encoding-test {
            padding: 10px;
            margin: 10px 0;
            background: white;
            border-left: 4px solid #0073aa;
        }
        .success {
            color: #155724;
            background: #d4edda;
            border-left-color: #28a745;
        }
        .error {
            color: #721c24;
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .code {
            font-family: monospace;
            background: #e9ecef;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .hex {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔬 中文編碼測試工具</h1>
        
        <h2>系統資訊</h2>
        <table>
            <?php foreach ($system_info as $key => $value): ?>
            <tr>
                <th width="40%"><?php echo $key; ?></th>
                <td><?php echo $value; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>支援的編碼</h2>
        <table>
            <?php foreach ($supported_encodings as $encoding => $supported): ?>
            <tr>
                <th width="40%"><?php echo $encoding; ?></th>
                <td><?php echo $supported; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h2>編碼轉換測試</h2>
        <div class="test-box">
            <?php
            // 測試編碼轉換
            $original = '測試中文檔名_2024年報告.pdf';
            echo "<p>原始字串（UTF-8）：<span class='code'>$original</span></p>";
            
            // 測試不同編碼的轉換
            $encodings = ['GBK', 'BIG5', 'GB2312', 'CP950', 'CP936'];
            foreach ($encodings as $from_enc) {
                echo "<div class='encoding-test'>";
                echo "<strong>$from_enc → UTF-8 轉換測試：</strong><br>";
                
                // 先轉換到目標編碼
                $converted = @iconv('UTF-8', $from_enc . '//IGNORE', $original);
                if ($converted !== false) {
                    // 再轉回 UTF-8
                    $back = @iconv($from_enc, 'UTF-8//IGNORE', $converted);
                    if ($back !== false && $back === $original) {
                        echo "<span class='success'>✅ 成功：$back</span>";
                    } else {
                        echo "<span class='error'>❌ 轉換失敗或資料遺失</span>";
                    }
                    
                    // 顯示 hex
                    echo "<br><span class='hex'>Hex: " . bin2hex(substr($converted, 0, 20)) . "...</span>";
                } else {
                    echo "<span class='error'>❌ 無法轉換到 $from_enc</span>";
                }
                echo "</div>";
            }
            ?>
        </div>
        
        <h2>模擬 ZIP 檔案名稱問題</h2>
        <div class="test-box">
            <?php
            // 模擬常見的亂碼情況
            $test_cases = [
                'Windows ZIP (GBK)' => iconv('UTF-8', 'GBK', '中文檔名.jpg'),
                'Mac ZIP (UTF-8)' => '中文檔名.jpg',
                'Big5 編碼' => iconv('UTF-8', 'BIG5', '繁體中文.doc'),
            ];
            
            foreach ($test_cases as $desc => $filename) {
                echo "<div class='encoding-test'>";
                echo "<strong>$desc：</strong><br>";
                echo "原始（可能亂碼）：" . htmlspecialchars($filename) . "<br>";
                
                // 嘗試修復
                $fixed = '';
                $detected_encoding = '';
                
                // 檢測編碼
                if (mb_check_encoding($filename, 'UTF-8')) {
                    $fixed = $filename;
                    $detected_encoding = 'UTF-8';
                } else {
                    // 嘗試不同編碼
                    foreach (['GBK', 'BIG5', 'GB2312'] as $enc) {
                        $try = @iconv($enc, 'UTF-8//IGNORE', $filename);
                        if ($try && mb_check_encoding($try, 'UTF-8')) {
                            $fixed = $try;
                            $detected_encoding = $enc;
                            break;
                        }
                    }
                }
                
                if ($fixed) {
                    echo "<span class='success'>修復後（$detected_encoding → UTF-8）：$fixed</span>";
                } else {
                    echo "<span class='error'>無法修復</span>";
                }
                echo "</div>";
            }
            ?>
        </div>
        
        <h2>建議</h2>
        <div class="test-box">
            <ol>
                <li>如果看到 ✅，表示系統支援該功能或編碼</li>
                <li>如果看到 ❌，可能需要安裝相關擴展或調整系統設定</li>
                <li>確保 PHP 安裝了 <code>mbstring</code> 和 <code>iconv</code> 擴展</li>
                <li>使用 <code>chinese_filename_handler.php</code> 來自動處理編碼問題</li>
            </ol>
        </div>
    </div>
</body>
</html>