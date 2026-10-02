#!/bin/bash
set -e

# Port configuration for Render ($PORT is provided dynamically by Render, default 80)
PORT_TO_USE="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT_TO_USE}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT_TO_USE}>/g" /etc/apache2/sites-available/000-default.conf

# Ensure database and storage directories exist
mkdir -p /var/www/html/database \
         /var/www/html/storage/logs \
         /var/www/html/storage/documents \
         /var/www/html/storage/uploads \
         /var/www/html/storage/generated_docs

# Ensure www-data can create SQLite journal/lock files and store uploads
chown -R www-data:www-data /var/www/html/storage /var/www/html/database
chmod -R 777 /var/www/html/storage /var/www/html/database

exec "$@"
