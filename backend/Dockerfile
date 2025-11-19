FROM php:8.3-fpm-alpine

# Install core PHP dependencies needed for Laravel runtime
RUN apk update && apk add --no-cache \
    # Standard PHP extensions
    php83-pdo \
    php83-pdo_mysql \
    php83-mbstring \
    php83-xml \
    php83-json \
    php83-openssl \
    php83-tokenizer \
    php83-gd \
    # Install gRPC extension and core library as pre-compiled binaries
    php83-pecl-grpc \
    grpc \
    libstdc++ \
    # Clean up apk cache to keep image size small
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# Copy application files (must be after all heavy installs for caching)
COPY . .

# Run Composer installation
RUN composer install --optimize-autoloader --no-dev
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm83"]