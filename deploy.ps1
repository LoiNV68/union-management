Write-Host "🚀 Starting Deployment..." -ForegroundColor Green

# 1. Build Docker Images
Write-Host "📦 Building Docker Images..." -ForegroundColor Cyan
docker-compose build --no-cache

# 2. Start Services
Write-Host "🔥 Starting Services..." -ForegroundColor Cyan
docker-compose up -d

# 3. Wait for Database
Write-Host "⏳ Waiting for Database to be healthy..." -ForegroundColor Cyan
$retries = 30
while ($retries -gt 0) {
    $status = docker inspect --format="{{json .State.Health.Status}}" manage-members-db
    if ($status -like '*healthy*') {
        Write-Host "✅ Database is ready!" -ForegroundColor Green
        break
    }
    Write-Host "Waiting for database... ($retries)" -ForegroundColor Yellow
    Start-Sleep -Seconds 2
    $retries--
}

if ($retries -eq 0) {
    Write-Host "❌ Database failed to start in time." -ForegroundColor Red
    exit 1
}

# 4. Run Migrations
Write-Host "🗄️ Running Migrations..." -ForegroundColor Cyan
docker-compose exec app php artisan migrate --force

# 5. Clear Caches & Optimize
Write-Host "🧹 Optimizing..." -ForegroundColor Cyan
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan optimize

# 6. Set Permissions
Write-Host "🔒 Setting Permissions..." -ForegroundColor Cyan
docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

Write-Host "✅ Deployment Complete!" -ForegroundColor Green
