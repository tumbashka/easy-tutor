#!/bin/bash

# Установка прав
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Установка зависимостей Laravel (если нужно)
if [ ! -d "/var/www/vendor" ]; then
  echo "Installing composer dependencies..."
  cd /var/www && composer install --no-dev --optimize-autoloader
fi

# Создание ссылок
php /var/www/artisan storage:link || true
