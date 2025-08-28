# Docker 環境設定說明

## 快速開始

### 1. 設定環境變數
```bash
cp .env.example .env
# 編輯 .env 檔案，設定資料庫密碼
```

### 2. 啟動服務
```bash
# 建立並啟動所有服務
docker-compose up -d

# 或者先建立映像再啟動
docker-compose build
docker-compose up -d
```

### 3. 訪問網站
- WordPress 網站: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- MySQL: localhost:3307

## 服務說明

### WordPress (PHP 7.3 + Apache)
- PHP 7.3 配合 Apache 2.4
- 已安裝所有 WordPress 必要的 PHP 擴展
- 支援 .htaccess 重寫規則
- 記憶體限制: 512MB
- 上傳限制: 200MB
- 執行時間: 600秒 (10分鐘)

### MySQL 5.7
- 資料庫名稱: atomdpc3_superdouble_wp
- 使用者: atomdpc3_hcd1983
- 字元集: utf8mb4
- 端口: 3307 (避免與本地 MySQL 衝突)
- 最大封包大小: 256MB (支援大型 SQL 檔案匯入)
- InnoDB 緩衝池: 512MB

### phpMyAdmin
- 用於管理資料庫的 Web 介面
- 端口: 8081
- 上傳限制: 200MB (支援大型 SQL 檔案匯入)

## 常用命令

### 查看日誌
```bash
# 查看所有服務日誌
docker-compose logs -f

# 查看特定服務日誌
docker-compose logs -f wordpress
docker-compose logs -f db
```

### 進入容器
```bash
# 進入 WordPress 容器
docker-compose exec wordpress bash

# 進入 MySQL 容器
docker-compose exec db mysql -u root -p
```

### 停止和重啟
```bash
# 停止所有服務
docker-compose stop

# 重啟所有服務
docker-compose restart

# 完全移除（包括資料）
docker-compose down -v
```

## 資料庫操作

### 匯入現有資料庫
```bash
# 將 SQL 檔案放在 docker/mysql-init/ 目錄
# 檔案會在首次建立容器時自動執行

# 或者手動匯入
docker-compose exec -T db mysql -u root -p atomdpc3_superdouble_wp < backup.sql
```

### 備份資料庫
```bash
docker-compose exec db mysqldump -u root -p atomdpc3_superdouble_wp > backup-$(date +%Y%m%d).sql
```

## 權限設定

如果遇到檔案權限問題：
```bash
# 在容器內執行
docker-compose exec wordpress bash
chown -R www-data:www-data /var/www/html/wp-content/uploads
chmod -R 755 /var/www/html/wp-content/uploads
```

## 疑難排解

### 1. 無法連接資料庫
- 確認 .env 檔案中的資料庫密碼設定正確
- 確認 wp-config.php 中的資料庫主機設為 'db'

### 2. 上傳檔案大小限制
- 已在 docker/uploads.ini 中設定為 200MB
- 支援匯入最大 200MB 的 SQL 檔案
- 可根據需要調整

### 3. WordPress 白畫面
- 檢查錯誤日誌: `docker-compose logs wordpress`
- 確認檔案權限正確
- 清除瀏覽器快取

### 4. 時區問題
- 預設設定為 Asia/Taipei
- 可在 docker/uploads.ini 中調整

## 生產環境注意事項

部署到生產環境前，請：
1. 修改 .env 中的所有密碼
2. 設定 WP_DEBUG 為 false
3. 調整 docker/uploads.ini 中的 display_errors 為 Off
4. 考慮使用 SSL 憑證
5. 設定適當的防火牆規則