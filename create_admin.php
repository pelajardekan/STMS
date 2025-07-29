<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if admin user already exists
    $existingAdmin = User::where('email', 'admin@stms.com')->first();
    
    if ($existingAdmin) {
        echo "Admin user already exists!\n";
        echo "Email: admin@stms.com\n";
        echo "Password: admin123\n";
    } else {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@stms.com',
            'phone_number' => '0123456789',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@stms.com\n";
        echo "Password: admin123\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 