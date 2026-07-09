#!/bin/sh
set -e

cd /var/www/html

echo "==> Local dev entrypoint"

# Ensure a .env exists (fallback to example if it was not created on the host)
if [ ! -f .env ]; then
    echo "==> .env missing, copying from .env.example"
    cp .env.example .env
fi

# Install PHP dependencies if vendor is missing
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    echo "==> Installing Composer dependencies (first run, this can take a few minutes)..."
    composer install --no-interaction --prefer-dist
fi

# Generate an app key if none is set
if ! grep -q "^APP_KEY=base64:" .env; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# Install and build front-end assets if not present
if [ ! -d node_modules ]; then
    echo "==> Installing npm dependencies (first run, this can take a few minutes)..."
    npm install --ignore-scripts
fi

if [ ! -f public/build/manifest.json ]; then
    echo "==> Building front-end assets with Vite..."
    npm run build
fi

# Fix permissions for www-data (PHP-FPM workers)
chown -R www-data:www-data storage bootstrap/cache

# Wait for the database
echo "==> Waiting for database..."
until php artisan db:show >/dev/null 2>&1; do
    sleep 2
done
echo "==> Database ready."

# Run migrations
echo "==> Running migrations..."
php artisan migrate --force

# Seed once on a fresh install
if [ ! -f storage/.seeded ]; then
    echo "==> Running seeders..."
    php artisan db:seed --force
    touch storage/.seeded
fi

# Clear caches so config/route changes are picked up during development
php artisan optimize:clear || true

echo "==> Starting PHP-FPM..."
exec php-fpm
