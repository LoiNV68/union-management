# Stage 1 - Composer Dependencies
FROM composer:2 AS composer_deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --ignore-platform-reqs

# Stage 2 - Build Frontend (Vite)
FROM node:20 AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
# Copy vendor directory from composer_deps because Vite needs it for CSS references
COPY --from=composer_deps /app/vendor/ ./vendor/
RUN npm run build

# Stage 3 - Backend (Laravel + PHP)
FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath opcache gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app files
COPY . .

# Copy vendor dependencies from composer_deps
COPY --from=composer_deps /app/vendor/ ./vendor/

# Copy built frontend
COPY --from=frontend /app/public/build ./public/build

# Final composer dump-autoload
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy and set entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

# Use entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
