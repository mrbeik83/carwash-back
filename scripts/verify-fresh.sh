#!/usr/bin/env bash
set -euo pipefail

echo "WARNING: this command drops all database tables."

required=(mbstring pdo_mysql openssl tokenizer xml dom xmlwriter ctype fileinfo)
loaded="$(php -m)"
for extension in "${required[@]}"; do
  if ! grep -Fxq "$extension" <<<"$loaded"; then
    echo "Missing PHP extension: $extension" >&2
    exit 1
  fi
done

composer install
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
npm install
npm run build

echo "All project verification steps completed successfully."
