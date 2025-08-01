#!/bin/bash

# Azure Container Apps Migration Job
# This script can be run as a separate container job for database migrations

set -e

echo "🚀 Starting STMS Database Migration Job..."
echo "Timestamp: $(date)"

# Environment variables check
if [ -z "$DB_HOST" ]; then
    echo "❌ DB_HOST environment variable is required"
    exit 1
fi

if [ -z "$DB_USERNAME" ]; then
    echo "❌ DB_USERNAME environment variable is required"
    exit 1
fi

if [ -z "$DB_PASSWORD" ]; then
    echo "❌ DB_PASSWORD environment variable is required"
    exit 1
fi

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
max_attempts=60
attempt=1

while [ $attempt -le $max_attempts ]; do
    if mysql -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; then
        echo "✅ Database connection successful!"
        break
    else
        echo "🔄 Database connection attempt $attempt/$max_attempts failed, waiting 10 seconds..."
        sleep 10
        attempt=$((attempt + 1))
    fi
done

if [ $attempt -gt $max_attempts ]; then
    echo "❌ Failed to connect to database after $max_attempts attempts"
    exit 1
fi

# Change to Laravel directory
cd /var/www/html

# Generate Laravel application key if not set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating Laravel application key..."
    php artisan key:generate --force
fi

# Clear any stale caches
echo "🧹 Clearing Laravel caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run database migrations
echo "📊 Running database migrations..."
php artisan migrate --force
migration_exit_code=$?

if [ $migration_exit_code -eq 0 ]; then
    echo "✅ Migrations completed successfully"
    
    # Run database seeders
    echo "🌱 Running database seeders..."
    php artisan db:seed --force --class=AdminUserSeeder
    seeder_exit_code=$?
    
    if [ $seeder_exit_code -eq 0 ]; then
        echo "✅ Seeders completed successfully"
    else
        echo "⚠️ Seeder completed with exit code: $seeder_exit_code (may be normal if admin user already exists)"
    fi
    
    # Optimize Laravel for production
    echo "⚡ Optimizing Laravel application..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "🎉 Database migration job completed successfully!"
else
    echo "❌ Migration failed with exit code: $migration_exit_code"
    exit $migration_exit_code
fi
