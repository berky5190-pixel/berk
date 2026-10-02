#!/bin/bash
set -e

# Port configuration for Render ($PORT is provided dynamically by Render, default 80)
PORT_TO_USE="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf

# If SQLite database file does not exist, run migrations to generate tables & admin user
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "SQLite veritabanı bulunamadı, migration ve seeders çalıştırılıyor..."
    php /var/www/html/database/migrate.php sqlite || true
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Ensure storage directories and permissions
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/documents \
         /var/www/html/storage/uploads \
         /var/www/html/storage/generated_docs

chown -R www-data:www-data /var/www/html/storage /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/database

exec "$@"
