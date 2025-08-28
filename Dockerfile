FROM php:7.3-apache

# 安裝必要的系統套件
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# 設定 GD 擴展
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

# 安裝 PHP 擴展
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    bcmath \
    exif \
    intl \
    mbstring \
    xml \
    opcache

# 啟用 Apache mod_rewrite
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod expires

# 設定 PHP 配置 (支援 200MB SQL 檔案)
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 200M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 210M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_input_time = 600" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_input_vars = 5000" >> /usr/local/etc/php/conf.d/custom.ini

# 設定 Apache 虛擬主機
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# 設定工作目錄
WORKDIR /var/www/html

# 複製整個專案到容器（包括 wp-config.php）
COPY . /var/www/html/

# 設定權限
RUN chown -R www-data:www-data /var/www/html

# 建立一個啟動腳本來處理 wp-config.php
RUN echo '#!/bin/bash\n\
# 如果環境變數存在但 wp-config.php 不存在，則使用模板建立\n\
if [ ! -f /var/www/html/wp-config.php ] && [ -f /var/www/html/wp-config-zeabur.php ]; then\n\
    cp /var/www/html/wp-config-zeabur.php /var/www/html/wp-config.php\n\
fi\n\
# 啟動 Apache\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# 暴露端口
EXPOSE 80

# 使用自訂的啟動腳本
CMD ["/usr/local/bin/docker-entrypoint.sh"]