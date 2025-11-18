FROM php:8.3-fpm-alpine

# Install pre-compiled gRPC (seconds, not minutes)
RUN apk add --no-cache \
    php83 \
    php83-common \
    php83-fpm \
    php83-pdo \
    php83-pdo_mysql \
    php83-mbstring \
    php83-xml \
    php83-json \
    php83-openssl \
    php83-tokenizer \
    php83-gd \
    php83-grpc

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --optimize-autoloader --no-dev
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm83"]