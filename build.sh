#!/bin/bash
set -e

echo "🔧 Vérification Node/NPM..."
node -v
npm -v


echo "🔧 Composer install..."
composer install --no-dev --optimize-autoloader

echo "🔧 NPM install..."
npm install

echo "🎯 Vite build (CSS + JS)..."
npm run build

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

echo "📂 Run migrations and seed..."
php artisan migrate --force
php artisan db:seed --force

echo "✅ Build complete!"