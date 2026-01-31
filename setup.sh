#!/bin/sh
set -e

echo "Running migrations..."
php artisan migrate --force
php artisan db:seed --force --class=ProdSeeder

echo "Generating permissions..."
php artisan shield:generate --all --panel=admin --option=permissions

echo "Creating super-admin user..."
php artisan shield:super-admin --user=1 --panel=admin

echo "Optimize application..."
php artisan optimize:clear
php artisan optimize

php artisan octane:frankenphp --port=443 --caddyfile=Caddyfile
