# Base Image with PHP 8.2 and Apache for Laravel 12
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Avoid interactive prompts during apt install
ENV DEBIAN_FRONTEND=noninteractive
ENV MYSQL_ATTR_SSL_CA=/etc/ssl/certs/ca-certificates.crt

# Install system dependencies & libraries
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

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer from official Composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Apache VirtualHost configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy composer definition files first for layer caching
COPY composer.json composer.lock ./

# Install Composer dependencies without running artisan scripts yet
RUN composer install --no-dev --prefer-dist --no-scripts --no-autoloader --no-interaction

# Copy the rest of the application codebase
COPY . /var/www/html

# Generate optimized Composer autoloader
RUN composer dump-autoload --optimize --no-dev

# Setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Set directory permissions for Laravel
RUN mkdir -p /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default HTTP ports
EXPOSE 80 10000

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
