<?php
// Database Configuration with Environment Variables Support
// 使用與 WordPress 相同的環境變數，確保一致性
$dbset['url'] = getenv('DB_HOST') ?: getenv('ADMIN_DB_HOST') ?: 'db';
$dbset['db'] = getenv('DB_NAME') ?: getenv('ADMIN_DB_NAME') ?: 'atomdpc3_superdouble';
$dbset['ur'] = getenv('DB_USER') ?: getenv('ADMIN_DB_USER') ?: 'atomdpc3_hcd1983';
$dbset['pw'] = getenv('DB_PASSWORD') ?: getenv('ADMIN_DB_PASSWORD') ?: 'your_password';

// Table names configuration (可選：也可從環境變數讀取)
$dbset['table']['products'] = getenv('ADMIN_TABLE_PRODUCTS') ?: 'products';
$dbset['table']['users'] = getenv('ADMIN_TABLE_USERS') ?: 'users';
$dbset['table']['orders'] = getenv('ADMIN_TABLE_ORDERS') ?: 'orders';
$dbset['table']['settings'] = getenv('ADMIN_TABLE_SETTINGS') ?: 'settings';
$dbset['table']['email'] = getenv('ADMIN_TABLE_EMAIL') ?: 'email';
?>