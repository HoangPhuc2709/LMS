# --- Base PHP Image ---
    FROM php:8.1-fpm

    # --- Set working directory ---
    WORKDIR /var/www/html
    
    # --- Install system dependencies ---
    RUN apt-get update && apt-get install -y \
        git \
        curl \
        zip \
        unzip \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        && docker-php-ext-configure zip \
        && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
        && rm -rf /var/lib/apt/lists/*
    
    # --- Install Composer ---
    COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
    
    # --- Configure Composer for performance ---
    # Tăng timeout và bật cache global
    RUN composer config --global process-timeout 1800 \
        && composer config --global cache-dir /tmp/composer-cache
    
    # --- Set proper permissions ---
    # Dùng root để tránh lỗi khi mount volume từ Windows
    RUN chown -R www-data:www-data /var/www/html || true
    USER root
    
    # --- Optional: Enable opcache for Laravel performance ---
    RUN docker-php-ext-enable opcache
    
    # --- Expose port & start php-fpm ---
    EXPOSE 9000
    CMD ["php-fpm"]
    