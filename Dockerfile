FROM php:7.3-apache

# 設定 ServerName 避免 Apache 警告
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

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
    default-mysql-client \
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

# 確保 wp-config.php 存在（從模板複製）
RUN cp /var/www/html/wp-config-zeabur.php /var/www/html/wp-config.php || true

# 確保目錄權限正確
RUN chmod -R 755 /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 777 /var/www/html/wp-content/uploads 2>/dev/null || true

# 暴露端口
EXPOSE 80

# 使用預設的 Apache 啟動命令
CMD ["apache2-foreground"]