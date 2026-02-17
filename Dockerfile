## Stage 1: build assets with Node (Vite)
FROM node:18-alpine AS node_builder
WORKDIR /app

# Copy only package files first to leverage layer cache
COPY package*.json ./

# Install Node dependencies (include dev dependencies required for Vite build)
# Use `npm ci` when a lockfile is present, fallback to `npm install` otherwise
RUN npm ci || npm install

# Copy vite config and resources then build
COPY vite.config.js .
COPY resources ./resources

RUN npm run build


## Stage 2: PHP runtime
FROM php:8.2-fpm

# Install system packages required for PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libpq-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_pgsql gd zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy built assets from node stage
COPY --from=node_builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the default port Render will provide (fallback to 10000)
EXPOSE 10000

# Start the built-in PHP server so an HTTP port is opened for Render.
# Use the PORT env var provided by Render or default to 10000.
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]