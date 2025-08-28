<?php
/**
 * WordPress Uploads 還原工具
 * 上傳並解壓 zip 檔案到 wp-content/uploads
 */

// 設定最大執行時間和記憶體
set_time_limit(600);
ini_set('memory_limit', '512M');

$upload_dir = __DIR__ . '/wp-content/uploads/';
$message = '';
$error = '';

// 處理檔案上傳
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $uploadedFile = $_FILES['zip_file'];
    
    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        $tempFile = $uploadedFile['tmp_name'];
        $fileName = $uploadedFile['name'];
        
        // 確認是 ZIP 檔案
        if (pathinfo($fileName, PATHINFO_EXTENSION) === 'zip') {
            $zip = new ZipArchive();
            
            if ($zip->open($tempFile) === TRUE) {
                // 解壓縮到 uploads 目錄
                $zip->extractTo($upload_dir);
                $zip->close();
                
                $message = "✅ 成功上傳並解壓：$fileName";
                
                // 設定權限
                exec("chmod -R 755 " . escapeshellarg($upload_dir));
                exec("chown -R www-data:www-data " . escapeshellarg($upload_dir));
            } else {
                $error = "❌ 無法開啟 ZIP 檔案";
            }
        } else {
            $error = "❌ 請上傳 ZIP 格式的檔案";
        }
    } else {
        $error = "❌ 上傳失敗，錯誤碼：" . $uploadedFile['error'];
    }
}

// 列出已存在的目錄
$years = [];
if (is_dir($upload_dir)) {
    $yearDirs = glob($upload_dir . '*', GLOB_ONLYDIR);
    foreach ($yearDirs as $yearDir) {
        $year = basename($yearDir);
        if (is_numeric($year)) {
            $months = [];
            $monthDirs = glob($yearDir . '/*', GLOB_ONLYDIR);
            foreach ($monthDirs as $monthDir) {
                $months[] = basename($monthDir);
            }
            $years[$year] = $months;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Uploads 還原工具</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        .upload-form {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        input[type="file"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background: #0073aa;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #005a87;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .directory-list {
            margin: 20px 0;
        }
        .year-group {
            margin: 10px 0;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .month-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
        }
        .month-tag {
            background: #0073aa;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WordPress Uploads 還原工具</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="info">
            <strong>使用說明：</strong>
            <ol>
                <li>在本地使用 <code>./zip_single_month.sh 2023 08</code> 打包單個月份</li>
                <li>上傳打包好的 ZIP 檔案</li>
                <li>系統會自動解壓到正確的位置</li>
            </ol>
        </div>
        
        <div class="upload-form">
            <h2>上傳 ZIP 檔案</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="zip_file" accept=".zip" required>
                <br><br>
                <button type="submit">上傳並解壓</button>
            </form>
        </div>
        
        <div class="directory-list">
            <h2>已還原的目錄</h2>
            <?php if (empty($years)): ?>
                <p>尚未還原任何檔案</p>
            <?php else: ?>
                <?php foreach ($years as $year => $months): ?>
                    <div class="year-group">
                        <strong><?php echo $year; ?> 年</strong>
                        <div class="month-list">
                            <?php foreach ($months as $month): ?>
                                <span class="month-tag"><?php echo $month; ?> 月</span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="info">
            <strong>注意事項：</strong>
            <ul>
                <li>上傳大檔案可能需要較長時間</li>
                <li>確保 PHP 的 upload_max_filesize 和 post_max_size 設定足夠大</li>
                <li>當前限制：<?php echo ini_get('upload_max_filesize'); ?></li>
            </ul>
        </div>
    </div>
</body>
</html>