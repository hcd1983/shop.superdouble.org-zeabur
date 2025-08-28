<?php
// Test file for Zeabur deployment
phpinfo();

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "MYSQL_HOST: " . getenv('MYSQL_HOST') . "\n";
echo "MYSQL_DATABASE: " . getenv('MYSQL_DATABASE') . "\n";
echo "MYSQL_USERNAME: " . getenv('MYSQL_USERNAME') . "\n";
echo "</pre>";

echo "<h2>Database Connection Test:</h2>";
$host = getenv('MYSQL_HOST') ?: getenv('DB_HOST') ?: 'mysql.zeabur.internal';
$db = getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: 'zeabur';
$user = getenv('MYSQL_USERNAME') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('MYSQL_PASSWORD') ?: getenv('DB_PASSWORD') ?: '';

echo "Attempting to connect to: $host with database: $db\n";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error;
    } else {
        echo "Successfully connected to MySQL!";
        $conn->close();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>