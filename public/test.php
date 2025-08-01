<?php
echo "Basic PHP test: " . date('Y-m-d H:i:s') . "\n";
echo "Environment variables:\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_DATABASE: " . getenv('DB_DATABASE') . "\n"; 
echo "APP_KEY: " . (getenv('APP_KEY') ? 'Set' : 'Not set') . "\n";
echo "APP_ENV: " . getenv('APP_ENV') . "\n";
?>
