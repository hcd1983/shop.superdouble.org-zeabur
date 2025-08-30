<?php
/**
 * 中文檔名修復工具
 * 掃描並修復已存在的亂碼檔名
 */

// 引入中文檔名處理類別
require_once __DIR__ . '/chinese_filename_handler.php';

// 設定執行時間
set_time_limit(600);
ini_set('memory_limit', '512M');

// 檢查上傳目錄
$upload_dir = __DIR__ . '/wp-content/uploads/';
if (!is_dir($upload_dir)) {
    die("錯誤：找不到 uploads 目錄");
}

$action = $_POST['action'] ?? '';
$result = [];

// 處理表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'scan') {
        // 掃描模式（不實際修改）
        $result = ChineseFilenameHandler::fixDirectoryFilenames($upload_dir, true);
    } elseif ($action === 'fix') {
        // 修復模式（實際重命名）
        $result = ChineseFilenameHandler::fixDirectoryFilenames($upload_dir, false);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>中文檔名修復工具</title>
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
        }
        .file-item.error {
            border-left-color: #dc3545;
            background: #ffe6e6;
        }
        .file-item.skipped {
            border-left-color: #ffc107;
            background: #fff9e6;
        }
        .old-name {
            color: #721c24;
            word-break: break-all;
        }
        .new-name {
            color: #155724;
            font-weight: bold;
            word-break: break-all;
        }
        .arrow {
            color: #666;
            margin: 0 10px;
        }
        .stats {
            padding: 15px;
            background: #e9ecef;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        .tab-button {
            padding: 8px 16px;
            background: #e9ecef;
            border: none;
            border-radius: 5px 5px 0 0;
            cursor: pointer;
        }
        .tab-button.active {
            background: #0073aa;
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 中文檔名修復工具</h1>
        
        <div class="warning">
            <strong>⚠️ 使用前請注意：</strong>
            <ul>
                <li>建議先執行「掃描」查看將要修改的檔案</li>
                <li>修復前請先備份 uploads 目錄</li>
                <li>修復後可能需要更新資料庫中的檔案路徑</li>
            </ul>
        </div>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit" name="action" value="scan">
                    🔍 掃描亂碼檔案（安全）
                </button>
                <button type="submit" name="action" value="fix" class="danger" 
                        onclick="return confirm('確定要修復所有亂碼檔名嗎？建議先掃描查看。')">
                    🔄 修復亂碼檔名（會重命名檔案）
                </button>
            </div>
        </form>
        
        <?php if (!empty($result)): ?>
            <div class="stats">
                <h3>📊 處理統計</h3>
                <ul>
                    <li>需要修復的檔案：<?php echo count($result['fixed']); ?> 個</li>
                    <li>跳過的檔案：<?php echo count($result['skipped']); ?> 個</li>
                    <li>錯誤：<?php echo count($result['errors']); ?> 個</li>
                </ul>
            </div>
            
            <?php if ($action === 'fix' && empty($result['errors'])): ?>
                <div class="success">
                    ✅ 成功修復 <?php echo count($result['fixed']); ?> 個檔案！
                </div>
            <?php endif; ?>
            
            <div class="tab-buttons">
                <button class="tab-button active" onclick="showTab('fixed')">
                    需要修復 (<?php echo count($result['fixed']); ?>)
                </button>
                <button class="tab-button" onclick="showTab('errors')">
                    錯誤 (<?php echo count($result['errors']); ?>)
                </button>
                <button class="tab-button" onclick="showTab('skipped')">
                    已跳過 (<?php echo count($result['skipped']); ?>)
                </button>
            </div>
            
            <!-- 需要修復的檔案 -->
            <div id="fixed" class="tab-content active">
                <div class="result-box">
                    <?php if (empty($result['fixed'])): ?>
                        <p>沒有需要修復的檔案</p>
                    <?php else: ?>
                        <?php foreach ($result['fixed'] as $file): ?>
                            <div class="file-item">
                                <div class="old-name">原始：<?php echo htmlspecialchars($file['old']); ?></div>
                                <div class="arrow">↓</div>
                                <div class="new-name">
                                    <?php if (isset($file['dry_run']) && $file['dry_run']): ?>
                                        將修改為：
                                    <?php else: ?>
                                        已修改為：
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($file['new']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 錯誤 -->
            <div id="errors" class="tab-content">
                <div class="result-box">
                    <?php if (empty($result['errors'])): ?>
                        <p>沒有錯誤</p>
                    <?php else: ?>
                        <?php foreach ($result['errors'] as $error): ?>
                            <div class="file-item error">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 跳過的檔案 -->
            <div id="skipped" class="tab-content">
                <div class="result-box">
                    <?php if (empty($result['skipped'])): ?>
                        <p>沒有跳過的檔案</p>
                    <?php else: ?>
                        <?php foreach ($result['skipped'] as $file): ?>
                            <div class="file-item skipped">
                                <?php echo htmlspecialchars($file); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>💡 支援的編碼</h3>
            <p>本工具會自動嘗試以下編碼轉換：</p>
            <ul>
                <li>GBK（Windows 簡體中文）</li>
                <li>BIG5（Windows 繁體中文）</li>
                <li>GB2312（簡體中文）</li>
                <li>UTF-8（標準編碼）</li>
                <li>CP950（Windows 繁體中文代碼頁）</li>
                <li>CP936（Windows 簡體中文代碼頁）</li>
            </ul>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            // 隱藏所有標籤內容
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.classList.remove('active');
            });
            
            // 移除所有按鈕的 active 類
            document.querySelectorAll('.tab-button').forEach(function(btn) {
                btn.classList.remove('active');
            });
            
            // 顯示選中的標籤
            document.getElementById(tabName).classList.add('active');
            
            // 設置按鈕為 active
            event.target.classList.add('active');
        }
    </script>
</body>
</html>