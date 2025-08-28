<?php
/**
 * WordPress 網址修復腳本
 * 用於修復部署後的重新導向問題
 * 
 * 使用方法：
 * 1. 修改下面的 $new_url 為你的新網址
 * 2. 將此檔案放在 WordPress 根目錄
 * 3. 在瀏覽器中執行：http://你的網址/fix-redirect.php
 * 4. 執行完成後刪除此檔案
 */

// 載入 WordPress
require_once('wp-load.php');

// ===== 請修改這裡的設定 =====
$new_url = 'http://localhost'; // 修改為你的新網址（不要加斜線）
// ==============================

// 檢查是否有管理員權限（安全性考量）
if (!current_user_can('manage_options')) {
    die('請先登入管理員帳號');
}

echo "<h1>WordPress 網址修復工具</h1>";

// 1. 顯示目前的設定
$old_home = get_option('home');
$old_siteurl = get_option('siteurl');

echo "<h2>目前設定：</h2>";
echo "<p>Home URL: $old_home</p>";
echo "<p>Site URL: $old_siteurl</p>";

// 2. 更新資料庫中的 URL
if (isset($_POST['fix_url'])) {
    update_option('home', $new_url);
    update_option('siteurl', $new_url);
    
    // 更新文章內容中的 URL
    global $wpdb;
    
    // 更新文章內容
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)",
        $old_home,
        $new_url
    ));
    
    // 更新文章 GUID
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET guid = REPLACE(guid, %s, %s)",
        $old_home,
        $new_url
    ));
    
    // 更新 postmeta
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
        $old_home,
        $new_url,
        '%' . $wpdb->esc_like($old_home) . '%'
    ));
    
    echo "<h2 style='color: green;'>✅ 網址已更新為：$new_url</h2>";
    echo "<p>請清除瀏覽器快取並重新整理頁面</p>";
    echo "<p><strong>重要：請立即刪除此檔案！</strong></p>";
} else {
    echo "<h2>準備更新到新網址：$new_url</h2>";
    echo '<form method="post">';
    echo '<input type="submit" name="fix_url" value="確認更新" style="padding: 10px 20px; font-size: 16px; background: #0073aa; color: white; border: none; cursor: pointer;">';
    echo '</form>';
}

echo "<hr>";
echo "<h3>其他注意事項：</h3>";
echo "<ul>";
echo "<li>如果使用快取外掛，請清除所有快取</li>";
echo "<li>檢查 .htaccess 檔案是否有硬編碼的路徑</li>";
echo "<li>如果問題仍然存在，可能需要手動編輯 wp-config.php</li>";
echo "</ul>";
?>