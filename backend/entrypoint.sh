#!/bin/bash

# Cloud Run injects the listening port via $PORT (defaults to 8080)
PORT=${PORT:-8080}

echo "=== Starting container on port: $PORT ==="

# 1. Update Nginx configuration to listen on the required $PORT
echo "Updating nginx config to listen on port $PORT"
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf

# 2. Fix permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Create PHP-FPM socket directory
mkdir -p /var/run/php
chown www-data:www-data /var/run/php

# 4. Test PHP-FPM config first
echo "Testing PHP-FPM configuration..."
php-fpm -t

# 5. Start PHP-FPM in background
echo "Starting PHP-FPM..."
php-fpm -D

# 6. Test nginx config
echo "Testing nginx configuration..."
nginx -t

# 7. Start nginx and keep container alive
echo "Starting nginx on port $PORT..."
nginx -g "daemon off;" &
NGINX_PID=$!

# 8. Wait a moment and check if processes are running
sleep 3
echo "Process status:"
ps aux | grep -E "(nginx|php-fpm)"

# 9. Check if port is listening
echo "Network status:"
netstat -tulpn | grep :$PORT || echo "PORT $PORT not listening!"

# 10. Keep container running
wait $NGINX_PID