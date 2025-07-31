<?php
// Simple database setup for STMS
header('Content-Type: text/html; charset=utf-8');
echo "<h1>STMS Database Setup</h1>";

// Set time limit
set_time_limit(300);

// Change to application directory
chdir('/var/www/html');

echo "<h2>Running Database Migrations</h2>";
echo "<pre>";
$output = shell_exec('php artisan migrate --force 2>&1');
echo htmlspecialchars($output);
echo "</pre>";

echo "<h2>Running Database Seeders</h2>";
echo "<pre>";
$output = shell_exec('php artisan db:seed --force 2>&1');
echo htmlspecialchars($output);
echo "</pre>";

echo "<h2>Creating Admin User (Alternative)</h2>";
echo "<pre>";
$output = shell_exec('php create_admin.php 2>&1');
echo htmlspecialchars($output);
echo "</pre>";

echo "<h2>Manual Admin User Creation</h2>";
try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
    $stmt->execute(['adminpjp@gmail.com']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "<p style='color: green;'>✓ Admin user exists (adminpjp@gmail.com)</p>";
    } else {
        // Create admin user
        $hashedPassword = password_hash('12345678', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
        $stmt->execute(['admin pjp berjaya', 'adminpjp@gmail.com', $hashedPassword]);
        echo "<p style='color: green;'>✓ Admin user created successfully</p>";
    }
    
    echo "<p><strong>Login Credentials:</strong></p>";
    echo "<p>Email: adminpjp@gmail.com</p>";
    echo "<p>Password: 12345678</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error with manual admin creation: " . $e->getMessage() . "</p>";
}

echo "<h2>Setup Complete!</h2>";
echo "<p><a href='/'>Go to Application</a></p>";
?>
