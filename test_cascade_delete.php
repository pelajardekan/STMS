<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Tenant;

echo "Testing Cascade Delete Functionality\n";
echo "====================================\n\n";

try {
    // Test 1: Create a user with tenant profile
    echo "1. Creating test user with tenant profile...\n";
    $user = User::create([
        'name' => 'Test User for Delete',
        'email' => 'testdelete@example.com',
        'phone_number' => '0123456789',
        'role' => 'tenant',
        'password' => bcrypt('password123'),
    ]);
    
    $tenant = Tenant::create([
        'user_id' => $user->id,
        'IC_number' => '900101-01-1234',
        'address' => 'Test Address',
        'emergency_contact' => '0123456788',
        'additional_info' => 'Test info',
    ]);
    
    echo "   ✓ User created with ID: {$user->id}\n";
    echo "   ✓ Tenant created with ID: {$tenant->id}\n";
    
    // Test 2: Verify relationship
    echo "\n2. Verifying relationship...\n";
    $userReloaded = User::find($user->id);
    $tenantReloaded = Tenant::find($tenant->id);
    
    echo "   ✓ User has tenant: " . ($userReloaded->tenant ? 'Yes' : 'No') . "\n";
    echo "   ✓ Tenant belongs to user: " . ($tenantReloaded->user_id == $user->id ? 'Yes' : 'No') . "\n";
    
    // Test 3: Delete user and check cascade
    echo "\n3. Deleting user (should cascade delete tenant)...\n";
    $user->delete();
    
    echo "   ✓ User deleted\n";
    
    // Test 4: Verify tenant is also deleted
    echo "\n4. Verifying tenant is also deleted...\n";
    $tenantStillExists = Tenant::find($tenant->id);
    $userStillExists = User::find($user->id);
    
    echo "   ✓ Tenant still exists: " . ($tenantStillExists ? 'Yes' : 'No') . "\n";
    echo "   ✓ User still exists: " . ($userStillExists ? 'Yes' : 'No') . "\n";
    
    if (!$tenantStillExists && !$userStillExists) {
        echo "\n🎉 SUCCESS: Cascade delete is working correctly!\n";
    } else {
        echo "\n❌ FAILED: Cascade delete is not working properly!\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n"; 