# Use official PHP with Apache
FROM php:8.3-apache

# Install extensions
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set proper DocumentRoot
RUN sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's#<Directory /var/www/>#<Directory /var/www/html/public/>#g' /etc/apache2/apache2.conf
RUN sed -ri 's/AllowOverride\s+None/AllowOverride All/g' /etc/apache2/apache2.conf

# Tune Apache to respect low DB connection limits
RUN a2dismod mpm_event && a2enmod mpm_prefork \
 && printf "<IfModule mpm_prefork_module>\nStartServers 2\nMinSpareServers 2\nMaxSpareServers 2\nMaxRequestWorkers 2\nMaxConnectionsPerChild 1000\n</IfModule>\n" > /etc/apache2/conf-available/mpm-tune.conf \
 && printf "KeepAlive On\nMaxKeepAliveRequests 50\nKeepAliveTimeout 2\n" > /etc/apache2/conf-available/keepalive-tune.conf \
 && a2enconf mpm-tune keepalive-tune

# Copy application
COPY . /var/www/html

# Create required folders
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

# Set working directory
WORKDIR /var/www/html

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 1000

# Start Apache
CMD ["apache2-foreground"]
