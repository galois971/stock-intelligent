# Utiliser une image PHP avec Composer
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip nodejs npm \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code
WORKDIR /var/www/html
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer les dépendances Node et compiler les assets
RUN npm install && npm run build

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port
EXPOSE 9000

CMD ["php-fpm"]