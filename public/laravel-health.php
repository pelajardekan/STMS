<?php
// Simple Laravel health check - no database required
header('Content-Type: text/plain');

try {
    // Check if Laravel bootstrap file exists
    if (!file_exists(__DIR__ . '/../bootstrap/app.php')) {
        throw new Exception('Laravel bootstrap file not found');
    }
    
    // Check if vendor directory exists
    if (!is_dir(__DIR__ . '/../vendor')) {
        throw new Exception('Vendor directory not found');
    }
    
    // Check if storage directory is writable
    if (!is_writable(__DIR__ . '/../storage')) {
        throw new Exception('Storage directory not writable');
    }
    
    echo "Laravel application is healthy\n";
    echo "PHP version: " . PHP_VERSION . "\n";
    echo "Memory usage: " . memory_get_usage(true) . " bytes\n";
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Laravel health check failed: " . $e->getMessage() . "\n";
}
?> 