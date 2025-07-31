<?php
// Database setup script
echo "STMS Database Setup\n";
echo "===================\n\n";

// Set environment variables
putenv("DB_HOST=stms-mysql-server.mysql.database.azure.com");
putenv("DB_PORT=3306");
putenv("DB_DATABASE=stms_db");
putenv("DB_USERNAME=stmsadmin");
putenv("DB_PASSWORD=STMSSecure123!");
putenv("APP_ENV=production");
putenv("APP_KEY=base64:lYoXEYoNb7tNYcwSHjCgOVuXKuiJWJgRHtHaGqGjUzk=");

// Test database connection first
echo "Testing database connection...\n";
try {
    $pdo = new PDO(
        "mysql:host=stms-mysql-server.mysql.database.azure.com;port=3306;dbname=stms_db",
        "stmsadmin",
        "STMSSecure123!"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Run migrations
echo "Running database migrations...\n";
$output = shell_exec('php artisan migrate --force 2>&1');
echo $output . "\n";

// Run seeders
echo "Running database seeders...\n";
$output = shell_exec('php artisan db:seed --force 2>&1');
echo $output . "\n";

// Create admin user manually
echo "Creating admin user...\n";
try {
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute(['adminpjp@gmail.com']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "✓ Admin user already exists (adminpjp@gmail.com)\n";
    } else {
        // Create admin user
        $hashedPassword = password_hash('12345678', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
        $stmt->execute(['admin pjp berjaya', 'adminpjp@gmail.com', $hashedPassword]);
        echo "✓ Admin user created successfully\n";
    }
    
    echo "\nLogin Credentials:\n";
    echo "Email: adminpjp@gmail.com\n";
    echo "Password: 12345678\n";
    
} catch (Exception $e) {
    echo "✗ Admin user creation failed: " . $e->getMessage() . "\n";
}

echo "\nDatabase setup complete!\n";
?>
