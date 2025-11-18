# --- STAGE 1: GRPC BUILDER (Uses pre-compiled libraries for reliability) ---
FROM php:8.3-fpm-alpine AS grpc_builder

# Install build dependencies ($PHPIZE_DEPS includes build-base, autoconf, etc.)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    # Install the pre-compiled C++ gRPC library from Alpine's 'testing' repository
    && apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing grpc-dev \
    # Install the PHP extension wrapper using PECL
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    # Clean up the build dependencies
    && apk del .build-deps

# --- STAGE 2: FINAL ALPINE RUNTIME IMAGE (Slim and fast-to-deploy) ---
FROM php:8.3-fpm-alpine

# Install core PHP dependencies needed for Laravel runtime
RUN apk add --no-cache \
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
    # Required runtime dependency for gRPC core libraries
    libstdc++ \
    grpc

# COPY the pre-compiled grpc.so from the builder stage
# The path below is the standard location for the compiled .so file
COPY --from=grpc_builder /usr/local/lib/php/extensions/no-debug-non-zts-*/grpc.so \
    /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so
    
# Enable the extension
RUN echo "extension=grpc.so" > /usr/local/etc/php/conf.d/40-grpc.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# Copy application files (must be after grpc install for caching)
COPY . .

# Run Composer installation
RUN composer install --optimize-autoloader --no-dev
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm83"]