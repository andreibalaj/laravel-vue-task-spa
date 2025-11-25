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

# Copy custom Nginx config template
COPY nginx-spa.conf /etc/nginx/templates/default.conf.template

# No EXPOSE needed - Cloud Run uses the PORT env variable
# The nginx:alpine base image already includes a default CMD