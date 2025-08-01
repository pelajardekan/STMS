<?php
// Diagnostic script for STMS application
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>STMS Application Diagnostics</h1>";
echo "<h2>Environment Check</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";

// Check if Laravel files exist
$checks = [
    'Bootstrap file' => __DIR__ . '/../bootstrap/app.php',
    'Vendor autoload' => __DIR__ . '/../vendor/autoload.php',
    'Storage directory' => __DIR__ . '/../storage',
    'Bootstrap cache' => __DIR__ . '/../bootstrap/cache',
    '.env file' => __DIR__ . '/../.env',
];

echo "<h3>File System Checks</h3>";
foreach ($checks as $name => $path) {
    $exists = file_exists($path);
    $writable = $exists && is_writable($path);
    $status = $exists ? ($writable ? 'OK (writable)' : 'OK (read-only)') : 'MISSING';
    $color = $exists ? ($writable ? 'green' : 'orange') : 'red';
    echo "<p><strong>$name:</strong> <span style='color: $color'>$status</span> ($path)</p>";
}

// Check environment variables
echo "<h3>Environment Variables</h3>";
$env_vars = [
    'APP_NAME', 'APP_ENV', 'APP_KEY', 'APP_DEBUG', 'APP_URL',
    'DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'
];

foreach ($env_vars as $var) {
    $value = getenv($var) ?: $_ENV[$var] ?? 'NOT SET';
    if ($var === 'DB_PASSWORD') {
        $value = $value !== 'NOT SET' ? '***HIDDEN***' : 'NOT SET';
    }
    $color = $value !== 'NOT SET' ? 'green' : 'red';
    echo "<p><strong>$var:</strong> <span style='color: $color'>$value</span></p>";
}

// Try to load Laravel application
echo "<h3>Laravel Application Test</h3>";
try {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        echo "<p style='color: green'>✓ Autoloader loaded successfully</p>";
        
        if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            echo "<p style='color: green'>✓ Laravel application bootstrapped</p>";
            
            // Test database connection if configured
            if (getenv('DB_HOST') && getenv('DB_USERNAME')) {
                try {
                    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                    $kernel->bootstrap();
                    
                    $pdo = new PDO(
                        'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
                        getenv('DB_USERNAME'),
                        getenv('DB_PASSWORD')
                    );
                    echo "<p style='color: green'>✓ Database connection successful</p>";
                } catch (Exception $e) {
                    echo "<p style='color: red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: orange'>⚠ Database not configured</p>";
            }
        } else {
            echo "<p style='color: red'>✗ Laravel bootstrap file missing</p>";
        }
    } else {
        echo "<p style='color: red'>✗ Composer autoload file missing</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red'>✗ Error loading Laravel: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Check permissions
echo "<h3>Permission Checks</h3>";
$permission_checks = [
    'storage' => __DIR__ . '/../storage',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
    'public' => __DIR__,
];

foreach ($permission_checks as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path);
        $color = $writable ? 'green' : 'red';
        echo "<p><strong>$name:</strong> <span style='color: $color'>$perms " . ($writable ? '(writable)' : '(not writable)') . "</span></p>";
    } else {
        echo "<p><strong>$name:</strong> <span style='color: red'>Directory missing</span></p>";
    }
}

echo "<hr>";
echo "<p><em>Generated at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
