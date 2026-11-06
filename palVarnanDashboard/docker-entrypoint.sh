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
mkdir -p /var/www/html/public/images/home_promotions /var/www/html/public/images/web_images /var/www/html/public/images/banner_images /var/www/html/public/images/brand_images /var/www/html/public/images/category_images /var/www/html/public/images/gift_images /var/www/html/public/images/gift_variant_images /var/www/html/public/images/offer_images /var/www/html/public/images/product_images /var/www/html/public/images/product_thum_images /var/www/html/public/images/variant_images || true
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/images

echo "==> Application ready. Starting Apache web server on port ${PORT}..."
exec apache2-foreground
