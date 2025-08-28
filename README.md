# SuperDouble Shop - WordPress E-Commerce Site

這是一個使用 WordPress 建構的電子商務網站，包含自訂購物車系統（MyProducts 外掛）。

## 🚀 Zeabur 部署指南

### 步驟 1: 準備 GitHub 儲存庫

1. 在 GitHub 創建新的儲存庫
2. 將此專案推送到 GitHub：

```bash
git init
git add .
git commit -m "Initial commit for Zeabur deployment"
git branch -M main
git remote add origin git@github.com:hcd1983/shop.superdouble.org-zeabur.git
git push -u origin main
```

### 步驟 2: 在 Zeabur 部署

1. 登入 [Zeabur](https://zeabur.com)
2. 創建新專案
3. 選擇「從 GitHub 導入」
4. 選擇你的儲存庫
5. Zeabur 會自動偵測 PHP 應用程式

### 步驟 3: 設定 MySQL 資料庫

1. 在 Zeabur 專案中，點擊「Add Service」
2. 選擇「Prebuilt」→「MySQL」
3. 記下資料庫連線資訊：
   - 主機名稱 (DB_HOST) - 通常是 `mysql.zeabur.internal`
   - 資料庫名稱 (DB_NAME) - 預設是 `zeabur`
   - 使用者名稱 (DB_USER) - 預設是 `root`
   - 密碼 (DB_PASSWORD) - 在 MySQL 服務的 Variables 中查看

### 步驟 3.1: 安裝 phpMyAdmin（選用）

1. 在 Zeabur 專案中，點擊「Add Service」
2. 選擇「Prebuilt」→「phpMyAdmin」
3. Zeabur 會自動連接到你的 MySQL 服務
4. 部署完成後，點擊 phpMyAdmin 服務的域名即可訪問

或者手動配置：
1. 點擊「Add Service」→「Git」
2. 選擇 phpMyAdmin 儲存庫：`https://github.com/phpmyadmin/phpmyadmin`
3. 添加環境變數：
   - `PMA_HOST`: mysql.zeabur.internal
   - `PMA_PORT`: 3306
   - `UPLOAD_LIMIT`: 256M

### 步驟 4: 配置環境變數

在 Zeabur 專案設定中，添加以下環境變數：

```env
# 資料庫設定
DB_HOST=你的MySQL主機
DB_NAME=你的資料庫名稱
DB_USER=你的資料庫使用者
DB_PASSWORD=你的資料庫密碼
DB_PORT=3306

# WordPress 設定
WP_TABLE_PREFIX=wp_
WP_DEBUG=false
WP_CACHE=true
WP_HOME=https://你的專案名稱.zeabur.app
WP_SITEURL=https://你的專案名稱.zeabur.app

# 安全金鑰（請生成新的）
AUTH_KEY=生成的金鑰
SECURE_AUTH_KEY=生成的金鑰
LOGGED_IN_KEY=生成的金鑰
NONCE_KEY=生成的金鑰
AUTH_SALT=生成的金鑰
SECURE_AUTH_SALT=生成的金鑰
LOGGED_IN_SALT=生成的金鑰
NONCE_SALT=生成的金鑰
```

生成安全金鑰：訪問 https://api.wordpress.org/secret-key/1.1/salt/

### 步驟 5: 匯入資料庫

#### 方法 A: 使用 phpMyAdmin (如果 Zeabur 提供)
1. 訪問 Zeabur MySQL 管理介面
2. 匯入 `backup_*.sql` 檔案

#### 方法 B: 使用命令列
```bash
mysql -h 你的主機 -u 你的使用者 -p 你的資料庫 < backup_20250828_082040.sql
```

### 步驟 6: 部署後設定

1. 在部署前，將 `wp-config-zeabur.php` 重命名為 `wp-config.php`：
```bash
mv wp-config-zeabur.php wp-config.php
git add .
git commit -m "Use Zeabur config"
git push
```

2. 訪問你的網站：`https://你的專案名稱.zeabur.app`

## 📁 專案結構

```
shop.superdouble.org/
├── wp-admin/           # WordPress 管理後台
├── wp-content/         # 內容目錄
│   ├── plugins/        # 外掛目錄
│   │   └── MyProducts/ # 自訂購物車系統
│   ├── themes/         # 主題目錄
│   │   └── enfold/     # 主要主題
│   └── uploads/        # 媒體上傳目錄
├── wp-includes/        # WordPress 核心檔案
├── wp-config.php       # WordPress 配置（本地開發）
├── wp-config-zeabur.php # Zeabur 部署配置
├── zeabur.yaml         # Zeabur 部署設定
├── .env.example        # 環境變數範例
└── .gitignore          # Git 忽略檔案
```

## 🛠️ 本地開發

### 使用 Docker Compose

1. 複製環境設定：
```bash
cp .env.example .env
```

2. 啟動容器：
```bash
docker-compose up -d
```

3. 訪問：
- WordPress: http://localhost:8080
- phpMyAdmin: http://localhost:8081

4. 停止容器（保留資料）：
```bash
docker-compose down
```

## 📝 重要注意事項

1. **安全性**：
   - 務必更改所有預設密碼
   - 生成新的安全金鑰
   - 不要將 `wp-config.php` 和 `.env` 提交到 Git

2. **檔案權限**：
   - Zeabur 會自動處理檔案權限
   - 確保 `wp-content/uploads` 可寫入

3. **外掛相容性**：
   - MyProducts 外掛已針對 PHP 7.3 優化
   - 確保所有外掛都支援 PHP 7.3

4. **資料庫**：
   - 定期備份資料庫
   - 使用 UTF8MB4 編碼支援完整的 Unicode

## 📞 支援

如有問題，請查看：
- [WordPress 官方文檔](https://wordpress.org/documentation/)
- [Zeabur 文檔](https://docs.zeabur.com/)

## 📄 授權

此專案包含商業主題 Enfold 和自訂開發的 MyProducts 外掛。