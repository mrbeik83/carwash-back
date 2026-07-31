# فهرست دقیق فایل‌های Patch آرینو

- فایل‌های جدید: **44**
- فایل‌های جایگزین‌شده: **47**
- فایل‌های تولیدی حذف‌شده: **2**

## فایل‌های جدید

- `app/Http/Controllers/Admin/AuditLogController.php`
- `app/Http/Controllers/Admin/BookingController.php`
- `app/Http/Controllers/Admin/FinanceController.php`
- `app/Http/Controllers/Admin/ReportController.php`
- `app/Http/Controllers/Admin/RoleController.php`
- `app/Http/Controllers/Admin/SystemSettingController.php`
- `app/Http/Controllers/Auth/AdminAuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/CarWashAuthenticatedSessionController.php`
- `app/Http/Middleware/EnsureSuperAdmin.php`
- `docs/11-ARINO-THEME-INTEGRATION.md`
- `docs/12-ROUTE-MAP.md`
- `docs/13-REPLACEMENT-GUIDE.md`
- `public/vendor/arino/images/auth-background.png`
- `public/vendor/arino/images/profile.jpg`
- `public/vendor/arino/images/qr-placeholder.jpg`
- `public/vendor/arino/js/chart.js`
- `resources/views/auth/admin-login.blade.php`
- `resources/views/auth/carwash-login.blade.php`
- `resources/views/auth/carwash-select.blade.php`
- `resources/views/auth/portal.blade.php`
- `resources/views/components/empty-state.blade.php`
- `resources/views/components/icon.blade.php`
- `resources/views/components/panel/nav-link.blade.php`
- `resources/views/components/panel/stat-card.blade.php`
- `resources/views/components/status-badge.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/auth.blade.php`
- `resources/views/layouts/carwash.blade.php`
- `resources/views/partials/admin/sidebar.blade.php`
- `resources/views/partials/carwash/sidebar.blade.php`
- `resources/views/partials/flash.blade.php`
- `routes/admin-auth.php`
- `routes/carwash-auth.php`
- `scripts/install-arino-ui.ps1`
- `storage/framework/cache/data/.gitignore`
- `storage/framework/sessions/.gitignore`
- `storage/framework/views/.gitignore`
- `tests/Feature/Routing/SeparatePanelAuthenticationRoutesTest.php`

## فایل‌های جایگزین‌شده

- `.env.example`
- `README.md`
- `app/Http/Controllers/Admin/DashboardController.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/CarWashPanel/BookingController.php`
- `app/Http/Controllers/CarWashPanel/CustomerController.php`
- `app/Models/AuditLog.php`
- `bootstrap/app.php`
- `config/app.php`
- `docs/01-ARCHITECTURE.md`
- `docs/09-CHANGED-FILES.md`
- `docs/10-VALIDATION-RESULTS.txt`
- `resources/css/app.css`
- `resources/js/app.js`
- `resources/views/admin/audit-logs/index.blade.php`
- `resources/views/admin/bookings/index.blade.php`
- `resources/views/admin/car-washes/_form.blade.php`
- `resources/views/admin/car-washes/create.blade.php`
- `resources/views/admin/car-washes/edit.blade.php`
- `resources/views/admin/car-washes/index.blade.php`
- `resources/views/admin/car-washes/show.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/finance/index.blade.php`
- `resources/views/admin/reports/index.blade.php`
- `resources/views/admin/roles/index.blade.php`
- `resources/views/admin/settings/index.blade.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/no-access.blade.php`
- `resources/views/carwash/bookings/index.blade.php`
- `resources/views/carwash/bookings/show.blade.php`
- `resources/views/carwash/customers/index.blade.php`
- `resources/views/carwash/dashboard.blade.php`
- `resources/views/carwash/members/index.blade.php`
- `resources/views/carwash/payments/index.blade.php`
- `resources/views/carwash/profile/edit.blade.php`
- `resources/views/carwash/qr/index.blade.php`
- `resources/views/carwash/reports/index.blade.php`
- `resources/views/carwash/schedule/index.blade.php`
- `resources/views/carwash/services/index.blade.php`
- `resources/views/carwash/settings/edit.blade.php`
- `resources/views/invitations/show.blade.php`
- `resources/views/layouts/panel.blade.php`
- `routes/admin.php`
- `routes/carwash.php`
- `routes/web.php`
- `scripts/verify-fresh.ps1`

## فایل‌های تولیدی که باید با `php artisan optimize:clear` پاک شوند

- `bootstrap/cache/packages.php`
- `bootstrap/cache/services.php`

---

# افزوده‌های نسخه فارسی و زمان‌بندی هفتگی ـ ۱۴۰۵/۰۵/۰۹

## هسته فارسی‌سازی

- `app/Support/PersianDate.php`
- `resources/views/components/persian-date.blade.php`
- `resources/views/components/persian-date-input.blade.php`
- `lang/fa/auth.php`
- `lang/fa/pagination.php`
- `lang/fa/validation.php`
- `config/app.php`
- `.env.example`

## زمان‌بندی و ظرفیت اسلات

- `database/migrations/2026_07_31_000100_add_slot_capacities_to_capacity_rules_table.php`
- `app/Models/CapacityRule.php`
- `app/Http/Requests/CarWashPanel/SaveWeeklyScheduleRequest.php`
- `app/Http/Requests/CarWashPanel/UpdateBookingSlotRequest.php`
- `app/Http/Controllers/CarWashPanel/ScheduleController.php`
- `app/Services/SlotGenerationService.php`
- `routes/carwash.php`
- `resources/views/carwash/schedule/index.blade.php`

## تجربه کاربری رزرو و پنل

- `app/Http/Controllers/CarWashPanel/BookingController.php`
- `resources/views/carwash/bookings/index.blade.php`
- `resources/views/carwash/bookings/show.blade.php`
- `resources/views/carwash/dashboard.blade.php`
- `resources/views/layouts/carwash.blade.php`
- `resources/views/partials/carwash/sidebar.blade.php`
- `resources/css/app.css`
- `resources/js/app.js`

## قرارداد API فرانت

- `app/Http/Resources/Api/V1/BookingResource.php`
- `app/Http/Resources/Api/V1/VehicleResource.php`
- `app/Http/Controllers/Api/V1/BookingController.php`
- `app/Http/Controllers/Api/V1/VehicleController.php`
- `app/Http/Controllers/Api/V1/PublicCarWashController.php`
- `docs/openapi.yaml`
- `docs/15-FRONTEND-INTEGRATION-FA.md`

## گزارش و راهنمای تحویل

- `DELIVERY-FA.md`
- `docs/14-PERSIAN-UX-AND-WEEKLY-SCHEDULING.md`
- `docs/16-IMPLEMENTATION-AND-VALIDATION-FA.md`
- `docs/17-PERSIAN-PANEL-VALIDATION.txt`
- `scripts/validate-persian-panel.php`
