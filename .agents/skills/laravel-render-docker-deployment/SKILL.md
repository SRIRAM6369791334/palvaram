---
name: laravel-render-docker-deployment
description: Deploy Laravel applications (Laravel 8, 10, 11, 12) to Render Web Services using Docker, Apache, and TiDB Cloud Serverless MySQL. Includes fixes for Render dynamic PORT binding, SSL/TLS database connections, HTTPS mixed-content asset blocks, and clean MySQL database migration.
---

# Laravel Docker Deployment on Render with TiDB Cloud MySQL

This skill codifies the complete, battle-tested playbook for deploying Laravel applications (both standalone and monorepo setups with frontend/admin dashboards) to **Render Web Services** using **Docker** and connecting to **TiDB Cloud Serverless MySQL**.

---

## 1. Core Architecture & Monorepo Structure

In projects where the repository contains multiple Laravel apps (e.g. `dash/` for Admin Panel and `web/` for Customer Facing Site):
- **Dashboard (`dash`)**: Set Render **Root Directory** to `dash`.
- **Website (`web`)**: Set Render **Root Directory** to `web`.
- Each sub-app maintains its own isolated `Dockerfile`, `apache.conf`, `docker-entrypoint.sh`, and `.dockerignore`.

---

## 2. Dockerfile Configuration Patterns

### A. PHP Version Matching
- **Laravel 8/9**: Use `FROM php:8.1-apache`
- **Laravel 10/11/12**: Use `FROM php:8.2-apache` (or `8.3-apache`)

### B. Standard Extensions & Dependencies
Avoid deprecated packages like `wkhtmltopdf` in modern Debian (Trixie/Bookworm). Use native PHP `Barryvdh\DomPDF` instead.

```dockerfile
# Base Image
FROM php:8.2-apache # (or php:8.1-apache for Laravel 8)

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

# Install system libraries and CA certificates for TiDB SSL
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    fonts-dejavu \
    fonts-freefont-ttf \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache VirtualHost
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Composer Cache Optimization
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader --no-interaction

# Copy App Code
COPY . /var/www/html

# Dump Autoload
RUN composer dump-autoload --optimize --no-dev

# Setup Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Writable Storage Permissions
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80 10000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
```

---

## 3. Dynamic Port Binding & Entrypoint Script

Render dynamically supplies an arbitrary `$PORT` (typically 10000). The entrypoint script must dynamically update Apache:

### `apache.conf`:
```apache
<VirtualHost *:${PORT}>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### `docker-entrypoint.sh`:
```bash
#!/bin/bash
set -e

export PORT="${PORT:-80}"
echo "Listen ${PORT}" > /etc/apache2/ports.conf

# Generate key if empty
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force || true
fi

# Ensure storage link
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link || true
fi

# Run migrations if enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force || true
fi

php artisan package:discover --ansi || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec apache2-foreground
```

---

## 4. Solving Mixed Content (CSS / JS Blocked)

Because Render terminates SSL at Cloudflare/Load Balancer and talks HTTP to the container, Laravel generates insecure `http://` asset links unless instructed otherwise.

### Step 1: Trust Proxies
- **Laravel 8/9/10** in `app/Http/Middleware/TrustProxies.php`:
  ```php
  protected $proxies = '*';
  ```
- **Laravel 11/12** in `bootstrap/app.php`:
  ```php
  ->withMiddleware(function (Middleware $middleware) {
      $middleware->trustProxies(at: '*');
  })
  ```

### Step 2: Force HTTPS Scheme
In `app/Providers/AppServiceProvider.php` inside `boot()`:
```php
if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
    \Illuminate\Support\Facades\URL::forceScheme('https');
}
```

---

## 5. TiDB Cloud MySQL Setup & Import

1. **SSL/TLS Requirement:**
   In Render environment variables, pass:
   ```env
   MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt
   ```
2. **Schema Incompatibility Fixes:**
   - TiDB does not support `latin1_swedish_ci` -> Replace with `utf8mb4_unicode_ci`.
   - TiDB does not support `ENGINE=MyISAM` -> Replace with `ENGINE=InnoDB`.
   - TiDB requires `AUTO_INCREMENT` and `PRIMARY KEY` to be defined **inside** the `CREATE TABLE` statement (not deferred `ALTER TABLE` statements created by phpMyAdmin).
3. **Clean Dump & Stream Import Command:**
   ```bash
   mysqldump.exe --host=127.0.0.1 --user=root --default-character-set=utf8mb4 saaluvesa_db > clean_dump.sql
   
   # Replace charsets
   (Get-Content clean_dump.sql) -replace "latin1_swedish_ci", "utf8mb4_unicode_ci" | Set-Content clean_dump.sql
   
   # Import with SSL directly to TiDB
   mysql.exe --host=<tidb-host> --port=4000 --user=<user> --password=<pass> --database=<dbname> --ssl --default-character-set=utf8mb4 < clean_dump.sql
   ```

---

## 6. Inter-App Linking (`APP_URL` & `MAIN_URL`)

When Admin Dashboard (`dash`) and Public Website (`web`) communicate:
- **`dash` Service**:
  - `APP_URL`: `https://<dash-domain>.onrender.com`
  - `MAIN_URL`: `https://<web-domain>.onrender.com` (for website links)
- **`web` Service**:
  - `APP_URL`: `https://<web-domain>.onrender.com`
  - `MAIN_URL`: `https://<dash-domain>.onrender.com` (for loading media/storage uploaded in dash)

---

## 7. Deployed Projects Registry (Historical Deployments)

This registry tracks live projects deployed using this skill for instant context retrieval in future sessions.

### 🏢 Project 1: Saaluvesa Enterprises Private Limited
- **Local Path:** `C:\xampp\htdocs\Saaluvesa Enterprises Private Limited\New folder`
  - Admin App: `dash` (Laravel 8, PHP 8.1 Apache)
  - Public Web: `web` (Laravel 12, PHP 8.2 Apache)
- **GitHub Repository:** `https://github.com/SRIRAM6369791334/salluvesa.git`
- **TiDB Cloud Database:** `saaluvesa_db` (Cluster: `SRIRAM`, Port: `4000`, User: `EyMBLb8z3cuyjH7.root`)
- **Render Project Dashboard:** [Render Environment](https://dashboard.render.com/project/prj-db6vn97f3r2c73cn8ln0/environment/evm-db6vn97f3r2c73cn8lng)
- **TiDB Cluster Overview:** [TiDB Cloud Console](https://tidbcloud.com/tidbs/10051779094385099883/overview?orgId=1372813089209373925)
- **Live URLs:**
  - **Dashboard:** https://salluvesa.onrender.com/
  - **Website:** https://salluvesa-1.onrender.com/

---

### 🥛 Project 2: PalVarnan (Paul's International)
- **Local Path:** `C:\xampp\htdocs\pauls`
  - Admin App: `palVarnanDashboard` (Laravel 8, PHP 8.1 Apache)
  - Public Web: `palVarnan` (Laravel 12, PHP 8.2 Apache)
- **GitHub Repository:** `https://github.com/SRIRAM6369791334/palvaram.git`
- **TiDB Cloud Database:** `palvarnan_db` (Cluster: `SRIRAM`, Port: `4000`, User: `EyMBLb8z3cuyjH7.root`)
- **Live URLs:**
  - **Dashboard:** https://palvaram.onrender.com/
  - **Website:** https://palvaram-web.onrender.com/

