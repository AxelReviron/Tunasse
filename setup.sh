#!/bin/sh
set -e

# TODO: Check if migrations have already been run

echo "Running migrations..."
php artisan migrate --force --seed

echo "Generating permissions..."
php artisan shield:generate --all --panel=admin --option=permissions

echo "Creating super-admin user..."
php artisan shield:super-admin --user=1 --panel=admin

echo "Optimize application..."
php artisan optimize:clear
php artisan optimize

