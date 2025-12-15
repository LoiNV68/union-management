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

# Configure PHP-FPM to not clear env vars
RUN echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Nginx (Overwrite default site)
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Configure Supervisor
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

# Copy and set entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 80

# Use entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
