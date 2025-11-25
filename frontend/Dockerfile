# Stage 1
FROM node:latest as build

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY ./ .
RUN npm run build

# Stage 2
FROM nginx 

# Make app dir
RUN mkdir /app

# Copy build files
COPY --from=build /app/dist /app

# Copy custom Nginx config for SPA
COPY nginx-spa.conf /etc/nginx/nginx.conf

# Expose port 80
EXPOSE 80

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]