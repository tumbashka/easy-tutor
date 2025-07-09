FROM php:8.3-fpm

# Установка системных библиотек
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev \
    libonig-dev libxml2-dev libxslt1-dev libicu-dev curl \
    default-mysql-client \
    git \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo_mysql mbstring zip gd exif intl xsl


# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Xdebug (опционально)
RUN pecl install xdebug && docker-php-ext-enable xdebug \
 && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Настройка PHP
RUN echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/uploads.ini \
 && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www
