<?php
/**
 * 修復八進位編碼的中文檔名
 * 處理類似 \350\207\252\347\224\261\346\220\255 這種格式
 */

set_time_limit(600);
ini_set('memory_limit', '512M');

// 檢查上傳目錄
$upload_dir = __DIR__ . '/wp-content/uploads/';
if (!is_dir($upload_dir)) {
    die("錯誤：找不到 uploads 目錄");
}

$action = $_POST['action'] ?? '';
$result = [];

/**
 * 將八進位字串轉換為正常字串
 */
function octal_to_utf8($filename) {
    // 檢查是否包含八進位編碼 (\xxx 格式)
    if (!preg_match('/\\\\[0-7]{3}/', $filename)) {
        return false;
    }
    
    // 替換八進位編碼為實際字元
    $decoded = preg_replace_callback(
        '/\\\\([0-7]{3})/',
        function($matches) {
            return chr(octdec($matches[1]));
        },
        $filename
    );
    
    // 檢查是否為有效的 UTF-8
    if (mb_check_encoding($decoded, 'UTF-8')) {
        return $decoded;
    }
    
    return false;
}

/**
 * 掃描並修復目錄中的八進位檔名
 */
function fix_octal_filenames($directory, $dry_run = true) {
    $result = [
        'fixed' => [],
        'errors' => [],
        'skipped' => []
    ];
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        $oldPath = $file->getPathname();
        $oldName = $file->getFilename();
        $dirPath = $file->getPath();
        
        // 嘗試轉換檔名
        $fixedName = octal_to_utf8($oldName);
        
        if ($fixedName !== false && $fixedName !== $oldName) {
            $newPath = $dirPath . DIRECTORY_SEPARATOR . $fixedName;
            
            // 檢查目標檔案是否已存在
            if (file_exists($newPath)) {
                $result['errors'][] = "目標已存在: $newPath";
                continue;
            }
            
            if (!$dry_run) {
                // 實際重命名
                if (@rename($oldPath, $newPath)) {
                    $result['fixed'][] = [
                        'old' => $oldName,
                        'new' => $fixedName,
                        'path' => $dirPath
                    ];
                } else {
                    $result['errors'][] = "無法重命名: $oldPath";
                }
            } else {
                // 預覽模式
                $result['fixed'][] = [
                    'old' => $oldName,
                    'new' => $fixedName,
                    'path' => $dirPath,
                    'dry_run' => true
                ];
            }
        } else {
            // 不需要修復的檔案
            if (!preg_match('/\\\\[0-7]{3}/', $oldName)) {
                $result['skipped'][] = $oldName;
            }
        }
    }
    
    return $result;
}

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'scan') {
        $result = fix_octal_filenames($upload_dir, true);
    } elseif ($action === 'fix') {
        $result = fix_octal_filenames($upload_dir, false);
    } elseif ($action === 'test') {
        // 測試單個檔名
        $test_name = $_POST['test_name'] ?? '';
        if ($test_name) {
            $decoded = octal_to_utf8($test_name);
            $result['test'] = [
                'input' => $test_name,
                'output' => $decoded ?: '無法解碼',
                'success' => $decoded !== false
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修復八進位編碼檔名</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
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
        .info-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            color: #0d47a1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .test-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button-group {
            margin: 20px 0;
        }
        button {
            background: #0073aa;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background: #005a87;
        }
        button.danger {
            background: #dc3545;
        }
        button.danger:hover {
            background: #c82333;
        }
        .result-box {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            max-height: 500px;
            overflow-y: auto;
        }
        .file-item {
            padding: 10px;
            margin: 5px 0;
            background: white;
            border-left: 4px solid #0073aa;
            border-radius: 3px;
            font-family: monospace;
            font-size: 14px;
        }
        .old-name {
            color: #dc3545;
            word-break: break-all;
        }
        .new-name {
            color: #28a745;
            font-weight: bold;
            word-break: break-all;
        }
        .arrow {
            color: #666;
            margin: 5px 0;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            flex: 1;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
        }
        .example-box {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 修復八進位編碼檔名</h1>
        
        <div class="info-box">
            <strong>📋 問題說明：</strong>
            <p>此工具專門處理顯示為八進位編碼的中文檔名，例如：</p>
            <div class="example-box">
                \350\207\252\347\224\261\346\220\255-120x120.jpg → 自由搭-120x120.jpg
            </div>
            <p>這種情況通常發生在 ZIP 解壓時沒有正確處理 UTF-8 編碼。</p>
        </div>
        
        <!-- 測試區域 -->
        <div class="test-section">
            <h3>🧪 測試解碼</h3>
            <form method="POST">
                <input type="text" name="test_name" 
                       placeholder="輸入要測試的檔名，例如：\350\207\252\347\224\261\346\220\255.jpg"
                       value="<?php echo htmlspecialchars($_POST['test_name'] ?? ''); ?>">
                <br><br>
                <button type="submit" name="action" value="test">測試解碼</button>
            </form>
            
            <?php if (isset($result['test'])): ?>
                <div class="<?php echo $result['test']['success'] ? 'success-box' : 'warning-box'; ?>" style="margin-top: 15px;">
                    <strong>輸入：</strong> <?php echo htmlspecialchars($result['test']['input']); ?><br>
                    <strong>解碼結果：</strong> <?php echo htmlspecialchars($result['test']['output']); ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="warning-box">
            <strong>⚠️ 使用前請注意：</strong>
            <ul>
                <li>建議先執行「掃描」查看將要修改的檔案</li>
                <li>修復前請先備份 uploads 目錄</li>
                <li>此工具只處理八進位編碼（\xxx）格式的檔名</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit" name="action" value="scan">
                    🔍 掃描八進位檔名（預覽）
                </button>
                <button type="submit" name="action" value="fix" class="danger" 
                        onclick="return confirm('確定要修復所有八進位編碼檔名嗎？建議先掃描查看。')">
                    🔄 修復八進位檔名（實際重命名）
                </button>
            </div>
        </form>
        
        <?php if (!empty($result) && !isset($result['test'])): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($result['fixed']); ?></div>
                    <div>需要修復</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($result['errors']); ?></div>
                    <div>錯誤</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($result['skipped']); ?></div>
                    <div>已跳過</div>
                </div>
            </div>
            
            <?php if ($action === 'fix' && empty($result['errors'])): ?>
                <div class="success-box">
                    ✅ 成功修復 <?php echo count($result['fixed']); ?> 個檔案！
                </div>
            <?php endif; ?>
            
            <?php if (!empty($result['fixed'])): ?>
                <h3>📝 修復清單</h3>
                <div class="result-box">
                    <?php foreach ($result['fixed'] as $file): ?>
                        <div class="file-item">
                            <div class="old-name">原始：<?php echo htmlspecialchars($file['old']); ?></div>
                            <div class="arrow">↓</div>
                            <div class="new-name">
                                <?php echo isset($file['dry_run']) ? '將修改為：' : '已修改為：'; ?>
                                <?php echo htmlspecialchars($file['new']); ?>
                            </div>
                            <div style="color: #666; font-size: 12px; margin-top: 5px;">
                                路徑：<?php echo htmlspecialchars($file['path']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($result['errors'])): ?>
                <h3>❌ 錯誤</h3>
                <div class="result-box">
                    <?php foreach ($result['errors'] as $error): ?>
                        <div class="file-item" style="border-left-color: #dc3545;">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>💡 技術說明</h3>
            <p>八進位編碼範例：</p>
            <ul>
                <li><code>\350</code> = 0xE8 (八進位 350 = 十進位 232)</li>
                <li><code>\350\207\252</code> = UTF-8 編碼的「自」字</li>
                <li><code>\347\224\261</code> = UTF-8 編碼的「由」字</li>
            </ul>
        </div>
    </div>
</body>
</html>