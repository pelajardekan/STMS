<?php
// Database connection test script
try {
    $host = $_ENV['DB_HOST'] ?? 'stms-mysql-server.mysql.database.azure.com';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $database = $_ENV['DB_DATABASE'] ?? 'stms';
    $username = $_ENV['DB_USERNAME'] ?? 'stmsadmin';
    $password = $_ENV['DB_PASSWORD'] ?? 'Stms@2024!';
    
    echo "<h1>Database Connection Test</h1>\n";
    echo "<p>Host: $host</p>\n";
    echo "<p>Port: $port</p>\n";
    echo "<p>Database: $database</p>\n";
    echo "<p>Username: $username</p>\n";
    echo "<p>Password: " . (strlen($password) > 0 ? str_repeat('*', strlen($password)) : 'EMPTY') . "</p>\n";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
    
    echo "<p style='color: green;'>✅ Database connection successful!</p>\n";
    
    // Test basic query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "<p>MySQL Version: " . $result['version'] . "</p>\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $usersTable = $stmt->fetch();
    if ($usersTable) {
        echo "<p style='color: green;'>✅ Users table exists</p>\n";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<p>Users count: " . $result['count'] . "</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Users table does not exist - migrations need to be run</p>\n";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>\n";
    echo "<p>Error Code: " . $e->getCode() . "</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ General error: " . $e->getMessage() . "</p>\n";
}
?>
