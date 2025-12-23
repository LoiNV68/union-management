#!/bin/bash

# Kiểm tra và tạo APP_KEY nếu chưa có hoặc có nhiều dòng
echo "🔑 Đang kiểm tra APP_KEY..."
APP_KEY_COUNT=$(grep -c "^APP_KEY=" .env 2>/dev/null || echo "0")

# Nếu không có APP_KEY hoặc có nhiều hơn 1 dòng APP_KEY
if [ "$APP_KEY_COUNT" -ne 1 ]; then
    echo "📝 Đang sửa APP_KEY (phát hiện $APP_KEY_COUNT dòng)..."
    # Xóa tất cả dòng APP_KEY cũ
    grep -v "^APP_KEY=" .env > .env.tmp && mv .env.tmp .env
    # Tạo key mới
    php artisan key:generate --force
    echo "✅ APP_KEY đã được tạo thành công"
fi

# Kiểm tra kết nối và chạy Migration
echo "📦 Đang chạy migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "🚨 LỖI QUAN TRỌNG: Migration thất bại. Kiểm tra biến môi trường DB."
    exit 1
fi

# Chạy Seeder
echo "🌱 Đang chạy seeders..."
php artisan db:seed --force
if [ $? -ne 0 ]; then
    echo "🚨 LỖI QUAN TRỌNG: Seeding thất bại. Kiểm tra Seeder và kết nối DB."
    exit 1
fi

# Kiểm tra và build Vite assets nếu cần
if [ ! -f "public/build/manifest.json" ]; then
    echo "📦 Đang kiểm tra npm dependencies..."
    if [ ! -d "node_modules" ]; then
        echo "📥 Đang cài đặt npm packages..."
        npm install
    fi
    echo "🔨 Đang build Vite assets..."
    npm run build
fi

# Chạy các lệnh khác và khởi động server
echo "🧹 Đang clear cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "✅ Khởi động server tại http://0.0.0.0:${PORT:-8080}"
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}