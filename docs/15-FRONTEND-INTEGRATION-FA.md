# قرارداد اتصال فرانت رزرو به بک‌اند کارواش

این سند برای تیم Next.js/React نوشته شده است و قرارداد فعلی API نسخه `v1` را توضیح می‌دهد.

## ۱. آدرس پایه

```text
Backend origin: http://localhost:8000
API base:       http://localhost:8000/api/v1
Frontend:       http://localhost:3000
```

در محیط واقعی، دامنه‌ها را از متغیرهای محیطی بخوانید و در کد ثابت نکنید.

نمونه فرانت:

```env
NEXT_PUBLIC_API_ORIGIN=https://api.example.ir
NEXT_PUBLIC_API_BASE_URL=https://api.example.ir/api/v1
```

## ۲. احراز هویت Sanctum به روش Cookie

این پروژه برای SPA از کوکی session و Laravel Sanctum استفاده می‌کند. توکن Bearer در Local Storage ذخیره نکنید.

ترتیب ورود:

1. دریافت CSRF cookie از `GET /sanctum/csrf-cookie`
2. درخواست رمز یک‌بارمصرف از `POST /api/v1/auth/request-otp`
3. تأیید رمز از `POST /api/v1/auth/verify-otp`
4. دریافت کاربر از `GET /api/v1/me`

تمام درخواست‌ها باید با credentials ارسال شوند.

```ts
import axios from "axios";

export const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL,
  withCredentials: true,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

export const backend = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_ORIGIN,
  withCredentials: true,
  headers: { Accept: "application/json" },
});

export async function initializeCsrf(): Promise<void> {
  await backend.get("/sanctum/csrf-cookie");
}
```

### درخواست OTP

`POST /api/v1/auth/request-otp`

```json
{
  "mobile": "09121234567",
  "purpose": "login"
}
```

پاسخ موفق:

```json
{
  "message": "کد تایید ارسال شد."
}
```

مقادیر مجاز `purpose`:

- `login`
- `register`
- `verify_mobile`

### تأیید OTP

`POST /api/v1/auth/verify-otp`

```json
{
  "mobile": "09121234567",
  "code": "123456",
  "purpose": "login",
  "full_name": "علی رضایی"
}
```

`full_name` برای کاربر جدید یا حسابی که هنوز نام ندارد استفاده می‌شود.

پاسخ:

```json
{
  "data": {
    "id": "01J...",
    "full_name": "علی رضایی",
    "mobile": "09121234567"
  }
}
```

### خروج

`POST /api/v1/auth/logout`

پس از خروج، state سمت فرانت و cache مربوط به کاربر، خودروها و رزروها پاک شود.

## ۳. دریافت صفحه عمومی کارواش

`GET /api/v1/car-washes/{slug}`

این endpoint عمومی است و برای ساخت صفحه معرفی، فهرست خدمات، انواع خودرو و قوانین رزرو استفاده می‌شود.

ساختار مهم پاسخ:

```ts
export interface PublicCarWashResponse {
  data: {
    id: string;
    name: string;
    slug: string;
    phone: string | null;
    mobile: string | null;
    city: string | null;
    province: string | null;
    address: string | null;
    description: string | null;
    logo_url: string | null;
    cover_image_url: string | null;
    location: {
      latitude: number | null;
      longitude: number | null;
    };
    timezone: string;
    currency_code: string;
    booking_policy: {
      minimum_notice_minutes: number;
      maximum_days_ahead: number;
      cancellation_deadline_minutes: number;
      auto_confirm: boolean;
      online_payment_required: boolean;
    };
    vehicle_types: VehicleType[];
    services: CarWashService[];
  };
}

export interface VehicleType {
  id: number;
  name: string;
  slug: string;
  size_class: string | null;
}

export interface CarWashService {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  base_price: number;
  default_duration_minutes: number;
  vehicle_prices: Array<{
    vehicle_type_id: number;
    vehicle_type: VehicleType | null;
    price: number;
    duration_minutes: number;
  }>;
}
```

### منطق قیمت

هنگامی که کاربر نوع خودرو را انتخاب می‌کند:

1. در `vehicle_prices` همان خدمت، رکورد مربوط به `vehicle_type_id` را پیدا کنید.
2. اگر وجود داشت، `price` و `duration_minutes` همان رکورد را نمایش دهید.
3. در غیر این صورت از `base_price` و `default_duration_minutes` استفاده کنید.
4. مبلغ نهایی را سرور هنگام ثبت رزرو دوباره محاسبه می‌کند؛ مبلغ محاسبه‌شده فرانت مرجع امنیتی نیست.

## ۴. دریافت ظرفیت‌های قابل رزرو

`GET /api/v1/car-washes/{slug}/availability?from=2026-08-01&to=2026-08-07`

پارامترها باید میلادی و با فرمت `YYYY-MM-DD` ارسال شوند. برای نمایش، از فیلدهای شمسی پاسخ استفاده کنید.

```ts
export interface AvailabilitySlot {
  id: number;
  date: string;
  persian_date: string;
  persian_date_label: string;
  weekday: string;
  starts_at: string;
  ends_at: string;
  local_start_time: string;
  local_end_time: string;
  capacity: number;
  reserved_count: number;
  remaining_capacity: number;
  status: "open";
}

export interface AvailabilityResponse {
  data: AvailabilitySlot[];
  meta: {
    timezone: string;
    from: string;
    to: string;
    persian_from: string;
    persian_to: string;
  };
}
```

نمونه پاسخ:

```json
{
  "data": [
    {
      "id": 215,
      "date": "2026-08-01",
      "persian_date": "1405/05/10",
      "persian_date_label": "شنبه ۱۰ مرداد ۱۴۰۵",
      "weekday": "شنبه",
      "starts_at": "2026-08-01T05:30:00+00:00",
      "ends_at": "2026-08-01T06:00:00+00:00",
      "local_start_time": "09:00",
      "local_end_time": "09:30",
      "capacity": 3,
      "reserved_count": 1,
      "remaining_capacity": 2,
      "status": "open"
    }
  ],
  "meta": {
    "timezone": "Asia/Tehran",
    "from": "2026-08-01",
    "to": "2026-08-07",
    "persian_from": "1405/05/10",
    "persian_to": "1405/05/16"
  }
}
```

قواعد رابط:

- اسلاتی را که در پاسخ وجود ندارد، قابل رزرو فرض نکنید.
- ظرفیت باقی‌مانده را فقط برای اطلاع نمایش دهید؛ هنگام ثبت رزرو ممکن است کاربر دیگری همان اسلات را رزرو کند.
- خطای ظرفیت در `POST /bookings` باید مدیریت شود و سپس availability دوباره واکشی شود.
- بازه پیشنهادی واکشی برای رابط هفته‌ای، ۷ روز است.

## ۵. خودروهای کاربر

تمام endpointهای این بخش نیازمند ورود هستند.

### فهرست خودروها

`GET /api/v1/vehicles`

### افزودن خودرو

`POST /api/v1/vehicles`

```json
{
  "vehicle_type_id": 1,
  "plate_number": "12 ب 345 ایران 67",
  "brand": "ایران خودرو",
  "model": "دنا پلاس",
  "color": "سفید",
  "production_year": 1403,
  "nickname": "ماشین شخصی",
  "is_default": true
}
```

### حذف خودرو

`DELETE /api/v1/vehicles/{id}`

پاسخ موفق `204 No Content` است.

```ts
export interface UserVehicle {
  id: number;
  vehicle_type_id: number;
  vehicle_type: VehicleType | null;
  plate_number: string | null;
  brand: string | null;
  model: string | null;
  color: string | null;
  production_year: number | null;
  nickname: string | null;
  is_default: boolean;
  created_at: string | null;
}
```

## ۶. ثبت رزرو

`POST /api/v1/bookings`

نیازمند ورود است.

حالت خودرو ذخیره‌شده:

```json
{
  "booking_slot_id": 215,
  "vehicle_id": 8,
  "customer_name": "علی رضایی",
  "customer_mobile": "09121234567",
  "customer_note": "لطفاً داخل خودرو هم نظافت شود.",
  "service_ids": [3, 5],
  "qr_token": null
}
```

حالت بدون خودروی ذخیره‌شده:

```json
{
  "booking_slot_id": 215,
  "vehicle_type_id": 1,
  "vehicle_plate": "12 ب 345 ایران 67",
  "customer_name": "علی رضایی",
  "customer_mobile": "09121234567",
  "service_ids": [3]
}
```

قواعد مهم:

- `service_ids` باید حداقل یک عضو داشته باشد.
- خدمت باید فعال و متعلق به همان کارواش اسلات باشد.
- خودرو ذخیره‌شده باید متعلق به کاربر واردشده باشد.
- اگر `vehicle_id` ارسال نمی‌شود، `vehicle_type_id` اجباری است.
- ظرفیت با transaction و lock در سرور کنترل می‌شود.
- قیمت و مدت خدمات در سرور محاسبه می‌شود.
- اگر سیاست کارواش `auto_confirm=true` باشد، رزرو مستقیم `confirmed` می‌شود؛ در غیر این صورت `pending` است.

## ۷. ساختار رزرو در پاسخ

```ts
export type BookingStatusValue =
  | "pending"
  | "confirmed"
  | "checked_in"
  | "in_progress"
  | "completed"
  | "cancelled"
  | "no_show"
  | "rejected";

export interface LabelValue<T extends string = string> {
  value: T;
  label: string;
}

export interface BookingResponseItem {
  id: string;
  tracking_code: string;
  status: LabelValue<BookingStatusValue>;
  payment_status: LabelValue;
  source: LabelValue;
  customer: { name: string; mobile: string };
  vehicle: {
    id: number | null;
    plate: string | null;
    type: string | null;
  };
  amounts: {
    subtotal: number;
    discount: number;
    payable: number;
    currency_code: string;
  };
  customer_note: string | null;
  car_wash: {
    id: string;
    name: string;
    slug: string;
    timezone: string;
    address: string | null;
    phone: string | null;
    mobile: string | null;
  } | null;
  slot: {
    id: number;
    starts_at: string;
    ends_at: string;
    date: string;
    persian_date: string;
    persian_date_label: string;
    weekday: string;
    local_start_time: string;
    local_end_time: string;
  } | null;
  items: Array<{
    service_id: number;
    name: string;
    quantity: number;
    unit_price: number;
    duration_minutes: number;
    discount_amount: number;
    total_amount: number;
  }>;
  payments?: Array<unknown>;
  created_at: string | null;
  updated_at: string | null;
}
```

### فهرست رزروها

`GET /api/v1/bookings?page=1`

پاسخ صفحه‌بندی‌شده است و در کنار `data`، لینک‌ها و meta صفحه‌بندی Laravel برگردانده می‌شود.

### جزئیات رزرو

`GET /api/v1/bookings/{publicId}`

### لغو رزرو

`POST /api/v1/bookings/{publicId}/cancel`

```json
{
  "reason": "تغییر برنامه"
}
```

اگر از مهلت لغو گذشته باشد، پاسخ `422` دریافت می‌شود.

## ۸. تاریخ، تقویم و منطقه زمانی

قانون اصلی:

- برای query و request، تاریخ میلادی `YYYY-MM-DD` ارسال شود.
- timestampهای `starts_at` و `ends_at` استاندارد ISO 8601 و مرجع قطعی زمان هستند.
- برای نمایش فارسی، از `persian_date`, `persian_date_label`, `weekday`, `local_start_time` استفاده شود.
- زمان مرورگر را به‌جای زمان محلی اعلام‌شده توسط API مبنا قرار ندهید؛ ممکن است کاربر در کشور دیگری باشد.
- منطقه زمانی هر کارواش از `data.timezone` یا `meta.timezone` خوانده شود.

برای انتخاب تاریخ شمسی در فرانت، می‌توانید تاریخ شمسی انتخاب‌شده را پیش از درخواست به میلادی تبدیل کنید؛ اما state داخلی رزرو بهتر است تاریخ میلادی و `slot.id` را نگه دارد.

## ۹. فرمت خطا

### خطای اعتبارسنجی 422

```json
{
  "message": "اطلاعات واردشده معتبر نیست.",
  "errors": {
    "booking_slot_id": [
      "ظرفیت این بازه تکمیل شده است."
    ]
  }
}
```

نمایش پیشنهادی:

- پیام فیلد را زیر همان کنترل نشان دهید.
- اولین پیام را toast کنید.
- در خطای ظرفیت، availability را invalidate/refetch کنید.

### 401

کاربر وارد نشده یا session منقضی شده است. کاربر را به مرحله ورود برگردانید و queryهای خصوصی را پاک کنید.

### 403

کاربر وارد شده اما مجوز عملیات را ندارد.

### 404

منبع وجود ندارد، غیرفعال است یا متعلق به کاربر نیست. بک‌اند برای جلوگیری از افشای اطلاعات، مالکیت نامعتبر را نیز ممکن است 404 برگرداند.

### 429

تعداد درخواست بیش از حد است. در OTP شمارش معکوس نمایش دهید و تا پایان cooldown دکمه ارسال مجدد را غیرفعال کنید.

## ۱۰. Query Keyهای پیشنهادی React Query

```ts
export const carWashKeys = {
  detail: (slug: string) => ["car-wash", slug] as const,
  availability: (slug: string, from: string, to: string) =>
    ["availability", slug, from, to] as const,
};

export const accountKeys = {
  me: ["me"] as const,
  vehicles: ["vehicles"] as const,
  bookings: (page = 1) => ["bookings", page] as const,
  booking: (id: string) => ["booking", id] as const,
};
```

پس از ثبت یا لغو رزرو:

- availability همان هفته invalidate شود.
- فهرست رزروهای کاربر invalidate شود.
- جزئیات رزرو مربوطه invalidate شود.

## ۱۱. ترتیب پیشنهادی صفحات فرانت

1. صفحه معرفی کارواش و انتخاب خدمات
2. انتخاب نوع خودرو یا خودروی ذخیره‌شده
3. انتخاب روز و اسلات از availability
4. ورود/OTP در صورت وارد نبودن
5. مرور نهایی قیمت، زمان و قوانین
6. ثبت رزرو
7. صفحه موفقیت با `tracking_code`
8. ناحیه کاربری: خودروها، رزروهای آینده، تاریخچه و لغو

## ۱۲. تنظیمات بک‌اند برای دامنه واقعی

نمونه:

```env
APP_URL=https://api.example.ir
FRONTEND_URL=https://example.ir
CORS_ALLOWED_ORIGINS=https://example.ir,https://www.example.ir
SANCTUM_STATEFUL_DOMAINS=example.ir,www.example.ir,api.example.ir
SESSION_DOMAIN=.example.ir
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

اگر فرانت و بک‌اند روی siteهای کاملاً متفاوت قرار دارند، سیاست cookie و SameSite باید با زیرساخت HTTPS بررسی شود. بهترین حالت استفاده از زیردامنه‌های یک دامنه اصلی است.

## ۱۳. چک‌لیست تحویل فرانت

- [ ] axios/fetch با credentials فعال است.
- [ ] پیش از ورود، CSRF cookie گرفته می‌شود.
- [ ] تاریخ‌های query به‌صورت میلادی ارسال می‌شوند.
- [ ] تاریخ شمسی و ساعت محلی از پاسخ API نمایش داده می‌شوند.
- [ ] قیمت نهایی پاسخ رزرو، جایگزین محاسبه موقت فرانت می‌شود.
- [ ] خطای 422 ظرفیت باعث refetch اسلات‌ها می‌شود.
- [ ] session منقضی‌شده مدیریت می‌شود.
- [ ] دکمه ثبت رزرو هنگام ارسال دوباره قابل کلیک نیست.
- [ ] صفحه موفقیت کد رهگیری را نگه می‌دارد.
- [ ] اطلاعات خصوصی در Local Storage ذخیره نمی‌شود.
- [ ] تست موبایل، RTL، اعداد فارسی و اینترنت ضعیف انجام شده است.

## ۱۴. مرجع ماشینی

فایل کامل OpenAPI در مسیر زیر قرار دارد:

```text
docs/openapi.yaml
```
