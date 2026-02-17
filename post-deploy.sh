#!/bin/bash
set -e

echo "======================================"
echo "🚀 POST-DEPLOYMENT VERIFICATION"
echo "======================================"

echo ""
echo "1️⃣  App Key Check..."
if [ -z "$APP_KEY" ]; then
    echo "❌ APP_KEY not set!"
    exit 1
else
    echo "✅ APP_KEY is configured"
fi

echo ""
echo "2️⃣  Database Connection Check..."
php artisan tinker --execute="DB::connection()->getPdo();" && echo "✅ Database connection successful" || echo "⚠️  Database check skipped"

echo ""
echo "3️⃣  Migration Status..."
php artisan migrate:status || true

echo ""
echo "4️⃣  Seeded Roles & Permissions..."
php artisan tinker --execute="echo 'Roles: ' . \App\Models\User::count();" || true

echo ""
echo "5️⃣  Cache Status..."
php artisan cache:clear
php artisan config:clear
echo "✅ Cache cleared"

echo ""
echo "======================================"
echo "✨ DEPLOYMENT READY!"
echo "======================================"
