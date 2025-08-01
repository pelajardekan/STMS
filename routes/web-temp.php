<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

// Database setup and test routes for Azure deployment
Route::get('/setup-database', function () {
    try {
        echo "<h2>🚀 STMS Database Setup</h2>";
        echo "<pre>";
        
        // Test database connection
        echo "📋 Testing database connection...\n";
        DB::connection()->getPdo();
        echo "✅ Database connection successful!\n\n";
        
        // Check migration status
        echo "📋 Checking migration status...\n";
        Artisan::call('migrate:status');
        echo Artisan::output() . "\n";
        
        // Run migrations
        echo "🔄 Running migrations...\n";
        Artisan::call('migrate', ['--force' => true]);
        echo Artisan::output() . "\n";
        
        // Run seeders
        echo "🌱 Running seeders...\n";
        Artisan::call('db:seed', ['--force' => true]);
        echo Artisan::output() . "\n";
        
        echo "✅ Database setup completed successfully!\n";
        echo "</pre>";
        
    } catch (\Exception $e) {
        echo "<h2>❌ Database Setup Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
    }
});

Route::get('/test-db', function () {
    try {
        echo "<h2>🧪 Database Connection Test</h2>";
        echo "<pre>";
        
        // Test connection
        DB::connection()->getPdo();
        echo "✅ Database connection successful!\n";
        
        // Show database info
        $result = DB::select('SELECT DATABASE() as db_name, USER() as db_user, VERSION() as db_version');
        echo "Database: " . $result[0]->db_name . "\n";
        echo "User: " . $result[0]->db_user . "\n";
        echo "Version: " . $result[0]->db_version . "\n";
        
        // Show tables
        $tables = DB::select('SHOW TABLES');
        echo "\nTables (" . count($tables) . "):\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- " . $tableName . "\n";
        }
        
        echo "</pre>";
    } catch (\Exception $e) {
        echo "<h2>❌ Database Connection Error</h2>";
        echo "<pre style='color: red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "</pre>";
    }
});
