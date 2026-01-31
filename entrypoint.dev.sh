#!/bin/bash
set -e

# Composer
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist
else
    echo "✅ Composer dependencies already installed"
fi

# Node
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node dependencies..."
    npm install
    echo "⚙️ Building assets..."
    npm run build
else
    echo "✅ Node dependencies already installed"
    echo "⚙️ Building assets..."
    npm run build
fi

# App key
if grep -qE "^APP_KEY=base64:.+" .env; then
    echo "✅ App key already set"
else
    echo "🔑 Generating app key..."
    php artisan key:generate --force
fi

# Octane
if [ ! -f "public/frankenphp-worker.php" ]; then
    echo "📦 Installing Octane..."
    php artisan octane:install --server=frankenphp --no-interaction
else
    echo "✅ Octane already installed"
fi

echo "🗃️ Running migrations..."
php artisan migrate --force --seed

# Shield permissions
PERM_COUNT=$(php artisan tinker --execute="echo \Spatie\Permission\Models\Permission::count();" 2>/dev/null | tail -n 1 | tr -d '[:space:]')
if [ "$PERM_COUNT" = "0" ] || [ -z "$PERM_COUNT" ]; then
    echo "🛡️ Generating Shield permissions..."
    php artisan shield:generate --all --panel=admin --option=permissions
else
    echo "✅ Shield permissions already exist ($PERM_COUNT permissions)"
fi

# Super-admin
HAS_ROLE=$(php artisan tinker --execute="echo \App\Models\User::find(1)?->hasRole('super_admin') ? 'yes' : 'no';" 2>/dev/null | tail -n 1 | tr -d '[:space:]')
if [ "$HAS_ROLE" != "yes" ]; then
    echo "👑 Assigning super-admin role to user #1..."
    php artisan shield:super-admin --user=1 --panel=admin
else
    echo "✅ Super-admin already configured"
fi

echo "🚀 Starting Octane server..."
exec php artisan octane:frankenphp --workers=1 --max-requests=1
