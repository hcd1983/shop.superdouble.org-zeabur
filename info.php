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
echo "<h2>Database Environment Variables:</h2>";
echo "<pre>";
echo "MYSQL_HOST: " . getenv('MYSQL_HOST') . "\n";
echo "MYSQL_DATABASE: " . getenv('MYSQL_DATABASE') . "\n";
echo "MYSQL_USERNAME: " . getenv('MYSQL_USERNAME') . "\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "</pre>";

// 測試資料庫連線
echo "<h2>Database Connection Test:</h2>";
$host = getenv('MYSQL_HOST') ?: 'mysql.zeabur.internal';
$database = getenv('MYSQL_DATABASE') ?: 'zeabur';
$username = getenv('MYSQL_USERNAME') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: '';

echo "<p>Trying to connect to: $host</p>";

if (extension_loaded('mysqli')) {
    $mysqli = @new mysqli($host, $username, $password, $database);
    if ($mysqli->connect_error) {
        echo "<p style='color:red'>Connection failed: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>Successfully connected to MySQL!</p>";
        $mysqli->close();
    }
} else {
    echo "<p style='color:red'>mysqli extension not loaded!</p>";
}
?>