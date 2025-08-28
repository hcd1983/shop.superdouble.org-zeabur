<?php
/**
 * 檢查 Zeabur 檔案路徑結構
 */
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>檢查檔案路徑</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        .path { background: #f0f0f0; padding: 10px; margin: 10px 0; }
        .exists { color: green; }
        .not-exists { color: red; }
    </style>
</head>
<body>
    <h1>Zeabur 檔案路徑檢查</h1>
    
    <h2>當前路徑資訊：</h2>
    <div class="path">
        <strong>__DIR__:</strong> <?php echo __DIR__; ?><br>
        <strong>__FILE__:</strong> <?php echo __FILE__; ?><br>
        <strong>getcwd():</strong> <?php echo getcwd(); ?><br>
        <strong>$_SERVER['DOCUMENT_ROOT']:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
        <strong>$_SERVER['SCRIPT_FILENAME']:</strong> <?php echo $_SERVER['SCRIPT_FILENAME']; ?><br>
    </div>
    
    <h2>WordPress 目錄檢查：</h2>
    <?php
    $paths_to_check = [
        __DIR__ . '/wp-content/uploads',
        __DIR__ . '/html',
        __DIR__ . '/html/wp-content/uploads',
        $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads',
        '/var/www/html/wp-content/uploads',
        '/app/wp-content/uploads',
        dirname(__DIR__) . '/html/wp-content/uploads'
    ];
    
    foreach ($paths_to_check as $path) {
        $exists = file_exists($path);
        $class = $exists ? 'exists' : 'not-exists';
        echo "<div class='path $class'>";
        echo "<strong>$path:</strong> ";
        echo $exists ? '✅ 存在' : '❌ 不存在';
        if ($exists && is_dir($path)) {
            $count = count(glob($path . '/*'));
            echo " (包含 $count 個項目)";
        }
        echo "</div>";
    }
    ?>
    
    <h2>上傳目錄權限：</h2>
    <?php
    $upload_dir = __DIR__ . '/wp-content/uploads';
    if (file_exists($upload_dir)) {
        $perms = fileperms($upload_dir);
        $info = stat($upload_dir);
        ?>
        <div class="path">
            <strong>目錄權限：</strong> <?php echo substr(sprintf('%o', $perms), -4); ?><br>
            <strong>可寫入：</strong> <?php echo is_writable($upload_dir) ? '✅ 是' : '❌ 否'; ?><br>
            <strong>擁有者 UID：</strong> <?php echo $info['uid']; ?><br>
            <strong>群組 GID：</strong> <?php echo $info['gid']; ?><br>
        </div>
        <?php
    }
    ?>
    
    <h2>上傳檔案列表：</h2>
    <div class="path">
        <?php
        if (file_exists($upload_dir)) {
            $items = scandir($upload_dir);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    echo $item . "<br>";
                }
            }
        } else {
            echo "上傳目錄不存在";
        }
        ?>
    </div>
</body>
</html>