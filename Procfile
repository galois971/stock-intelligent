web: php artisan serve --host=0.0.0.0 --port=10000
worker: php artisan queue:work --sleep=3 --tries=3
scheduler: php artisan schedule:run >> /dev/null 2>&1
