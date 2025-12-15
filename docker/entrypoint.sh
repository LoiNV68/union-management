#!/bin/bash
set -e

echo "🚀 Starting application..."

# Function to check database connection
wait_for_db() {
    echo "⏳ Waiting for database connection..."
    max_attempts=30
    attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        if php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'connected'; } catch (Exception \$e) { echo 'failed'; }" 2>/dev/null | grep -q "connected"; then
            echo "✅ Database is ready!"
            return 0
        fi
        
        attempt=$((attempt + 1))
        echo "Attempt $attempt/$max_attempts - Database not ready, waiting..."
        sleep 2
    done
    
    echo "❌ Database connection failed after $max_attempts attempts"
    return 1
}

# Wait for database
if wait_for_db; then
    # Run migrations
    echo "🗄️ Running migrations..."
    php artisan migrate --force
    
    # Optimize
    echo "🧹 Optimizing..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Start supervisord
echo "🔥 Starting Nginx and PHP-FPM..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
