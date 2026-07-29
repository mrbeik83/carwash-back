# معماری پروژه کارواش

## اجزای اصلی

1. Laravel 13 / PHP 8.3+ به‌عنوان Backend واحد
2. پنل مدیریت کل با Blade در `/admin`
3. پنل هر کارواش با Blade در `/wash-panel/{carWash:slug}`
4. لندینگ رزرو Next.js روی دامنه جدا و اتصال با REST API
5. Sanctum برای احراز هویت SPA با Cookie
6. Spatie Permission v6 با Teams و `car_wash_id`
7. MySQL برای داده اصلی، Redis برای Cache / Queue / Rate Limit

## قانون Tenant

هر Resource مربوط به پنل کارواش باید `car_wash_id` داشته باشد یا از مدلی عبور کند که به کارواش وصل است. Middleware کارواش جاری را فعال می‌کند و Policy تعلق Resource را دوباره بررسی می‌کند.

## Guard

فقط `web` برای نقش‌ها و Permissionها استفاده می‌شود. `auth:sanctum` کاربر همان guard را برای API شناسایی می‌کند. این کار از تکرار نقش‌ها بین API و Blade جلوگیری می‌کند.
