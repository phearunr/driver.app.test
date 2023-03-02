#!/bin/sh
# chmod u+x YourScript.sh

PATH_BASE="$(dirname "$0")/.."

echo "\nSetting up Cache up project... \n"
echo "\nClearing Cache .. \n"
php artisan clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "\nInstalling... dependencies ...\n"
composer install --no--interaction

# npm install

#create .evn if not exists
if [-f "$PATH_BASE/.env"]
    echo "\n.evn file already exists.\n"
else
    echo "\Creating .evn file.\n"
    cp .env.example .env
