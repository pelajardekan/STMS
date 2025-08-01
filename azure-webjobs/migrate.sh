#!/bin/bash
echo "STMS Migration WebJob Starting..."
cd /home/site/wwwroot
echo "Current directory: $(pwd)"
echo "Running Laravel migrations..."
php artisan migrate --force
echo "Migration exit code: $?"
echo "Running Laravel seeders..."
php artisan db:seed --force  
echo "Seeding exit code: $?"
echo "STMS Migration WebJob Completed!"
