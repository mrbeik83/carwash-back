# سامانه مدیریت و رزرو کارواش

Backend پروژه با Laravel 12، دو پنل مستقل Blade، API مخصوص Next.js، Sanctum و Spatie Permission Teams.

## مسیرهای اصلی

```text
/                              انتخاب نوع پنل
/admin/login                   ورود مستقل مدیر اصلی
/admin/dashboard               پنل مدیریت کل

/carwash/login                 ورود مستقل مالک و کارکنان
/carwash/select                انتخاب کارواش برای کاربران چندکارواشی
/carwash/{carWash:slug}/dashboard
                               پنل اختصاصی همان کارواش

/api/v1                        API لندینگ Next.js
```

ورود مدیر اصلی و ورود کارواش Controller، Route و View جدا دارند؛ اما هر دو از جدول `users` و guard استاندارد `web` استفاده می‌کنند. این ساختار از تکرار User و Role جلوگیری می‌کند.

## طراحی رابط

رابط پنل‌ها بر اساس قالب فارسی آرینو بازطراحی شده است:

- فونت Peyda با اعداد فارسی
- رنگ اصلی نارنجی `#FF8229`
- رنگ ثانویه `#272C48`
- Sidebar واکنش‌گرا
- Dark mode
- صفحه ورود دو بخشی مشابه قالب
- جدول‌ها، کارت‌ها، فرم‌ها و نمودارهای یکپارچه
- Layout مستقل `admin` و `carwash`

تنها Assetهای ضروری قالب به پروژه منتقل شده‌اند. فایل‌های Demo فروشگاهی و JavaScript نمایشی قالب وارد پروژه نشده‌اند.

## پیش‌نیازها

- PHP 8.2+
- MySQL 8+
- Composer
- Node.js 20+
- افزونه‌های PHP: `mbstring`, `pdo_mysql`, `openssl`, `tokenizer`, `xml`, `dom`, `xmlwriter`, `ctype`, `fileinfo`

## نصب

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

مقادیر دیتابیس و Super Admin را در `.env` تنظیم کنید:

```dotenv
SUPER_ADMIN_NAME="مدیر کل سیستم"
SUPER_ADMIN_MOBILE="98912..."
SUPER_ADMIN_EMAIL="admin@example.com"
SUPER_ADMIN_PASSWORD="رمز قوی"
```

سپس:

```powershell
php artisan optimize:clear
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

> دستور `migrate:fresh` تمام داده‌های دیتابیس را حذف می‌کند و فقط در محیط توسعه مناسب است.

## ورود

### مدیر اصلی

```text
http://localhost:8000/admin/login
```

فقط کاربری وارد می‌شود که:

```text
users.is_super_admin = true
users.status = active
```

### مالک و کارکنان کارواش

```text
http://localhost:8000/carwash/login
```

ورود با رمز عبور یا OTP ممکن است. کاربر باید حداقل یک عضویت فعال در یک کارواش فعال داشته باشد.

## نقش‌ها

- `carwash-owner`
- `carwash-manager`
- `carwash-receptionist`
- `carwash-operator`
- `carwash-accountant`

مدیر اصلی Role اسپاتی ندارد؛ دسترسی کامل او با `users.is_super_admin` و `Gate::before` اعمال می‌شود.

## فایل‌های مهم رابط

```text
resources/views/layouts/admin.blade.php
resources/views/layouts/carwash.blade.php
resources/views/layouts/auth.blade.php

resources/views/partials/admin/sidebar.blade.php
resources/views/partials/carwash/sidebar.blade.php

resources/css/app.css
resources/js/app.js

public/vendor/arino/
```

## بررسی خودکار

در ویندوز:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\verify-fresh.ps1
```

این اسکریپت مخرب است و دیتابیس را از نو می‌سازد.

مستند کامل یکپارچه‌سازی قالب:

```text
docs/11-ARINO-THEME-INTEGRATION.md
```

## نسخه فارسی پنل و زمان‌بندی هفتگی

این نسخه شامل تقویم شمسی، رابط راست‌چین، برنامه هفتگی شنبه تا جمعه، اسلات‌های ۳۰/۶۰ دقیقه‌ای، ظرفیت مستقل هر اسلات، تقویم عملیاتی رزروها و قرارداد به‌روزشده API است.

مستندات اصلی:

- `docs/14-PERSIAN-UX-AND-WEEKLY-SCHEDULING.md`
- `docs/15-FRONTEND-INTEGRATION-FA.md`
- `docs/16-IMPLEMENTATION-AND-VALIDATION-FA.md`
- `docs/openapi.yaml`
