# Zeabur WordPress 部署指南

## 問題說明
每次在 Zeabur 重新部署時，`wp-config.php` 會消失，因為 Zeabur 會重新構建容器。

## 解決方案

### 方法 1：使用環境變數（推薦）

1. **在 Zeabur 控制台設定環境變數**
   
   在 Zeabur 專案設定中，新增以下環境變數：

   ```
   # 資料庫設定
   DB_NAME=atomdpc3_superdouble_wp
   DB_USER=你的資料庫使用者
   DB_PASSWORD=你的資料庫密碼
   DB_HOST=你的資料庫主機
   
   # WordPress URL
   WP_HOME=https://你的網域.zeabur.app
   WP_SITEURL=https://你的網域.zeabur.app
   
   # 安全金鑰（請產生新的）
   AUTH_KEY=產生的金鑰
   SECURE_AUTH_KEY=產生的金鑰
   LOGGED_IN_KEY=產生的金鑰
   NONCE_KEY=產生的金鑰
   AUTH_SALT=產生的金鑰
   SECURE_AUTH_SALT=產生的金鑰
   LOGGED_IN_SALT=產生的金鑰
   NONCE_SALT=產生的金鑰
   ```

   > 提示：可以在 https://api.wordpress.org/secret-key/1.1/salt/ 產生新的安全金鑰

2. **wp-config.php 會自動從環境變數讀取設定**
   
   專案中的 `wp-config.php` 和 `wp-config-zeabur.php` 已經設計成從環境變數讀取所有配置。

### 方法 2：使用 Dockerfile（已實作）

Dockerfile 已更新，包含以下功能：

1. **自動複製整個專案**：包括 `wp-config.php`
2. **啟動腳本**：如果 `wp-config.php` 不存在，會自動從 `wp-config-zeabur.php` 複製

### 方法 3：使用 Zeabur 建置命令

在 Zeabur 的建置設定中，可以設定自訂建置命令：

```bash
# 在建置階段確保 wp-config.php 存在
cp wp-config-zeabur.php wp-config.php
```

## 部署步驟

1. **準備資料庫**
   - 在 Zeabur 建立 MySQL 服務
   - 或使用外部資料庫服務

2. **設定環境變數**
   - 在 Zeabur 專案設定中新增上述所有環境變數
   - 確保資料庫連線資訊正確

3. **部署專案**
   - 推送程式碼到 Git
   - Zeabur 會自動部署

4. **驗證部署**
   - 訪問你的網站 URL
   - 確認 WordPress 可以正常運作

## 故障排除

### 如果 wp-config.php 仍然消失

1. 檢查 Zeabur 的建置日誌
2. 確認環境變數已正確設定
3. 使用 `wp-config-zeabur.php` 作為基礎設定檔

### 資料庫連線問題

1. 確認資料庫服務正在運行
2. 檢查環境變數中的資料庫連線資訊
3. 確認資料庫主機地址正確（Zeabur 內部服務通常使用服務名稱）

### HTTPS 重定向問題

`wp-config.php` 已包含 HTTPS 檢測和強制設定：
- 自動檢測 HTTPS 代理標頭
- 強制管理後台使用 HTTPS
- URL 自動切換到 HTTPS

## 持久化儲存

如果需要持久化 `wp-content/uploads` 目錄：

1. 在 Zeabur 建立持久化儲存卷
2. 掛載到 `/var/www/html/wp-content/uploads`

## 安全建議

1. **不要在程式碼中硬編碼敏感資訊**
2. **使用環境變數儲存所有敏感設定**
3. **定期更新安全金鑰**
4. **啟用 Wordfence 或其他安全外掛**

## 相關檔案

- `wp-config.php` - 主要設定檔（從環境變數讀取）
- `wp-config-zeabur.php` - Zeabur 專用設定檔模板
- `Dockerfile` - Docker 容器設定
- `.env.example` - 環境變數範例檔案