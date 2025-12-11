# Use official PHP with Apache
FROM php:8.3-apache

# Install extensions
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set proper DocumentRoot
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's#<Directory /var/www/>#<Directory /var/www/html/public/>#g' /etc/apache2/apache2.conf
RUN sed -ri 's/AllowOverride\s+None/AllowOverride All/g' /etc/apache2/apache2.conf

# Apache tuning (Render-safe)
RUN a2dismod mpm_event && a2enmod mpm_prefork && \
    printf "<IfModule mpm_prefork_module>\nStartServers 10\nMinSpareServers 10\nMaxSpareServers 20\nMaxRequestWorkers 150\nMaxConnectionsPerChild 3000\n</IfModule>\n" \
    > /etc/apache2/conf-available/mpm-tune.conf && \
    printf "KeepAlive On\nKeepAliveTimeout 5\nMaxKeepAliveRequests 100\n" \
    > /etc/apache2/conf-available/keepalive-tune.conf && \
    a2enconf mpm-tune keepalive-tune && \
    echo "LogLevel warn" >> /etc/apache2/apache2.conf

# OPcache (Render-safe)
RUN printf "opcache.enable=1\nopcache.memory_consumption=128\nopcache.max_accelerated_files=10000\n" \
    > /usr/local/etc/php/conf.d/opcache.ini

# Copy application
COPY . /var/www/html

# Create required folders with permissions
RUN mkdir -p /var/www/html/public/uploads \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && touch /var/www/html/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache

# Working directory
WORKDIR /var/www/html

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Clear caches
RUN php artisan config:clear \
 && php artisan cache:clear \
 && php artisan route:clear \
 && php artisan view:clear

# Render uses dynamic ports; EXPOSE is optional
EXPOSE 1000

# Start Apache
CMD ["apache2-foreground"]
