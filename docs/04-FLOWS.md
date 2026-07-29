# فلوهای اصلی

## ورود مشتری

درخواست OTP -> Rate limit -> ذخیره Hash کد -> ارسال پیامک -> تایید کد -> ایجاد/بازیابی User -> Session Sanctum

## ثبت کارواش

ایجاد کارواش Pending -> اتصال Owner -> تکمیل اطلاعات -> بررسی ادمین کل -> Active یا Rejected -> ساخت تنظیمات و اسلات‌ها

## رزرو مشتری

کارواش -> نوع خودرو -> خدمات -> تاریخ -> اسلات -> اطلاعات مشتری -> محاسبه قیمت در Backend -> Lock اسلات -> ثبت رزرو -> افزایش ظرفیت اشغال -> پیام تایید

## عملیات کارواش

Pending -> Confirmed -> Checked In -> In Progress -> Completed

مسیرهای خروج: Cancelled، Rejected، No Show

## نقش

Route کارواش -> Set team id -> بررسی عضویت فعال -> بررسی Permission -> Policy مالکیت Resource -> Controller/Action
