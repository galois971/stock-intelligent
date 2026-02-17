#!/bin/bash
set -e

echo "🔧 Composer install..."
composer install --no-dev --optimize-autoloader

echo "🔑 Générer APP_KEY si absent..."
php artisan key:generate || true

echo "📦 Cache config..."
php artisan config:cache

echo "🗄️  Migrations..."
php artisan migrate --force

echo "📚 Compiler routes..."
php artisan route:cache

echo "✨ Compiler vues..."
php artisan view:cache

echo "✅ Build complété!"
