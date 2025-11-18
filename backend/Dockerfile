FROM php:8.3-fpm-alpine

# Install PHP and build dependencies
RUN apk add --no-cache \
    php83 php83-common php83-fpm php83-pdo php83-pdo_mysql \
    php83-mbstring php83-xml php83-json php83-openssl \
    php83-tokenizer php83-gd \
    build-base autoconf  # ← Needed for PECL compilation

# Install gRPC via PECL (should be faster on Alpine)
RUN pecl install grpc && docker-php-ext-enable grpc

# Remove build dependencies to keep image small
RUN apk del build-base autoconf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --optimize-autoloader --no-dev
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm83"]