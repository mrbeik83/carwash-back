# دیتابیس

## هویت و دسترسی

- `users`: تمام اشخاص سیستم
- جداول Spatie: Role و Permission
- `car_wash_user`: عضویت تجاری کاربر در کارواش
- `car_wash_invitations`: دعوت پرسنل
- `otp_codes`: کدهای موقت ورود

## کارواش

- `car_washes`: مشخصات اصلی
- `car_wash_settings`: تنظیمات رزرو
- `car_wash_services`: خدمات
- `service_vehicle_prices`: قیمت و زمان خدمت برای نوع خودرو
- `vehicle_types`: انواع خودرو

## تقویم و رزرو

- `capacity_rules`: برنامه هفتگی
- `schedule_exceptions`: تعطیلی یا ظرفیت خاص
- `booking_slots`: بازه واقعی قابل رزرو
- `bookings`: سفارش رزرو
- `booking_items`: Snapshot خدمات و قیمت
- `booking_status_histories`: تاریخچه وضعیت

## مالی و بازاریابی

- `payments`: تلاش و ثبت پرداخت
- `qr_links`: لینک QR
- `qr_scans`: آمار اسکن
- `audit_logs`: لاگ امنیتی و مدیریتی
- `system_settings`: تنظیمات عمومی
- `notifications`: اعلان‌های Laravel

## قواعد

- مبالغ عدد صحیح ریال هستند.
- زمان‌های دقیق UTC ذخیره می‌شوند و با timezone کارواش نمایش داده می‌شوند.
- رزرو و پرداخت Hard Delete نمی‌شوند.
- اسلات با `lockForUpdate()` رزرو می‌شود.
