# نقشه Routeهای پنل‌ها

## ورودی مشترک

| Method | URI | Name | کاربرد |
|---|---|---|---|
| GET | `/` | `home` | هدایت بر اساس نوع کاربر |
| GET | `/login` | `login` | انتخاب پنل |

## احراز هویت مدیریت کل

| Method | URI | Name |
|---|---|---|
| GET | `/admin/login` | `admin.login` |
| POST | `/admin/login` | `admin.login.store` |
| POST | `/admin/logout` | `admin.logout` |

## مدیریت کل

| URI | Name |
|---|---|
| `/admin/dashboard` | `admin.dashboard` |
| `/admin/car-washes` | `admin.car-washes.index` |
| `/admin/users` | `admin.users.index` |
| `/admin/bookings` | `admin.bookings.index` |
| `/admin/finance` | `admin.finance.index` |
| `/admin/reports` | `admin.reports.index` |
| `/admin/roles` | `admin.roles.index` |
| `/admin/audit-logs` | `admin.audit-logs.index` |
| `/admin/settings` | `admin.settings.index` |

## احراز هویت پنل کارواش

| Method | URI | Name |
|---|---|---|
| GET | `/carwash/login` | `carwash.login` |
| POST | `/carwash/login` | `carwash.login.store` |
| POST | `/carwash/login/request-otp` | `carwash.login.otp.request` |
| POST | `/carwash/login/verify-otp` | `carwash.login.otp.verify` |
| GET | `/carwash/select` | `carwash.select` |
| POST | `/carwash/logout` | `carwash.logout` |

## پنل هر کارواش

تمام مسیرها زیر این Prefix هستند:

```text
/carwash/{carWash:slug}
```

| URI نسبی | Name |
|---|---|
| `/dashboard` | `carwash.dashboard` |
| `/bookings` | `carwash.bookings.index` |
| `/services` | `carwash.services.index` |
| `/schedule` | `carwash.schedule.index` |
| `/members` | `carwash.members.index` |
| `/customers` | `carwash.customers.index` |
| `/payments` | `carwash.payments.index` |
| `/reports` | `carwash.reports.index` |
| `/qr-links` | `carwash.qr.index` |
| `/profile` | `carwash.profile.edit` |
| `/settings` | `carwash.settings.edit` |
