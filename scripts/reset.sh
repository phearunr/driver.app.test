#!/bin/sh
# chmod u+x YourScript.sh

PATH_BASE="$(dirname "$0")/.."

echo "\nResetting Cache up project... \n"
echo "\nClearing Cache .. \n"
php artisan clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "\nDropping/recreating database"
php artisan migrate:refresh
echo "\nDone... :)\n"
