#!/bin/bash

# Путь к папке с бэкапами
BACKUP_DIR="/var/www/storage/app/backups"

echo "🛠  Фиксация прав доступа в $BACKUP_DIR"

# Меняем владельца на www-data (или другого, если Laravel работает от другого пользователя)
chown -R www-data:www-data "$BACKUP_DIR"

# Папки: 755 (rwxr-xr-x)
find "$BACKUP_DIR" -type d -exec chmod 755 {} \;

# Файлы: 644 (rw-r--r--)
find "$BACKUP_DIR" -type f -exec chmod 644 {} \;

echo "✅ Права успешно исправлены."
