#!/bin/bash

# Cloud Run injects the listening port via $PORT (defaults to 8080)
PORT=${PORT:-8080}

echo "=== Starting frontend container on port: $PORT ==="

# Update Nginx configuration to listen on the required $PORT
echo "Updating nginx config to listen on port $PORT"
sed -i "s/listen 8080;/listen ${PORT};/g" /etc/nginx/nginx.conf

# Test nginx config
echo "Testing nginx configuration..."
nginx -t

# Start nginx and keep container alive
echo "Starting nginx on port $PORT..."
nginx -g "daemon off;"