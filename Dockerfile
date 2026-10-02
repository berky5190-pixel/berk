FROM php:8.2-apache

# Sistem paketlerini ve gerekli PHP eklentilerini kur
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_sqlite zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Apache DocumentRoot ayarını public klasörüne yönlendir
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# .htaccess kurallarının çalışması için AllowOverride All
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Çalışma dizini
WORKDIR /var/www/html

# Proje dosyalarını kopyala
COPY . /var/www/html/

# Entrypoint scriptini ayarla ve CRLF sorunlarını temizle
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Klasörleri oluştur ve izinleri ver
RUN mkdir -p /var/www/html/database \
    /var/www/html/storage/logs \
    /var/www/html/storage/documents \
    /var/www/html/storage/uploads \
    /var/www/html/storage/generated_docs \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/database

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
