# Use official PHP 8.3 Apache
FROM php:8.3-apache

# Install build dependencies for gRPC (critical for speed)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    autoconf \
    build-essential \
    libssl-dev \
    libc-ares-dev \
    libz-dev \
    && docker-php-ext-install mbstring zip

# Install gRPC and Protobuf via PECL (now faster with deps)
RUN pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Generate app key
RUN php artisan key:generate

# Enable Apache mod_rewrite
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]