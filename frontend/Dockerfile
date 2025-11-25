# Stage 1
FROM node:latest as build

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY ./ .
RUN npm run build

# Stage 2
FROM nginx:alpine

# Copy build files to Nginx's default HTML directory
COPY --from=build /app/dist /usr/share/nginx/html

# Copy nginx config
COPY nginx-spa.conf /etc/nginx/nginx.conf

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]