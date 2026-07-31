# راهنمای فایل‌های جایگزینی و اضافه‌شده

این سند برای قرار دادن Patch در پروژه فعلی تهیه شده است.

## روش پیشنهادی

فایل `carwash-arino-theme-patch.zip` را در Root پروژه Laravel Extract کنید:

```text
carwash-app-back/
├── app/
├── bootstrap/
├── config/
├── public/
├── resources/
├── routes/
└── ...
```

در زمان Extract، گزینه **Replace existing files** را انتخاب کنید.

فایل‌های زیر داخل Patch قرار ندارند و نباید جایگزین شوند:

```text
.env
vendor/
node_modules/
public/build/
```

## ۱. فایل‌های ورود جداگانه

```text
routes/admin-auth.php
routes/carwash-auth.php

app/Http/Controllers/Auth/AdminAuthenticatedSessionController.php
app/Http/Controllers/Auth/CarWashAuthenticatedSessionController.php

resources/views/auth/admin-login.blade.php
resources/views/auth/carwash-login.blade.php
resources/views/auth/carwash-select.blade.php
resources/views/auth/portal.blade.php
```

کاربرد:

- `/admin/login`: فقط Super Admin
- `/carwash/login`: مالک و کارکنان کارواش
- `/carwash/select`: انتخاب کارواش برای کاربر چندکارواشی

## ۲. Layoutها و Sidebarها

```text
resources/views/layouts/auth.blade.php
resources/views/layouts/admin.blade.php
resources/views/layouts/carwash.blade.php

resources/views/partials/admin/sidebar.blade.php
resources/views/partials/carwash/sidebar.blade.php
resources/views/partials/flash.blade.php
```

## ۳. اجزای قابل استفاده مجدد

```text
resources/views/components/icon.blade.php
resources/views/components/status-badge.blade.php
resources/views/components/empty-state.blade.php
resources/views/components/panel/nav-link.blade.php
resources/views/components/panel/stat-card.blade.php
```

## ۴. پنل مدیریت کل

Controllerهای تکمیل‌شده:

```text
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Admin/BookingController.php
app/Http/Controllers/Admin/FinanceController.php
app/Http/Controllers/Admin/ReportController.php
app/Http/Controllers/Admin/RoleController.php
app/Http/Controllers/Admin/SystemSettingController.php
app/Http/Controllers/Admin/AuditLogController.php
```

Viewهای مدیریت کل:

```text
resources/views/admin/dashboard.blade.php
resources/views/admin/car-washes/_form.blade.php
resources/views/admin/car-washes/index.blade.php
resources/views/admin/car-washes/create.blade.php
resources/views/admin/car-washes/edit.blade.php
resources/views/admin/car-washes/show.blade.php
resources/views/admin/users/index.blade.php
resources/views/admin/bookings/index.blade.php
resources/views/admin/finance/index.blade.php
resources/views/admin/reports/index.blade.php
resources/views/admin/roles/index.blade.php
resources/views/admin/audit-logs/index.blade.php
resources/views/admin/settings/index.blade.php
```

## ۵. پنل کارواش

```text
resources/views/carwash/dashboard.blade.php
resources/views/carwash/bookings/index.blade.php
resources/views/carwash/bookings/show.blade.php
resources/views/carwash/services/index.blade.php
resources/views/carwash/schedule/index.blade.php
resources/views/carwash/members/index.blade.php
resources/views/carwash/customers/index.blade.php
resources/views/carwash/payments/index.blade.php
resources/views/carwash/reports/index.blade.php
resources/views/carwash/qr/index.blade.php
resources/views/carwash/profile/edit.blade.php
resources/views/carwash/settings/edit.blade.php
```

## ۶. Assetهای قالب آرینو

```text
public/vendor/arino/fonts/payda/
public/vendor/arino/images/
public/vendor/arino/js/chart.js
```

## ۷. CSS و JavaScript

```text
resources/css/app.css
resources/js/app.js
```

این فایل‌ها شامل رنگ‌بندی آرینو، فونت Peyda، Dark mode، Sidebar موبایل، Dropdown، Confirmation، Password Toggle و Tabهای ورود هستند.

## ۸. Routeها و Middleware

```text
routes/web.php
routes/admin.php
routes/carwash.php
bootstrap/app.php
app/Http/Middleware/EnsureSuperAdmin.php
```

## ۹. پس از جایگزینی

```powershell
composer dump-autoload
npm install
php artisan optimize:clear
php artisan migrate
npm run build
php artisan route:list
php artisan test
```

در محیط توسعه و در صورت امکان حذف داده‌ها:

```powershell
php artisan migrate:fresh --seed
```

## ۱۰. مسیرهای تست دستی

```text
http://localhost:8000/login
http://localhost:8000/admin/login
http://localhost:8000/carwash/login
```
