# Use official PHP 8.3 Apache image
FROM php:8.3-apache

# Install system dependencies required for gRPC, Firestore, and Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libssl-dev \
    libsasl2-dev \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    && pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf \
    && docker-php-ext-install pdo_mysql mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Fix Laravel storage permissions
RUN chown -R www-www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies (optimized for production)
RUN composer install --optimize-autoloader --no-dev

# Generate Laravel app key (will be overridden by Cloud Run env var)
RUN php artisan key:generate

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]