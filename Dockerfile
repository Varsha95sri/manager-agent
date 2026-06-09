# Stage 1: Build the frontend (Vite React/CSS)
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Copy package files and install npm dependencies
COPY package*.json ./
RUN npm ci

# Copy Vite, Tailwind configs and resources, then build assets
COPY resources/ ./resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# Stage 2: Production PHP runtime and web server (Nginx + PHP-FPM)
FROM webdevops/php-nginx:8.3-alpine
WORKDIR /app

# Copy the entire application code first
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend-builder /app/public/build ./public/build

# Copy Supervisor configuration for background Laravel queue worker
COPY laravel-worker.conf /opt/docker/etc/supervisor.d/laravel-worker.conf

# Install PHP dependencies (now artisan and app files are present for auto-discovery)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set document root for Nginx to serve Laravel public folder
ENV WEB_DOCUMENT_ROOT=/app/public

# Set correct permissions for Laravel directories
RUN chown -R application:application storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache
