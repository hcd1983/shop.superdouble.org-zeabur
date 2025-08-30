<?php
/**
 * 修復 uploads 目錄權限
 * 確保所有目錄和檔案有正確的權限
 */

$upload_dir = __DIR__ . '/wp-content/uploads/';

if (!is_dir($upload_dir)) {
    die("找不到 uploads 目錄");
}

// 修復權限
exec("chmod -R 777 " . escapeshellarg($upload_dir), $output, $return);

if ($return === 0) {
    echo "✅ 權限修復成功！";
} else {
    echo "❌ 權限修復失敗，請手動執行：<br>";
    echo "<code>chmod -R 777 wp-content/uploads/</code>";
}

// 顯示目錄狀態
echo "<h3>目錄狀態：</h3>";
echo "<pre>";
$dirs = glob($upload_dir . "*", GLOB_ONLYDIR);
foreach ($dirs as $dir) {
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    echo basename($dir) . " - 權限: " . $perms . "\n";
}
echo "</pre>";
?>