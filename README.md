# سامانه مدیریت و رزرو کارواش

Backend اصلی پروژه با Laravel 12، پنل‌های Blade، API مخصوص Next.js، احراز هویت Sanctum و مدیریت نقش‌های هر کارواش با Spatie Permission Teams.

## معماری

```text
Laravel
├── /admin                         پنل مدیر کل
├── /wash-panel/{carWash:slug}     پنل هر کارواش
└── /api/v1                       API لندینگ Next.js
```

تمام افراد در جدول `users` قرار دارند:

- مدیر کل: `users.is_super_admin = true`
- مالک و کارکنان کارواش: عضویت در `car_wash_user` و Role تیمی Spatie با `car_wash_id`
- مشتری: User عادی با ورود OTP

## پیش‌نیازها

- PHP 8.2 یا جدیدتر
- MySQL 8+
- Composer
- Node.js 20+
- افزونه‌های معمول Laravel مانند `mbstring`, `pdo_mysql`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`

## نصب

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```

اطلاعات دیتابیس و مدیر کل را در `.env` تنظیم کنید، سپس:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

اطلاعات ورود مدیر کل از این متغیرها خوانده می‌شود:

```dotenv
SUPER_ADMIN_MOBILE=
SUPER_ADMIN_PASSWORD=
```

## دستورات توسعه

```bash
composer test
php artisan route:list
php artisan carwash:generate-slots --days=45
php artisan schedule:list
npm run dev
```

## نقش‌های کارواش

- `carwash-owner`
- `carwash-manager`
- `carwash-receptionist`
- `carwash-operator`
- `carwash-accountant`

مدیر کل Role اسپاتی ندارد؛ دسترسی کامل او با ستون `is_super_admin` و `Gate::before` اعمال می‌شود. این تصمیم از مشکل `car_wash_id = null` در `model_has_roles` جلوگیری می‌کند.

## Cache، Session و Queue

Migrationهای زیر داخل پروژه موجود است:

- `cache`
- `cache_locks`
- `sessions`
- `jobs`
- `job_batches`
- `failed_jobs`

بنابراین مقادیر زیر بدون خطای نبودن جدول قابل استفاده‌اند:

```dotenv
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

## API و Sanctum

فایل `routes/api.php` در `bootstrap/app.php` ثبت شده است و مسیرها با پیشوند زیر در دسترس‌اند:

```text
/api/v1
```

برای Next.js در حالت Cookie-based:

```ts
fetch(`${API_URL}/sanctum/csrf-cookie`, {
  credentials: 'include',
});

fetch(`${API_URL}/api/v1/me`, {
  credentials: 'include',
  headers: { Accept: 'application/json' },
});
```

## مستندات

مستندات فنی در پوشه `docs` قرار دارند. گزارش اصلاحات بازبینی کامل پروژه:

```text
docs/07-CODE-REVIEW-FIXES.md
```

## CORS برای Next.js

در `.env` دامنه‌های مجاز را تنظیم کنید:

```dotenv
FRONTEND_URL=http://localhost:3000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000,localhost:8000,127.0.0.1:8000
```

در محیط Production، `SESSION_SECURE_COOKIE=true` و دامنه Session مشترک را تنظیم کنید.

## بررسی کامل روی ویندوز

دستور زیر مخرب است و همه جدول‌ها را حذف و از اول ایجاد می‌کند:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\verify-fresh.ps1
```

گزارش کامل بررسی:

```text
docs/08-FULL-AUDIT-REPORT.md
```
