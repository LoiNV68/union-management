#!/bin/bash

echo "Bắt đầu chạy Migrations và Seeding..."

# Chạy migrations
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "🚨 Migration thất bại! Dừng lại."
    # Dừng script nếu migration thất bại để kiểm tra log lỗi
    exit 1
fi

# Chạy seeder (chỉ chạy nếu migration thành công)
php artisan db:seed --force
if [ $? -ne 0 ]; then
    echo "🚨 Seeding thất bại! Dừng lại."
    # Dừng script nếu seeding thất bại
    exit 1
fi

echo "Hoàn tất Database. Tiến hành Cache cấu hình..."

# Dọn dẹp và cache cấu hình
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "Khởi động Server Laravel..."
# Khởi động server (lệnh này sẽ chạy mãi mãi)
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}