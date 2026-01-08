# Step 1: PHP base
FROM php:8.2-cli

# Step 2: Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Step 3: Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Step 4: Set working directory
WORKDIR /var/www

# Step 5: Copy composer files first
COPY composer.json composer.lock ./

# Step 6: Install dependencies
RUN composer install --no-dev --no-scripts --optimize-autoloader

# Step 7: Copy full project
COPY . .

# Step 8: Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Step 9: Expose port
EXPOSE 10000

# Step 10: Start Laravel server
CMD php artisan serve --host=0.0.0.0 --port=10000
