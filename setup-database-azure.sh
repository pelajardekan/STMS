#!/bin/bash

# Standalone database setup script for Azure deployment
echo "STMS Database Setup Script"
echo "=========================="
echo "This script will setup the database for the STMS application"

# Database connection variables
DB_HOST="stms-mysql-server.mysql.database.azure.com"
DB_PORT="3306"
DB_DATABASE="stms"
DB_USERNAME="stmsadmin"
DB_PASSWORD="STMSSecure123!"

echo "Testing database connection..."

# Test database connection using mysql client
mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -e "SELECT 1 as connected;"

if [ $? -eq 0 ]; then
    echo "✓ Database connection successful"
else
    echo "✗ Database connection failed"
    exit 1
fi

echo ""
echo "Running database migrations..."

# Create the database if it doesn't exist
mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_DATABASE;"

echo "Checking if users table exists..."
USERS_TABLE_EXISTS=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -D $DB_DATABASE -e "SHOW TABLES LIKE 'users';" | wc -l)

if [ $USERS_TABLE_EXISTS -eq 0 ]; then
    echo "Users table does not exist. Database needs to be migrated."
    echo "Please run the Laravel migrations from the container:"
    echo "php artisan migrate --force"
    echo "php artisan db:seed --force"
else
    echo "✓ Users table exists"
    
    # Check if admin user exists
    echo "Checking for admin user..."
    ADMIN_EXISTS=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -D $DB_DATABASE -e "SELECT COUNT(*) FROM users WHERE email IN ('admin@stms.com', 'adminpjp@gmail.com');" | tail -n 1)
    
    if [ $ADMIN_EXISTS -eq 0 ]; then
        echo "No admin user found. Creating admin user..."
        
        # Create admin user with hashed password (password: admin123)
        HASHED_PASSWORD='$2y$12$LKS2E2lVjQGgY4sWJhpXJ.sHT8XCOkYU7KHU6IU4fT9XWQp/4d1OC'
        
        mysql -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD -D $DB_DATABASE -e "
        INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at)
        VALUES ('Admin User', 'admin@stms.com', NOW(), '$HASHED_PASSWORD', NOW(), NOW())
        ON DUPLICATE KEY UPDATE
        password = '$HASHED_PASSWORD',
        updated_at = NOW();
        "
        
        if [ $? -eq 0 ]; then
            echo "✓ Admin user created successfully"
            echo "Admin credentials:"
            echo "  Email: admin@stms.com"
            echo "  Password: admin123"
        else
            echo "✗ Failed to create admin user"
        fi
    else
        echo "✓ Admin user already exists"
    fi
fi

echo ""
echo "Database setup completed!"
echo "You should now be able to login to the application at:"
echo "https://stms-app.azurewebsites.net/"
