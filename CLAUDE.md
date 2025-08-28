# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 專案概述

這是一個 WordPress 電子商務網站，使用繁體中文為主要語言。專案包含自訂的購物車系統（MyProducts）和多個第三方外掛。

## 核心架構

### WordPress 核心
- WordPress 版本：標準 WordPress 安裝
- 資料庫：MySQL（資料庫名稱：atomdpc3_superdouble_wp）
- PHP 版本：PHP 7.3（根據 .htaccess 配置）

### 主題
- 主要主題：Enfold（商業主題，位於 `/wp-content/themes/enfold/`）
- 包含其他預設主題：twentytwenty, twentytwentyone, twentytwentytwo, twentytwentythree

### 自訂購物車系統（MyProducts 外掛）
主要功能位於 `/wp-content/plugins/MyProducts/`：
- 產品自訂文章類型（Product Post Type）
- 購物車功能（MyCart）
- 運費計算（ShippingFee）
- 優惠券系統（Coupon）
- 電子郵件通知（SendMeMail）
- Stripe 金流整合

關鍵檔案：
- `main.php` - 外掛主要入口
- `NewPostType/` - 自訂文章類型定義
- `MyCartScript.php` - 購物車前端腳本
- `stripe/` - Stripe 支付整合

### 重要外掛
- Advanced Custom Fields Pro - 自訂欄位管理
- WooCommerce 相關配置（在 Enfold 主題中）
- Wordfence - 安全防護
- Post Types Order - 文章排序
- SVG Support - SVG 圖片支援

## 開發相關指令

### 本地開發
由於這是一個 WordPress 站點，沒有標準的建置系統。開發時需要：
1. 確保本地有 PHP 環境
2. 確保 MySQL 資料庫運行
3. WordPress 的偵錯模式可在 `wp-config.php` 中設定

### 檔案權限
WordPress 需要正確的檔案權限才能正常運作：
```bash
# 設定目錄權限
find . -type d -exec chmod 755 {} \;
# 設定檔案權限
find . -type f -exec chmod 644 {} \;
```

## 資料庫操作

備份資料庫：
```bash
mysqldump -u atomdpc3_hcd1983 -p atomdpc3_superdouble_wp > backup.sql
```

## 重要注意事項

1. **安全性**：wp-config.php 包含敏感的資料庫憑證，絕不應提交到公開儲存庫
2. **多語言支援**：網站支援中文和英文，語言切換在 MyProducts 外掛中處理
3. **自訂功能**：大部分電商功能都在自訂的 MyProducts 外掛中，而非使用標準 WooCommerce
4. **主題相依性**：MyProducts 外掛與 Enfold 主題緊密整合（見 `enfold.php`）

## 檔案結構說明

- `/wp-admin/` - WordPress 管理後台
- `/wp-content/plugins/MyProducts/` - 自訂購物車系統
- `/wp-content/themes/enfold/` - 主要網站主題
- `/wp-content/uploads/` - 媒體檔案上傳目錄
- `/wp-includes/` - WordPress 核心檔案

## 偵錯與疑難排解

1. 檢查錯誤日誌：
   - PHP 錯誤：`error_log` 檔案散布在各目錄中
   - WordPress 偵錯：在 `wp-config.php` 設定 `WP_DEBUG` 為 `true`

2. 清除快取：
   - WordPress 快取：在 `wp-config.php` 中 `WP_CACHE` 設為 `false`
   - 瀏覽器快取：開發時使用無痕模式

## 部署注意事項

1. 此網站原本託管在 Linux 伺服器上（從 .htaccess 和路徑可看出）
2. Wordfence WAF 已配置在 .htaccess 中
3. 使用 LiteSpeed 網頁伺服器