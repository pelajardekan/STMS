<?php
// Admin database setup and testing endpoint
// Only works if accessed with correct token

if (!isset($_GET['token']) || $_GET['token'] !== 'setup-admin-db-2024') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Check if this is a login test request
if (isset($_GET['action']) && $_GET['action'] === 'test_login') {
    $email = $_GET['email'] ?? 'admin@stms.com';
    $password = $_GET['password'] ?? 'password';
    
    try {
        $pdo = new PDO(
            'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_DATABASE'],
            $_ENV['DB_USERNAME'],
            $_ENV['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $passwordCheck = password_verify($password, $user['password']);
            echo json_encode([
                'user_found' => true,
                'user_data' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'password_hash' => substr($user['password'], 0, 20) . '...',
                'password_verify' => $passwordCheck,
                'password_length' => strlen($user['password'])
            ]);
        } else {
            echo json_encode(['user_found' => false, 'message' => 'No user found with that email']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

$baseDir = dirname(__DIR__);
require_once $baseDir . '/vendor/autoload.php';

// Load Laravel application
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$output = [];
$errors = [];

try {
    // Test database connection first
    $output[] = "Testing database connection...";
    
    // Run migrations
    $output[] = "Running migrations...";
    $exitCode = $kernel->call('migrate', ['--force' => true]);
    $output[] = "Migrations exit code: " . $exitCode;
    
    // Run seeders
    $output[] = "Running seeders...";
    $exitCode = $kernel->call('db:seed', ['--force' => true]);
    $output[] = "Seeders exit code: " . $exitCode;
    
    // Check if admin user exists
    $output[] = "Checking for admin user...";
    
    // Try to create admin user directly
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        $output[] = "Users table exists";
        
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR email = ?");
        $stmt->execute(['admin@stms.com', 'adminpjp@gmail.com']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            $output[] = "Admin user found: " . $adminUser['email'];
        } else {
            $output[] = "No admin user found, creating one...";
            
            // Create admin user
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, email_verified_at, password, created_at, updated_at) VALUES (?, ?, NOW(), ?, NOW(), NOW())");
            $stmt->execute(['Admin User', 'admin@stms.com', $hashedPassword]);
            $output[] = "Admin user created successfully";
        }
    } else {
        $errors[] = "Users table does not exist";
    }
    
} catch (Exception $e) {
    $errors[] = "Error: " . $e->getMessage();
    $output[] = get_class($e) . ": " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode([
    'success' => empty($errors),
    'output' => $output,
    'errors' => $errors,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
