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

# Stage 3 - Backend (Laravel + PHP + Composer + Nginx + Supervisor)
FROM php:8.4-fpm

# Install system dependencies and Nginx/Supervisor
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip \
    nginx supervisor \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath opcache gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Nginx & Supervisor
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www

# Copy app files
COPY . .

# Copy vendor dependencies from composer_deps
COPY --from=composer_deps /app/vendor/ ./vendor/

# Copy built frontend PROPERLY
COPY --from=frontend /app/public/build ./public/build

# Final composer dump-autoload (to optimize and generate classes map)
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 80

# Start Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
