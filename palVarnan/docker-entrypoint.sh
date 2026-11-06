#!/bin/bash
set -e

# Render provides $PORT (default to 80 if not provided)
export PORT="${PORT:-80}"

# Configure Apache port dynamically
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Check or generate APP_KEY if not provided
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is empty. Generating temporary application key..."
    php artisan key:generate --force || true
fi

# Ensure storage symlink exists
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link || true
fi

# Run migrations if enabled via environment variable
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Package discovery and cache cleanup
php artisan package:discover --ansi || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Ensure permissions for Laravel writable directories and upload paths
mkdir -p /var/www/html/storage/app/public /var/www/html/public/images /var/www/html/public/storage || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images

echo "==> Web Application ready. Starting Apache web server on port ${PORT}..."
exec apache2-foreground
