# استقرار

## دامنه پیشنهادی

- `panel.example.com`: Blade پنل‌ها
- `api.example.com`: Laravel API
- `book.example.com`: Next.js

## سرویس‌ها

- Nginx یا FrankenPHP
- PHP 8.3+
- MySQL 8+
- Redis
- Supervisor برای Queue
- Cron برای `schedule:run`
- Object storage برای لوگو و تصاویر

## متغیرهای مهم

```dotenv
APP_URL=https://api.example.com
FRONTEND_URL=https://book.example.com
SESSION_DOMAIN=.example.com
SANCTUM_STATEFUL_DOMAINS=book.example.com,panel.example.com
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
```

## دستورات انتشار

```bash
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan optimize
php artisan queue:restart
php artisan carwash:generate-slots --days=45
```
