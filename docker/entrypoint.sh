#!/bin/bash
set -e

echo "Starting application..."

# Run migrations (skip if fails)
php artisan migrate --force || echo "Migration skipped or failed"

# Optimize
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting Nginx and PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
