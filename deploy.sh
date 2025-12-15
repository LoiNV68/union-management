#!/bin/bash

echo "🚀 Starting Deployment..."

# 1. Build Docker Images
echo "📦 Building Docker Images..."
docker-compose build --no-cache

# 2. Start Services
echo "🔥 Starting Services..."
docker-compose up -d

# 3. Wait for database to be ready (optional but good)
echo "⏳ Waiting for Database..."
sleep 10

# 4. Run Migrations
echo "🗄️ Running Migrations..."
docker-compose exec app php artisan migrate --force

# 5. Clear Caches & Optimize
echo "🧹 Optimizing..."
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize

# 6. Set Permissions (Ensure storage is writable)
echo "🔒 Setting Permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

echo "✅ Deployment Complete!"
