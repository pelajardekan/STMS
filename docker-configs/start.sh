#!/bin/bash
set -e

echo "🚀 Starting STMS Laravel Application..."

# Fix permissions for persistent storage after Azure File Share mount
if [ -d "/var/www/html/storage" ]; then
    echo "📁 Setting storage permissions..."
    chown -R www-data:www-data /var/www/html/storage
    chmod -R 775 /var/www/html/storage
fi

if [ -d "/var/www/html/bootstrap/cache" ]; then
    echo "📁 Setting bootstrap cache permissions..."
    chown -R www-data:www-data /var/www/html/bootstrap/cache
    chmod -R 775 /var/www/html/bootstrap/cache
fi

# Critical: Clear Laravel configuration cache to pick up Azure App Service environment variables
echo "🔧 Clearing Laravel configuration cache for Azure App Service env vars..."
php /var/www/html/artisan config:clear || echo "Config clear failed (this is OK on first run)"
php /var/www/html/artisan cache:clear || echo "Cache clear failed (this is OK on first run)"
php /var/www/html/artisan route:clear || echo "Route clear failed (this is OK on first run)"
php /var/www/html/artisan view:clear || echo "View clear failed (this is OK on first run)"

# Re-cache for optimal performance (this picks up fresh environment variables)
echo "⚡ Optimizing Laravel for production..."
php /var/www/html/artisan config:cache || echo "Config cache failed"
php /var/www/html/artisan route:cache || echo "Route cache failed"
php /var/www/html/artisan view:cache || echo "View cache failed"

# Test configurations
echo "🧪 Testing Nginx configuration..."
nginx -t || exit 1

echo "🧪 Testing PHP-FPM configuration..."
php-fpm -t || exit 1

echo "✅ STMS Application ready!"
echo "🌐 Starting web server and PHP-FPM via supervisord..."

# Start supervisord in foreground - this will handle nginx, php-fpm, and ssh
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
