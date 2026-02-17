#!/bin/bash
# Emergency diagnostic script for Render deployment issues
# Run this locally before deploying: php artisan tinker < deploy-check.php

echo "=== Environment Check ===" 
echo "APP_DEBUG: ${APP_DEBUG}"
echo "APP_ENV: ${APP_ENV}"
echo "LOG_LEVEL: ${LOG_LEVEL}"
echo ""

echo "=== Database Check ===" 
php -r "
try {
    \$pdo = new PDO(
        'pgsql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        ['sslmode' => 'require']
    );
    echo '✅ PostgreSQL connection OK' . PHP_EOL;
} catch (Exception \$e) {
    echo '❌ Database Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "=== Cache Driver Check ===" 
echo "CACHE_STORE: ${CACHE_STORE}"
echo "SESSION_DRIVER: ${SESSION_DRIVER}"
echo ""

echo "=== File Permissions ===" 
ls -la storage/logs/ | head -5
echo ""

echo "=== Vendor Check ===" 
test -d vendor/laravel && echo "✅ Laravel vendor OK" || echo "❌ Vendor missing!"
test -d vendor/spatie/laravel-permission && echo "✅ Spatie permission OK" || echo "❌ Spatie missing!"

echo ""
echo "=== Ready to Deploy ===" 
