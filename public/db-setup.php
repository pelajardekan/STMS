<?php
// Simple database setup script for STMS
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>STMS Database Setup</h1>";

// Include Laravel's autoloader
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "<p style='color: green;'>✓ Autoloader loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Failed to load autoloader: " . $e->getMessage() . "</p>";
    exit(1);
}

// Load Laravel app
try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "<p style='color: green;'>✓ Laravel app loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Failed to load Laravel app: " . $e->getMessage() . "</p>";
    exit(1);
}

// Test database connection
try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    echo "<p>Host: " . getenv('DB_HOST') . "</p>";
    echo "<p>Database: " . getenv('DB_DATABASE') . "</p>";
    echo "<p>Username: " . getenv('DB_USERNAME') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit(1);
}

// Run migrations
echo "<h2>Running Database Migrations</h2>";
try {
    $output = shell_exec('cd /var/www/html && php artisan migrate --force 2>&1');
    echo "<pre>$output</pre>";
    echo "<p style='color: green;'>✓ Migrations completed</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>";
}

// Run seeders
echo "<h2>Running Database Seeders</h2>";
try {
    $output = shell_exec('cd /var/www/html && php artisan db:seed --force 2>&1');
    echo "<pre>$output</pre>";
    echo "<p style='color: green;'>✓ Seeders completed</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Seeding failed: " . $e->getMessage() . "</p>";
}

// Check for admin user
echo "<h2>Checking Admin User</h2>";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute(['adminpjp@gmail.com']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Admin user exists (adminpjp@gmail.com)</p>";
        echo "<p><strong>Login credentials:</strong></p>";
        echo "<p>Email: adminpjp@gmail.com</p>";
        echo "<p>Password: 12345678</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Admin user not found, creating manually...</p>";
        
        // Create admin user manually
        $hashedPassword = password_hash('12345678', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
        $stmt->execute(['admin pjp berjaya', 'adminpjp@gmail.com', $hashedPassword]);
        
        echo "<p style='color: green;'>✓ Admin user created successfully</p>";
        echo "<p><strong>Login credentials:</strong></p>";
        echo "<p>Email: adminpjp@gmail.com</p>";
        echo "<p>Password: 12345678</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Admin user check/creation failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Setup Complete!</h2>";
echo "<p><a href='/'>Go to Application</a></p>";
?>
