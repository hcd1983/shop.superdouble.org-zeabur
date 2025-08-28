<?php
// 簡單的 PHP 測試檔案
echo "<h1>PHP is working!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// 測試 MySQL 擴展
echo "<h2>MySQL Extensions:</h2>";
echo "<p>mysqli: " . (extension_loaded('mysqli') ? 'Loaded' : 'Not loaded') . "</p>";
echo "<p>pdo_mysql: " . (extension_loaded('pdo_mysql') ? 'Loaded' : 'Not loaded') . "</p>";

// 環境變數
echo "<h2>WordPress Database Environment Variables:</h2>";
echo "<pre>";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '***已設定***' : '未設定') . "\n";
echo "\n";
echo "MYSQL_HOST: " . getenv('MYSQL_HOST') . "\n";
echo "MYSQL_DATABASE: " . getenv('MYSQL_DATABASE') . "\n";
echo "MYSQL_USERNAME: " . getenv('MYSQL_USERNAME') . "\n";
echo "MYSQL_PASSWORD: " . (getenv('MYSQL_PASSWORD') ? '***已設定***' : '未設定') . "\n";
echo "</pre>";

// 測試 WordPress 資料庫連線
echo "<h2>WordPress Database Connection Test:</h2>";
$host = getenv('DB_HOST') ?: 'mysql.zeabur.internal';
$database = getenv('DB_NAME') ?: 'superdouble';
$username = getenv('DB_USER') ?: 'superdouble';
$password = getenv('DB_PASSWORD') ?: '';

echo "<p>Testing connection to: $host / Database: $database / User: $username</p>";

if (extension_loaded('mysqli')) {
    $mysqli = @new mysqli($host, $username, $password, $database);
    if ($mysqli->connect_error) {
        echo "<p style='color:red'>Connection failed: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✅ Successfully connected to WordPress database!</p>";
        
        // 檢查 wp_ 開頭的資料表
        $tables = $mysqli->query("SHOW TABLES LIKE 'wp_%'");
        if ($tables && $tables->num_rows > 0) {
            echo "<p style='color:green'>✅ Found " . $tables->num_rows . " WordPress tables</p>";
        } else {
            echo "<p style='color:orange'>⚠️ No WordPress tables found (need to install WordPress)</p>";
        }
        $mysqli->close();
    }
} else {
    echo "<p style='color:red'>mysqli extension not loaded!</p>";
}

// 顯示 wp-config.php 實際會使用的值
echo "<h2>wp-config.php would use:</h2>";
echo "<pre>";
echo "DB_NAME: " . (getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: 'superdouble_wp') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: getenv('MYSQL_USERNAME') ?: 'root') . "\n";
echo "DB_HOST: " . (getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: 'mysql.zeabur.internal') . "\n";
echo "</pre>";
?>