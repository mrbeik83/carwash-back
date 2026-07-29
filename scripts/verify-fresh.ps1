$ErrorActionPreference = "Stop"

Write-Host "WARNING: this command drops all database tables." -ForegroundColor Yellow

$requiredExtensions = @("mbstring", "pdo_mysql", "openssl", "tokenizer", "xml", "dom", "xmlwriter", "ctype", "fileinfo")
$loadedExtensions = php -m
$missingExtensions = $requiredExtensions | Where-Object { $loadedExtensions -notcontains $_ }

if ($missingExtensions.Count -gt 0) {
    throw "Missing PHP extensions: $($missingExtensions -join ', ')"
}

composer install
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
npm install
npm run build

Write-Host "All project verification steps completed successfully." -ForegroundColor Green
