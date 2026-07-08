#!/bin/sh
set -e

cd /var/www/html

# Fix permissions for www-data (PHP-FPM user)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure Vite manifest is accessible (Laravel looks for build/manifest.json)
if [ -f /var/www/html/public/build/.vite/manifest.json ] && [ ! -f /var/www/html/public/build/manifest.json ]; then
    cp /var/www/html/public/build/.vite/manifest.json /var/www/html/public/build/manifest.json
fi

# Wait for database
echo "Waiting for database..."
until php artisan db:show 2>/dev/null; do
    sleep 2
done
echo "Database ready."

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed if fresh install
if [ ! -f /var/www/html/storage/.seeded ]; then
    echo "Running seeders..."
    php artisan db:seed --force
    touch /var/www/html/storage/.seeded
fi

echo "Starting PHP-FPM..."
php-fpm
