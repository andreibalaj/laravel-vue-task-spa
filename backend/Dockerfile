# --- STAGE 1: GRPC BUILDER (Uses Debian base for reliable compilation) ---
FROM php:8.3-fpm AS grpc_builder

# Install necessary build tools and dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        build-essential \
        autoconf \
    && rm -rf /var/lib/apt/lists/*

# Compile and install gRPC via PECL
# This step is slow but reliable on this base, and CRITICALLY, it's cached.
RUN pecl install grpc \
    && docker-php-ext-enable grpc

# --- STAGE 2: FINAL ALPINE RUNTIME IMAGE (Slim, fast-to-deploy) ---
FROM php:8.3-fpm-alpine

# Install core PHP extensions needed for Laravel runtime
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
    libstdc++

# 🚨 COPY the pre-compiled grpc.so from the builder stage 🚨
COPY --from=grpc_builder /usr/local/lib/php/extensions/no-debug-non-zts-*/grpc.so \
    /usr/local/lib/php/extensions/no-debug-non-zts-20230831/grpc.so
    
# Enable the extension
RUN echo "extension=grpc.so" > /usr/local/etc/php/conf.d/40-grpc.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

# Copy application files (This must happen after the gRPC install for caching)
COPY . .

# Run Composer installation
# This layer will be updated on every code change, but the gRPC layer won't.
RUN composer install --optimize-autoloader --no-dev

# Set permissions for Laravel directories
RUN chmod -R 775 storage bootstrap/cache

# Start PHP-FPM
CMD ["php-fpm83"]