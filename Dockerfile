## Stage 1: build assets with Node (Vite)
FROM node:18-alpine AS node_builder
WORKDIR /app

# Copy only package files first to leverage layer cache
COPY package.json package-lock.json* ./

# Install Node dependencies (production build)
RUN npm ci --omit=dev

# Copy resources and vite config then build
COPY vite.config.js .
COPY resources ./resources
COPY postcss.config.js package.json tailwind.config.js ./ 2>/dev/null || true

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

EXPOSE 9000

CMD ["php-fpm"]