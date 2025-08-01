<?php
// Simple Laravel database connection test
require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test database connection
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful!\n";
    echo "Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "\n";
    
    // Test a simple query
    $result = DB::select('SELECT 1 as test');
    echo "✅ Database query successful: " . json_encode($result) . "\n";
    
    // Test connecting to the specific database
    DB::select('USE stms');
    echo "✅ Successfully connected to STMS database!\n";
    
    // Show tables
    $tables = DB::select('SHOW TABLES');
    echo "📋 Tables in database: " . count($tables) . "\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - $tableName\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    
    // Print environment variables for debugging
    echo "\n🔍 Database Configuration:\n";
    echo "DB_HOST: " . env('DB_HOST', 'not set') . "\n";
    echo "DB_PORT: " . env('DB_PORT', 'not set') . "\n";
    echo "DB_DATABASE: " . env('DB_DATABASE', 'not set') . "\n";
    echo "DB_USERNAME: " . env('DB_USERNAME', 'not set') . "\n";
    echo "DB_CONNECTION: " . env('DB_CONNECTION', 'not set') . "\n";
    echo "MYSQL_ATTR_SSL_CA: " . env('MYSQL_ATTR_SSL_CA', 'not set') . "\n";
    echo "DB_SSLMODE: " . env('DB_SSLMODE', 'not set') . "\n";
}
