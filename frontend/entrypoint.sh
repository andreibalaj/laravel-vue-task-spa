#!/bin/sh
set -e

PORT=${PORT:-8080}
echo "=== Starting frontend container on port: $PORT ==="

# Update Nginx configuration to listen on the required $PORT
echo "Updating nginx config to listen on port $PORT"
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/nginx.conf

# Test nginx config
echo "Testing nginx configuration..."
nginx -t

# Start nginx
echo "Starting nginx on port $PORT..."
exec nginx -g "daemon off;"