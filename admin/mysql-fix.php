<?php
// MySQL 連線測試和資料庫初始化
require_once('dbset.php');

echo "<h1>MySQL 連線和資料庫測試</h1>";

// 測試連線
$mysqli = new mysqli($dbset['url'], $dbset['ur'], $dbset['pw'], $dbset['db']);

if ($mysqli->connect_error) {
    die("連線失敗: " . $mysqli->connect_error);
}

echo "<p style='color:green'>✅ 成功連線到 MySQL!</p>";
echo "<p>MySQL 版本: " . $mysqli->server_info . "</p>";

// 設定字符集
if ($mysqli->set_charset("utf8mb4")) {
    echo "<p style='color:green'>✅ 字符集設定為 utf8mb4</p>";
} else {
    echo "<p style='color:red'>❌ 字符集設定失敗</p>";
}

// 測試簡單查詢
$result = $mysqli->query("SELECT 1");
if ($result) {
    echo "<p style='color:green'>✅ 基本查詢測試成功</p>";
} else {
    echo "<p style='color:red'>❌ 基本查詢失敗: " . $mysqli->error . "</p>";
}

// 列出所有資料表
echo "<h2>資料庫中的資料表：</h2>";
$tables = $mysqli->query("SHOW TABLES");
if ($tables) {
    echo "<ul>";
    while ($row = $tables->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>沒有找到資料表或查詢失敗</p>";
}

// 檢查必要的資料表是否存在
$required_tables = ['products', 'users', 'orders', 'settings', 'email'];
echo "<h2>檢查必要資料表：</h2>";
foreach ($required_tables as $table) {
    $check = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($check && $check->num_rows > 0) {
        echo "<p style='color:green'>✅ $table 表存在</p>";
    } else {
        echo "<p style='color:orange'>⚠️ $table 表不存在</p>";
    }
}

$mysqli->close();
?>