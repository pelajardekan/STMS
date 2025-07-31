<?php
// Database setup trigger endpoint
// This can be called via web request to manually run migrations and seeders

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

header('Content-Type: text/plain');

try {
    echo "=== STMS Database Setup ===\n";
    echo "Starting database migrations and seeding...\n\n";
    
    // Check database connection first
    echo "1. Testing database connection...\n";
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection successful!\n\n";
    
    // Run migrations
    echo "2. Running database migrations...\n";
    $exitCode = Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    if ($exitCode === 0) {
        echo "✅ Migrations completed successfully!\n\n";
        
        // Check if users exist
        echo "3. Checking existing users...\n";
        $userCount = \App\Models\User::count();
        echo "Current user count: $userCount\n";
        
        if ($userCount === 0) {
            echo "4. Running database seeders...\n";
            $exitCode = Artisan::call('db:seed', ['--force' => true]);
            echo Artisan::output();
            
            if ($exitCode === 0) {
                echo "✅ Seeders completed successfully!\n\n";
                echo "=== SETUP COMPLETE ===\n";
                echo "Admin credentials:\n";
                echo "Email: adminpjp@gmail.com\n";
                echo "Password: 12345678\n";
            } else {
                echo "❌ Seeding failed!\n";
            }
        } else {
            echo "ℹ️ Users already exist, skipping seeding.\n\n";
            echo "=== SETUP COMPLETE ===\n";
            echo "Admin credentials:\n";
            echo "Email: adminpjp@gmail.com\n";  
            echo "Password: 12345678\n";
        }
    } else {
        echo "❌ Migration failed!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
