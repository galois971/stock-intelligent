# 🚀 DEPLOYMENT GUIDE - Stock Intelligent on Render

## ✅ Pre-Deployment Checklist

### 1. **Local Testing**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate:fresh --seed

# Test the app locally
php artisan serve
```

### 2. **GitHub Push**
```bash
git add .
git commit -m "Fix: Correct deployment configuration and LoginRequest"
git push origin main
```

### 3. **Render Environment Variables**

Set these in Render dashboard > Environment:
```
APP_KEY=base64:/TQTiwWvzE52KRhuanIB5Spp8UspRi9YMpF9xuIs+mE=
APP_NAME=StockIntelligent
APP_ENV=production
APP_DEBUG=false
APP_URL=https://stock-intelligent.onrender.com
DB_CONNECTION=pgsql
DB_HOST=pg-32c1c2dc-galoisturner-c693.g.aivencloud.com
DB_PORT=13640
DB_DATABASE=stock_db
DB_USERNAME=avnadmin
DB_PASSWORD=
DB_SSLMODE=require
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_CHANNEL=stderr
LOG_LEVEL=debug
MAIL_MAILER=log
```

### 4. **Render Configuration**
The `render.yaml` file handles:
- ✅ Build: `chmod +x build.sh && ./build.sh`
- ✅ Start: `php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=$PORT`

## 🔧 What Gets Done Automatically

### During Build (build.sh)
1. ✅ Composer install
2. ✅ NPM install (for Vite)
3. ✅ **Vite build** (CSS + JavaScript compilation) - **CRITICAL**
4. ✅ APP_KEY generation
5. ✅ Cache clearing
6. ✅ Config caching
7. ✅ Route caching  
8. ✅ View caching

### During Start
1. ✅ Database migrations
2. ✅ Database seeding (creates admin, gestionnaire, observateur users)
3. ✅ Laravel server start

## 🔓 Default Credentials (After Seed)

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password | admin |
| gestionnaire@example.com | password | gestionnaire |
| observateur@example.com | password | observateur |

**⚠️ Change these immediately in production!**

## ✨ After Deployment

### 1. Test Login
- Go to: https://stock-intelligent.onrender.com/login
- Login with: admin@example.com / password
- Should redirect to /dashboard

### 2. Check Logs
```bash
render logs --service=stock-intelligent
```

### 3. Database Check
```bash
# SSH into container
render shell

# Check migrations
php artisan migrate:status

# Check seeded data
php artisan tinker
>>> User::all()
>>> Role::all()
```

## 🐛 Troubleshooting

### Issue: 500 Error on /login
**Cause**: LoginRequest not found or migrations not run
**Fix**: Ensure render.yaml has both:
- `php artisan migrate --force`
- `php artisan db:seed --force`

### Issue: "Vite Manifest Not Found" Error
**Cause**: Assets not compiled (npm run build didn't execute)
**Fix**: Ensure build.sh has:
- `npm ci --omit=dev` (install dependencies)
- `npm run build` (compile CSS/JS)
- Check that output: `/public/build/manifest.json` exists

### Issue: "Class not found" errors
**Cause**: Cache files are stale
**Fix**: build.sh clears all caches before rebuilding

### Issue: Database connection fails
**Cause**: PostgreSQL SSL or credentials wrong
**Check**:
1. DB_SSLMODE=require is set
2. DB credentials are correct
3. Aiven PostgreSQL is running

### Issue: Roles/Permissions not working
**Cause**: Migrations not executed
**Fix**: Check `php artisan migrate:status` in shell

## 📊 Files Modified for Deployment

1. **build.sh** - Added cache clearing + optimization
2. **render.yaml** - Added db:seed command
3. **app/Http/Requests/Auth/LoginRequest.php** - Created (was missing)
4. **post-deploy.sh** - Post-deployment verification script
5. **deploy-check.sh** - Local diagnostic before pushing

## 🎯 Success Indicators

✅ Build completes without errors
✅ /login displays login form (no 500)
✅ Can login with admin@example.com
✅ Dashboard displays correctly
✅ Roles and permissions working

---

**Last Updated**: February 17, 2026
**Status**: Ready for Deployment
