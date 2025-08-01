# STMS Local Testing and Fix Script (PowerShell)
# This script helps diagnose and fix common issues

$ErrorActionPreference = "Stop"

Write-Host "🔧 STMS Application Diagnostic and Fix Script" -ForegroundColor Green
Write-Host "=============================================="

# Check if we're in the right directory
if (!(Test-Path "artisan")) {
    Write-Host "❌ Error: artisan file not found. Please run this script from the Laravel project root." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found Laravel artisan - we're in the right directory" -ForegroundColor Green

# Check if .env exists
if (!(Test-Path ".env")) {
    Write-Host "⚠️  .env file not found. Creating from .env.example..." -ForegroundColor Yellow
    if (Test-Path ".env.example") {
        Copy-Item ".env.example" ".env"
        Write-Host "✅ Created .env file" -ForegroundColor Green
    } else {
        Write-Host "❌ .env.example not found. Creating basic .env..." -ForegroundColor Yellow
        @"
APP_NAME=STMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stms
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log
"@ | Out-File -FilePath ".env" -Encoding UTF8
        Write-Host "✅ Created basic .env file" -ForegroundColor Green
    }
}

# Generate application key if not set
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
    php artisan key:generate
    Write-Host "✅ Application key generated" -ForegroundColor Green
}

# Clear caches
Write-Host "🧹 Clearing Laravel caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "✅ Caches cleared" -ForegroundColor Green

# Check and create required directories
Write-Host "📁 Checking required directories..." -ForegroundColor Yellow
$directories = @(
    "storage\logs",
    "storage\framework\cache",
    "storage\framework\sessions",
    "storage\framework\views",
    "bootstrap\cache"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -Path $dir -ItemType Directory -Force | Out-Null
    }
}
Write-Host "✅ Required directories created" -ForegroundColor Green

# Install/update dependencies if needed
if (Test-Path "composer.json") {
    Write-Host "📦 Checking PHP dependencies..." -ForegroundColor Yellow
    if (!(Test-Path "vendor")) {
        Write-Host "Installing Composer dependencies..." -ForegroundColor Yellow
        composer install
    } else {
        Write-Host "✅ Composer dependencies already installed" -ForegroundColor Green
    }
}

if (Test-Path "package.json") {
    Write-Host "📦 Checking Node.js dependencies..." -ForegroundColor Yellow
    if (!(Test-Path "node_modules")) {
        Write-Host "Installing NPM dependencies..." -ForegroundColor Yellow
        npm install
    } else {
        Write-Host "✅ NPM dependencies already installed" -ForegroundColor Green
    }
    
    Write-Host "🏗️  Building frontend assets..." -ForegroundColor Yellow
    npm run build
    Write-Host "✅ Frontend assets built" -ForegroundColor Green
}

# Test Laravel application
Write-Host "🧪 Testing Laravel application..." -ForegroundColor Yellow
try {
    $version = php artisan --version
    Write-Host "✅ Laravel artisan is working" -ForegroundColor Green
    Write-Host $version -ForegroundColor Cyan
} catch {
    Write-Host "❌ Laravel artisan is not working" -ForegroundColor Red
    exit 1
}

# Check database connection (optional)
$envContent = Get-Content ".env" -Raw
if ($envContent -match "DB_HOST=" -and $envContent -match "DB_DATABASE=") {
    Write-Host "🗄️  Testing database connection..." -ForegroundColor Yellow
    try {
        php artisan migrate:status | Out-Null
        Write-Host "✅ Database connection successful" -ForegroundColor Green
        Write-Host "📊 Running migrations..." -ForegroundColor Yellow
        php artisan migrate --force
        Write-Host "🌱 Running seeders..." -ForegroundColor Yellow
        php artisan db:seed --force --class=AdminUserSeeder
    } catch {
        Write-Host "⚠️  Database connection failed - continuing without database operations" -ForegroundColor Yellow
    }
}

# Optimize for local development
Write-Host "⚡ Optimizing for local development..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache
Write-Host "✅ Application optimized" -ForegroundColor Green

Write-Host ""
Write-Host "🎉 STMS Application is ready!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next steps:" -ForegroundColor Cyan
Write-Host "1. Start your local development server:" -ForegroundColor White
Write-Host "   php artisan serve --host=0.0.0.0 --port=8080" -ForegroundColor Yellow
Write-Host ""
Write-Host "2. Or use Docker:" -ForegroundColor White
Write-Host "   docker-compose up -d" -ForegroundColor Yellow
Write-Host ""
Write-Host "3. Access your application at:" -ForegroundColor White
Write-Host "   http://localhost:8080" -ForegroundColor Yellow
Write-Host ""
Write-Host "4. Default admin credentials:" -ForegroundColor White
Write-Host "   Email: adminpjp@gmail.com" -ForegroundColor Yellow
Write-Host "   Password: 12345678" -ForegroundColor Yellow
Write-Host ""
Write-Host "5. To test Azure deployment, run:" -ForegroundColor White
Write-Host "   .\azure-deploy-containerapp.ps1" -ForegroundColor Yellow
