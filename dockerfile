FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first (IMPORTANT)
COPY composer.json composer.lock ./

# Install PHP dependencies (no scripts)
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Copy rest of the project
COPY . .

# Laravel permissions
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
