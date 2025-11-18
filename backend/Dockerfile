# Use official PHP 8.3 Apache (Debian Bookworm)
FROM php:8.3-apache

# Install system deps + pre-built gRPC from Debian
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    php-grpc \
    php-protobuf \
    && docker-php-ext-install mbstring zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --optimize-autoloader --no-dev

RUN php artisan key:generate

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]