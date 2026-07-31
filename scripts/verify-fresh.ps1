$ErrorActionPreference = "Stop"

Write-Host "WARNING: this command drops all database tables." -ForegroundColor Yellow

$requiredExtensions = @("mbstring", "pdo_mysql", "openssl", "tokenizer", "xml", "dom", "xmlwriter", "ctype", "fileinfo")
$loadedExtensions = php -m
$missingExtensions = $requiredExtensions | Where-Object { $loadedExtensions -notcontains $_ }

if ($missingExtensions.Count -gt 0) {
    throw "Missing PHP extensions: $($missingExtensions -join ', ')"
}

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

composer install
npm install
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
npm run build

Write-Host "Admin login:    /admin/login" -ForegroundColor Cyan
Write-Host "Car wash login: /carwash/login" -ForegroundColor Cyan
Write-Host "All project verification steps completed successfully." -ForegroundColor Green
