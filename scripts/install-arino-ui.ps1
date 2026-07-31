$ErrorActionPreference = "Stop"

$requiredDirectories = @(
    "storage/framework/views",
    "storage/framework/sessions",
    "storage/framework/cache/data",
    "storage/logs",
    "bootstrap/cache"
)

foreach ($directory in $requiredDirectories) {
    New-Item -ItemType Directory -Force $directory | Out-Null
}

composer dump-autoload
npm install
php artisan optimize:clear
php artisan route:list
npm run build

Write-Host ""
Write-Host "Arino UI integration is ready." -ForegroundColor Green
Write-Host "Admin login:    http://localhost:8000/admin/login" -ForegroundColor Cyan
Write-Host "Car wash login: http://localhost:8000/carwash/login" -ForegroundColor Cyan
Write-Host ""
Write-Host "Run migrations separately after reviewing your database:" -ForegroundColor Yellow
Write-Host "php artisan migrate" -ForegroundColor Yellow
