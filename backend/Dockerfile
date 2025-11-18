# Use official PHP 8.3 Apache image WITH gRPC pre-installed
FROM gcr.io/google-appengine/php83:2025-04-01-15-00

# Install only what's needed
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy code
COPY . .

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install deps
RUN composer install --optimize-autoloader --no-dev

# Generate app key (overridden in Cloud Run)
RUN php artisan key:generate

# Enable mod_rewrite
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]