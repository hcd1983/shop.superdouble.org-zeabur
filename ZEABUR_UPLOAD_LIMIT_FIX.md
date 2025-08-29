# 解決 Zeabur "413 Request Entity Too Large" 錯誤

## 問題描述
在使用 `upload_restore.php` 上傳大檔案時，遇到 Nginx 的 413 錯誤，表示請求實體太大。

## 解決方案

### 方案 1：聯繫 Zeabur 支援（推薦）
Zeabur 使用 Nginx 作為反向代理，預設限制可能是 1MB。需要請求他們：
1. 提升 `client_max_body_size` 到至少 100MB
2. 或詢問是否有環境變數可以配置此設定

### 方案 2：分割上傳
將大的 zip 檔案分割成較小的部分：

```bash
# 分割成 50MB 的檔案
split -b 50M uploads_2024.zip uploads_2024_part_

# 會產生 uploads_2024_part_aa, uploads_2024_part_ab 等檔案
```

然後分別上傳並解壓。

### 方案 3：使用命令列工具
如果有 SSH 或終端機存取權限：

```bash
# 直接在伺服器上下載並解壓
wget https://your-backup-location/uploads.zip
unzip uploads.zip -d wp-content/uploads/
```

### 方案 4：使用 FTP/SFTP
透過 FTP 客戶端直接上傳解壓後的檔案，避免 HTTP 上傳限制。

### 方案 5：建立分塊上傳工具
建立一個支援分塊上傳的 PHP 腳本（見 `chunked_upload.php`）。

## 已嘗試的配置

### 1. PHP 配置（.user.ini）- ✅ 已設定
```ini
upload_max_filesize = 800M
post_max_size = 800M
max_execution_time = 1200
```

### 2. Apache 配置（.htaccess）- ✅ 已設定
```apache
php_value upload_max_filesize 100M
php_value post_max_size 100M
```

### 3. Nginx 配置 - ❌ 需要 Zeabur 端設定
```nginx
client_max_body_size 100M;
```

## Zeabur 特定解決方案

### 在 zeabur.yaml 中嘗試以下配置：

```yaml
# 選項 1：環境變數（如果支援）
environment:
  - NGINX_CLIENT_MAX_BODY_SIZE=100M

# 選項 2：自訂 Nginx 配置（如果支援）
nginx:
  client_max_body_size: 100M
```

### 或在 Dockerfile 中（如果使用）：

```dockerfile
# 建立 Nginx 配置
RUN echo "client_max_body_size 100M;" > /etc/nginx/conf.d/upload.conf
```

## 臨時解決方案

使用已經壓縮好的月份檔案（在 `zipped_uploads/` 目錄中），這些檔案較小，應該可以上傳：

1. 上傳 `uploads_2024_01.zip`（較小的月份檔案）
2. 逐月上傳並解壓
3. 避免一次上傳整個年度的檔案

## 聯繫 Zeabur 支援時提供的資訊

```
服務類型：PHP WordPress 應用
問題：上傳檔案時遇到 "413 Request Entity Too Large"
需求：提升 Nginx client_max_body_size 限制到 100MB
當前配置：
- PHP upload_max_filesize: 800M
- PHP post_max_size: 800M
- 但 Nginx 層級限制了請求大小
```

## 檢查當前限制

建立 `check_limits.php` 來檢查實際限制：

```php
<?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

// 檢查 Nginx 限制（如果可以）
if (isset($_SERVER['SERVER_SOFTWARE'])) {
    echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
}
?>
```