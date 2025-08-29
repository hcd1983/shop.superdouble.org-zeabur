#!/bin/bash
# 準備 uploads 目錄的腳本
# 在 Zeabur 部署時執行，確保持久化儲存正確掛載

echo "準備 uploads 目錄..."

# 建立 uploads 目錄（如果不存在）
if [ ! -d "wp-content/uploads" ]; then
    echo "建立 uploads 目錄..."
    mkdir -p wp-content/uploads
fi

# 設置正確的權限
echo "設置目錄權限..."
chmod -R 755 wp-content
chmod -R 777 wp-content/uploads

# 建立 .htaccess 檔案以允許上傳
echo "建立 uploads/.htaccess..."
cat > wp-content/uploads/.htaccess << 'EOF'
# Zeabur uploads directory .htaccess
# Allow uploads
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule !^index\.php$ - [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options nosniff
</IfModule>

# Prevent PHP execution
<FilesMatch "\.(?i:php|phtml|php3|php4|php5|inc)$">
    <IfModule !mod_authz_core.c>
        Order Deny,Allow
        Deny from all
    </IfModule>
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>
EOF

echo "uploads 目錄準備完成！"

# 列出 uploads 目錄狀態
echo "檢查 uploads 目錄："
ls -la wp-content/uploads/ 2>/dev/null || echo "uploads 目錄是空的"