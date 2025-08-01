#!/bin/bash

# STMS Local Testing and Fix Script
# This script helps diagnose and fix common issues

set -e

echo "🔧 STMS Application Diagnostic and Fix Script"
echo "=============================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

echo "✅ Found Laravel artisan - we're in the right directory"

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found. Creating from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ Created .env file"
    else
        echo "❌ .env.example not found. Creating basic .env..."
        cat > .env << EOL
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
EOL
        echo "✅ Created basic .env file"
    fi
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated"
fi

# Clear caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Caches cleared"

# Check and create required directories
echo "📁 Checking required directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
echo "✅ Required directories created"

# Set proper permissions
echo "🔒 Setting proper permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache
echo "✅ Permissions set"

# Install/update dependencies if needed
if [ -f "composer.json" ]; then
    echo "📦 Checking PHP dependencies..."
    if [ ! -d "vendor" ]; then
        echo "Installing Composer dependencies..."
        composer install
    else
        echo "✅ Composer dependencies already installed"
    fi
fi

if [ -f "package.json" ]; then
    echo "📦 Checking Node.js dependencies..."
    if [ ! -d "node_modules" ]; then
        echo "Installing NPM dependencies..."
        npm install
    else
        echo "✅ NPM dependencies already installed"
    fi
    
    echo "🏗️  Building frontend assets..."
    npm run build
    echo "✅ Frontend assets built"
fi

# Test Laravel application
echo "🧪 Testing Laravel application..."
if php artisan --version >/dev/null 2>&1; then
    echo "✅ Laravel artisan is working"
    php artisan --version
else
    echo "❌ Laravel artisan is not working"
    exit 1
fi

# Check database connection (optional)
if grep -q "DB_HOST=" .env && grep -q "DB_DATABASE=" .env; then
    echo "🗄️  Testing database connection..."
    if php artisan migrate:status >/dev/null 2>&1; then
        echo "✅ Database connection successful"
        echo "📊 Running migrations..."
        php artisan migrate --force
        echo "🌱 Running seeders..."
        php artisan db:seed --force --class=AdminUserSeeder
    else
        echo "⚠️  Database connection failed - continuing without database operations"
    fi
fi

# Optimize for local development
echo "⚡ Optimizing for local development..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Application optimized"

echo ""
echo "🎉 STMS Application is ready!"
echo ""
echo "📋 Next steps:"
echo "1. Start your local development server:"
echo "   php artisan serve --host=0.0.0.0 --port=8080"
echo ""
echo "2. Or use Docker:"
echo "   docker-compose up -d"
echo ""
echo "3. Access your application at:"
echo "   http://localhost:8080"
echo ""
echo "4. Default admin credentials:"
echo "   Email: adminpjp@gmail.com"
echo "   Password: 12345678"
echo ""
echo "5. To test Azure deployment, run:"
echo "   ./azure-deploy-containerapp.sh  (Linux/macOS)"
echo "   ./azure-deploy-containerapp.ps1 (Windows PowerShell)"
