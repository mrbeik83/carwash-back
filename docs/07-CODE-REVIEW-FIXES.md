# گزارش بازبینی و اصلاح کامل پروژه

## خطاهای اجرایی برطرف‌شده

1. نقش `super-admin` با Team خالی در `model_has_roles` ثبت می‌شد و MySQL خطای `car_wash_id cannot be null` می‌داد.
   - Super Admin به ستون `users.is_super_admin` منتقل شد.
   - `Gate::before` بر اساس همین ستون کار می‌کند.
   - Seeder، Middleware و Testها اصلاح شدند.

2. `routes/api.php` در Bootstrap ثبت نشده بود.
   - گزینه `api:` به `bootstrap/app.php` اضافه شد.
   - تمام Routeهای `/api/v1` اکنون ثبت می‌شوند.

3. کلاس `App\Actions\Bookings\CreateBookingAction` وجود نداشت.
   - Action کامل ساخت رزرو، محاسبه قیمت، قفل ظرفیت، Snapshot و QR tracking اضافه شد.

4. فایل `tests/TestCase.php` حذف شده بود.
   - فایل استاندارد TestCase بازگردانده شد.

5. فایل‌های Vite حذف شده بودند.
   - `resources/css/app.css`
   - `resources/js/app.js`
   - `resources/js/bootstrap.js`
   بازگردانده شدند.

6. پروژه از Database Cache و Database Queue استفاده می‌کرد، اما جدول‌های لازم وجود نداشتند.
   - Migration جدول‌های Cache و Queue اضافه شد.

7. فایل README دارای Conflict حل‌نشده Git بود.
   - README کاملاً بازنویسی شد.

8. مسیرهای Schedule با Scoped Binding از نام‌های `{rule}` و `{exception}` استفاده می‌کردند، در حالی که Relationهای مدل `capacityRules` و `scheduleExceptions` هستند.
   - پارامترها به `{capacityRule}` و `{scheduleException}` تغییر کردند.

9. گزارش‌گیری مقدار پیش‌فرض را اشتباه به متد `Request::date()` می‌داد.
   - اعتبارسنجی و ساخت تاریخ‌های پیش‌فرض اصلاح شد.

10. Route ورود وجود نداشت و Middleware `auth` می‌توانست به Route ناموجود `login` هدایت کند.
    - ورود با رمز، ورود OTP، خروج و صفحه بدون دسترسی اضافه شد.

## اصلاحات امنیتی و Tenant Isolation

- اسلات، خدمات، خودرو و QR هنگام ثبت رزرو از نظر مالکیت بررسی می‌شوند.
- قیمت رزرو فقط در Backend محاسبه می‌شود.
- ساخت رزرو و افزایش ظرفیت در Transaction و `lockForUpdate()` انجام می‌شود.
- پرداخت حضوری با قفل رزرو و کنترل مبلغ باقی‌مانده ثبت می‌شود.
- عضو متعلق به کارواش دیگری از Route مدیریت اعضا قابل تغییر نیست.
- مالک توسط مدیر عادی قابل حذف یا تغییر نقش نیست.
- دعوت ایمیلی و موبایلی فقط توسط دریافت‌کننده واقعی قابل پذیرش است.
- پذیرش دعوت از GET به POST تغییر کرد.
- تغییر وضعیت رزرو بر اساس Permission اختصاصی هر Transition کنترل می‌شود.
- Scope کارواش بعد از پایان Request به مقدار قبلی بازگردانده می‌شود.

## بررسی‌های انجام‌شده

- Syntax تمام فایل‌های PHP
- وجود تمام کلاس‌های `App\...`
- وجود تمام Controllerها و Methodهای Routeها
- ثبت Routeهای Web و API
- وجود تمام Route nameهای استفاده‌شده در Blade
- نبود Conflict markerهای Git
- هماهنگی Vite inputها با فایل‌های واقعی
- هماهنگی Seederها با Spatie Teams

## محدودیت محیط بازبینی

محیط اجرای بازبینی فاقد Extensionهای `PDO`, `mbstring`, `DOM` بود؛ بنابراین اجرای واقعی MySQL و PHPUnit در همان محیط ممکن نبود. با این حال Syntax، Bootstrap، Route registration و تحلیل ساختاری اجرا شد. تست نهایی روی سیستم توسعه با دستورهای زیر انجام شود:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
npm install
npm run build
```
