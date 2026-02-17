#!/bin/bash
# FINAL DEPLOYMENT CHECKLIST
# Run this before pushing to ensure everything is ready

echo "======================================"
echo "📋 FINAL DEPLOYMENT CHECKLIST"
echo "======================================"
echo ""

ERRORS=0

echo "[1] Checking LoginRequest.php exists..."
if [ -f "app/Http/Requests/Auth/LoginRequest.php" ]; then
    echo "✅ LoginRequest.php found"
else
    echo "❌ LoginRequest.php MISSING!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[2] Checking build.sh has cache:clear..."
if grep -q "cache:clear" build.sh; then
    echo "✅ build.sh has cache:clear"
else
    echo "❌ build.sh missing cache:clear!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[3] Checking render.yaml has db:seed..."
if grep -q "db:seed" render.yaml; then
    echo "✅ render.yaml has db:seed"
else
    echo "❌ render.yaml missing db:seed!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[4] Checking DatabaseSeeder exists..."
if [ -f "database/seeders/DatabaseSeeder.php" ]; then
    echo "✅ DatabaseSeeder.php found"
    if grep -q "assignRole" database/seeders/DatabaseSeeder.php; then
        echo "✅ DatabaseSeeder has assignRole"
    else
        echo "⚠️  DatabaseSeeder might be incomplete"
    fi
else
    echo "❌ DatabaseSeeder.php MISSING!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[5] Checking .env has production DB config..."
if grep -q "DB_CONNECTION=pgsql" .env; then
    echo "✅ .env has PostgreSQL config"
else
    echo "❌ .env missing or wrong DB config!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[6] Checking APP_KEY is set..."
if grep -q "APP_KEY=base64:" .env; then
    echo "✅ APP_KEY is set"
else
    echo "❌ APP_KEY not set!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[7] Checking composer.json has spatie/laravel-permission..."
if grep -q "spatie/laravel-permission" composer.json; then
    echo "✅ spatie/laravel-permission in composer.json"
else
    echo "❌ spatie/laravel-permission missing!"
    ERRORS=$((ERRORS + 1))
fi

echo ""
echo "[8] Checking migrations exist..."
MIGRATION_COUNT=$(ls database/migrations/ | wc -l)
if [ "$MIGRATION_COUNT" -ge 10 ]; then
    echo "✅ Found $MIGRATION_COUNT migrations (min 10)"
else
    echo "⚠️  Only $MIGRATION_COUNT migrations found"
fi

echo ""
echo "[9] Checking config/permission.php exists..."
if [ -f "config/permission.php" ]; then
    echo "✅ config/permission.php found"
else
    echo "⚠️  config/permission.php might not exist"
fi

echo ""
echo "======================================"
if [ $ERRORS -eq 0 ]; then
    echo "✅ ALL CHECKS PASSED - Ready to Deploy!"
    echo ""
    echo "Next steps:"
    echo "1. git add ."
    echo "2. git commit -m 'Fix: Deployment configuration and LoginRequest'"
    echo "3. git push origin main"
    echo ""
    exit 0
else
    echo "❌ $ERRORS CHECK(S) FAILED - DO NOT DEPLOY YET!"
    echo ""
    echo "Fix the issues above before pushing to GitHub"
    echo "======================================"
    exit 1
fi
