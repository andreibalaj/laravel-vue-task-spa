#!/bin/bash

# Cloud Run injects the listening port via $PORT (defaults to 8080)
PORT=${PORT:-8080}

# 1. Update Nginx configuration to listen on the required $PORT
# We use sed to replace a placeholder port (e.g., 8080) with the actual $PORT value
sed -i "s/listen 8080;/listen ${PORT};/" /etc/nginx/conf.d/default.conf

# 2. Start PHP-FPM in the background
php-fpm83 -D

# 3. Start Nginx in the foreground (exec replaces the shell process)
# This ensures the container stays alive and forwards logs correctly.
exec nginx -g "daemon off;"