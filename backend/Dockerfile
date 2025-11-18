# --- STAGE 1: GRPC BUILDER (Using Alpine, Limiting JIT parallelism) ---
FROM php:8.3-fpm-alpine AS grpc_builder

# Install build dependencies and the pre-compiled C++ gRPC library
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/testing grpc-dev

# Manual compilation with -j1 (single thread)
# This drastically reduces memory usage, preventing OOM kills.
RUN pecl download grpc \
    && tar -xzf grpc-*.tgz \
    && cd grpc-* \
    && phpize \
    && ./configure --with-grpc \
    && make -j1 \
    && make install \
    && cd .. \
    && rm -rf grpc-* \
    && docker-php-ext-enable grpc \
    # Clean up the build dependencies
    && apk del .build-deps

# --- STAGE 2: FINAL ALPINE RUNTIME IMAGE ---
FROM php:8.3-fpm-alpine

# Install core PHP dependencies needed for Laravel runtime
RUN apk add --no-cache \
    php83-common php83-fpm php83-pdo php83-pdo_mysql \
    php83-mbstring php83-xml php83-json php83-openssl \
    php83-tokenizer php83-gd \
    libstdc++ grpc

# COPY the pre-compiled grpc.so from the builder stage
COPY --from=grpc_builder /usr/local/lib/php/extensions/no-debug-non-zts-*/grpc.so \
    /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so
    
# Enable the extension
RUN echo "extension=grpc.so" > /usr/local/etc/php/conf.d/40-grpc.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# Copy application files 
COPY . .

# Run Composer installation
RUN composer install --optimize-autoloader --no-dev
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm83"]