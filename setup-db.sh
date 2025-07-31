#!/bin/bash
set -e

echo "=== Manual Database Setup ==="
echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

echo "=== Setup Complete ==="
echo "Admin credentials:"
echo "Email: adminpjp@gmail.com"
echo "Password: 12345678"
