#!/bin/bash

# Kiểm tra kết nối và chạy Migration
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "🚨 LỖI QUAN TRỌNG: Migration thất bại. Kiểm tra biến môi trường DB."
    exit 1
fi

# Chạy Seeder
php artisan db:seed --force
if [ $? -ne 0 ]; then
    echo "🚨 LỖI QUAN TRỌNG: Seeding thất bại. Kiểm tra Seeder và kết nối DB."
    exit 1
fi

# Chạy các lệnh khác và khởi động server
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}