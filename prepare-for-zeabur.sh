#!/bin/bash

echo "🚀 準備 Zeabur 部署..."

# 1. 備份當前的 wp-config.php
if [ -f "wp-config.php" ]; then
    echo "📦 備份當前的 wp-config.php..."
    cp wp-config.php wp-config-local.php
fi

# 2. 使用 Zeabur 版本的 wp-config
echo "🔧 切換到 Zeabur 配置..."
cp wp-config-zeabur.php wp-config.php

# 3. 創建必要的目錄
echo "📁 創建必要的目錄..."
mkdir -p wp-content/uploads
mkdir -p wp-content/cache

# 4. 設定檔案權限
echo "🔒 設定檔案權限..."
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# 5. 清理不需要的檔案
echo "🧹 清理不需要的檔案..."
rm -rf node_modules
rm -rf .git
find . -name "*.log" -delete
find . -name ".DS_Store" -delete

# 6. 初始化 Git（如果需要）
echo "📝 初始化 Git..."
git init
git add .
git commit -m "Prepare for Zeabur deployment"

echo "✅ 準備完成！"
echo ""
echo "下一步："
echo "1. 創建 GitHub 儲存庫"
echo "2. 執行: git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git"
echo "3. 執行: git push -u origin main"
echo "4. 在 Zeabur 導入專案"
echo "5. 設定環境變數"
echo "6. 匯入資料庫"