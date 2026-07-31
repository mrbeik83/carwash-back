# مستند یکپارچه‌سازی قالب آرینو

## ۱. هدف

قالب فارسی آرینو به‌عنوان مرجع بصری استفاده شده و اجزای موردنیاز آن به ساختار استاندارد Laravel Blade منتقل شده‌اند. قالب فروشگاهی به‌صورت خام داخل پروژه کپی نشده است؛ چون فایل‌های Demo، JavaScriptهای نمایشی و صفحات نامرتبط نگهداری پروژه را سخت می‌کردند.

## ۲. معماری ورود و پنل‌ها

### پنل مدیریت کل

```text
GET  /admin/login
POST /admin/login
POST /admin/logout

GET  /admin/dashboard
```

فایل‌های ورود:

```text
routes/admin-auth.php
app/Http/Controllers/Auth/AdminAuthenticatedSessionController.php
resources/views/auth/admin-login.blade.php
```

فایل‌های پنل:

```text
routes/admin.php
resources/views/layouts/admin.blade.php
resources/views/partials/admin/sidebar.blade.php
resources/views/admin/**
```

### پنل کارواش

```text
GET  /carwash/login
POST /carwash/login
POST /carwash/login/request-otp
POST /carwash/login/verify-otp
POST /carwash/logout

GET  /carwash/select
GET  /carwash/{slug}/dashboard
```

فایل‌های ورود:

```text
routes/carwash-auth.php
app/Http/Controllers/Auth/CarWashAuthenticatedSessionController.php
resources/views/auth/carwash-login.blade.php
resources/views/auth/carwash-select.blade.php
```

فایل‌های پنل:

```text
routes/carwash.php
resources/views/layouts/carwash.blade.php
resources/views/partials/carwash/sidebar.blade.php
resources/views/carwash/**
```

## ۳. چرا Guard جدا ایجاد نشده است؟

ورود‌ها از نظر URL، Controller و View جدا هستند؛ اما هر دو از guard `web` استفاده می‌کنند. دلیل:

- همه اشخاص در جدول `users` هستند.
- Roleهای Spatie فقط برای عضویت کارواش استفاده می‌شوند.
- ساخت Guardهای متعدد Role و Permissionهای تکراری ایجاد می‌کرد.
- مدیر اصلی با `is_super_admin` و `Gate::before` کنترل می‌شود.
- Middlewareهای `admin` و `carwash.member` مرز پنل‌ها را اعمال می‌کنند.

## ۴. Layoutهای Blade

### `layouts/auth.blade.php`

صفحه ورود دو ستونه مطابق آرینو:

- تصویر پس‌زمینه
- لوگو و معرفی سامانه
- بخش فرم مستقل
- Dark mode
- Responsive mobile

### `layouts/admin.blade.php`

- Sidebar مدیریت کل
- Header مدیر اصلی
- منوهای کارواش‌ها، کاربران، رزروها، مالی، گزارش، نقش‌ها، لاگ و تنظیمات

### `layouts/carwash.blade.php`

- Sidebar وابسته به `$carWash`
- منوها بر اساس Permission با `@can`
- امکان تعویض کارواش
- Header اختصاصی کارواش

## ۵. صفحات پنل مدیریت کل

```text
admin/dashboard
admin/car-washes/index
admin/car-washes/create
admin/car-washes/edit
admin/car-washes/show
admin/users/index
admin/bookings/index
admin/finance/index
admin/reports/index
admin/roles/index
admin/audit-logs/index
admin/settings/index
```

## ۶. صفحات پنل کارواش

```text
carwash/dashboard
carwash/bookings/index
carwash/bookings/show
carwash/services/index
carwash/schedule/index
carwash/members/index
carwash/customers/index
carwash/payments/index
carwash/reports/index
carwash/qr/index
carwash/profile/edit
carwash/settings/edit
```

تمام عملیات موجود در Routeها در رابط پوشش داده شده‌اند:

- ایجاد و ویرایش خدمات
- قیمت بر اساس نوع خودرو
- ثبت رزرو حضوری
- تغییر وضعیت رزرو
- ثبت پرداخت حضوری
- دعوت و مدیریت عضو
- قوانین ظرفیت و استثنا
- تولید اسلات
- لینک QR و کمپین
- گزارش‌های نموداری
- تنظیمات رزرو
- پروفایل عمومی

## ۷. Assetهای منتقل‌شده از آرینو

```text
فونت اختصاصی در این بسته قرار نگرفته است؛ مسیر پیشنهادی در صورت افزودن فونت مجاز: public/vendor/arino/fonts/
public/vendor/arino/images/auth-background.png
public/vendor/arino/images/profile.jpg
public/vendor/arino/images/qr-placeholder.jpg
public/vendor/arino/js/chart.js
```

فایل اصلی JavaScript قالب وارد نشده است، چون رفتار Demo داشت و Submit فرم‌های ورود را متوقف می‌کرد. رفتارهای موردنیاز در فایل زیر بازنویسی شده‌اند:

```text
resources/js/app.js
```

## ۸. سیستم طراحی

```text
Primary:   #FF8229
Secondary: #272C48
Font:      Peyda
Direction: RTL
```

کلاس‌های قابل استفاده مجدد:

```text
panel-card
panel-card-header
panel-card-body
form-label
form-control
form-select
btn-primary
btn-secondary
btn-success
btn-danger
table-shell
data-table
```

## ۹. ترتیب جایگزینی

1. از پروژه فعلی Backup بگیرید.
2. فایل Patch را در Root پروژه Extract و Replace کنید.
3. فایل `.env` شخصی خود را نگه دارید.
4. دستورات زیر را اجرا کنید:

```powershell
composer dump-autoload
npm install
php artisan optimize:clear
php artisan migrate
npm run build
```

در محیط توسعه بدون داده مهم:

```powershell
php artisan migrate:fresh --seed
```

## ۱۰. بررسی نهایی

```powershell
php artisan route:list
php artisan test
npm run build
```

مسیرهای ورود را جداگانه باز کنید:

```text
/admin/login
/carwash/login
```

## ۱۱. نکته درباره فونت‌ها و Git

برای جلوگیری از خطای View path، فایل‌های `.gitignore` در این مسیرها نگهداری شده‌اند:

```text
storage/framework/views
storage/framework/sessions
storage/framework/cache/data
storage/logs
bootstrap/cache
```
