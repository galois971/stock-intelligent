#!/bin/bash
set -e

echo "🔧 Composer install..."
composer install --no-dev --optimize-autoloader

echo "🔑 Generate APP_KEY if missing..."
php artisan key:generate || true

echo "🗑️  Clear cache..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "📦 Cache config..."
php artisan config:cache

echo "📚 Cache routes..."
php artisan route:cache

echo "✨ Cache views..."
php artisan view:cache

echo "✅ Build complete!"

echo "✅ Build complété!"
