# 修復 Zeabur 硬碟掛載問題

## 問題診斷

你的 Zeabur 硬碟已經掛載到 `/var/www/html/wp-content/uploads`，但重新部署時資料仍會消失。這表示：

1. **路徑不匹配**：你的應用實際運行路徑可能不是 `/var/www/html`
2. **掛載點被覆蓋**：部署過程中可能覆蓋了掛載點

## 解決方案

### 方案 1：調整掛載路徑（推薦）

在 Zeabur 控制台中，將硬碟掛載路徑改為：

```
/wp-content/uploads
```

而不是：

```
/var/www/html/wp-content/uploads
```

因為你的 `zeabur.yaml` 設定 `document_root: /`，表示應用根目錄就是 `/`。

### 方案 2：使用絕對路徑掛載

如果方案 1 不行，嘗試掛載到：

```
/app/wp-content/uploads
```

或

```
/workspace/wp-content/uploads
```

### 實施步驟

1. **在 Zeabur 控制台**：
   - 進入你的服務設定
   - 找到「硬碟」或「Volume」設定
   - 刪除現有的掛載
   - 重新建立掛載，使用新路徑：`/wp-content/uploads`

2. **更新 zeabur.yaml**（已完成）：
   ```yaml
   build:
     commands:
       # 確保 uploads 目錄存在
       - mkdir -p wp-content/uploads
       # 設置正確的權限
       - chmod -R 755 wp-content
       - chmod -R 777 wp-content/uploads
       - chmod 644 .htaccess
       # 複製 wp-config 檔案
       - if [ -f "wp-config-zeabur.php" ]; then cp wp-config-zeabur.php wp-config.php; fi
   ```

3. **執行準備腳本**（已建立）：
   - `prepare-uploads.sh` 會在部署時自動設置正確的目錄結構和權限

### 驗證掛載是否成功

部署後，可以通過以下方式驗證：

1. **上傳測試檔案**：
   - 登入 WordPress 管理後台
   - 上傳一張圖片
   - 記下圖片 URL

2. **重新部署**：
   - 在 Zeabur 觸發重新部署
   - 等待部署完成

3. **檢查檔案是否還在**：
   - 訪問之前上傳的圖片 URL
   - 如果圖片還能訪問，表示持久化儲存生效

### 備用方案：使用外部儲存

如果 Zeabur 的持久化儲存仍有問題，考慮使用：

1. **外部物件儲存**（如 AWS S3、Cloudinary）
2. **CDN 服務**
3. **其他雲端儲存服務**

可以安裝 WordPress 外掛如：
- WP Offload Media
- Media Cloud
- Cloudinary

### 重要提醒

1. **備份現有資料**：在調整掛載設定前，先備份 uploads 目錄中的所有檔案
2. **測試環境**：先在測試環境驗證設定
3. **監控日誌**：查看 Zeabur 的部署日誌，確認沒有錯誤

## 聯繫支援

如果問題持續，建議：

1. 查看 Zeabur 的官方文檔關於持久化儲存的說明
2. 聯繫 Zeabur 技術支援，提供：
   - 你的服務 ID
   - 硬碟掛載配置截圖
   - 部署日誌

## 檔案清單

- `zeabur.yaml` - 已更新，包含正確的建構命令
- `prepare-uploads.sh` - 準備 uploads 目錄的腳本
- `wp-config-zeabur.php` - Zeabur 專用的 WordPress 配置