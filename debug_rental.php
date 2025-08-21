<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Rental;
use App\Models\Invoice;

echo "=== DEBUGGING RENTAL ID 3 ===\n";

// Check if rental 3 exists
$rental = Rental::find(3);
if ($rental) {
    echo "✅ Rental 3 exists: {$rental->rental_id}\n";
    
    // Check rentalRequest relationship
    $rentalRequest = $rental->rentalRequest;
    if ($rentalRequest) {
        echo "✅ Has rentalRequest: {$rentalRequest->rental_request_id}\n";
        
        // Check tenant relationship
        $tenant = $rentalRequest->tenant;
        if ($tenant) {
            echo "✅ Has tenant: {$tenant->tenant_id}\n";
            
            // Check user relationship
            $user = $tenant->user;
            if ($user) {
                echo "✅ Has user: {$user->name} ({$user->email})\n";
            } else {
                echo "❌ No user found for tenant {$tenant->tenant_id}\n";
            }
        } else {
            echo "❌ No tenant found for rental request {$rentalRequest->rental_request_id}\n";
        }
    } else {
        echo "❌ No rental request found for rental {$rental->rental_id}\n";
        echo "rental_request_id field: {$rental->rental_request_id}\n";
    }
    
    // Test the tenant() method
    echo "\n=== TESTING TENANT() METHOD ===\n";
    $tenantFromMethod = $rental->tenant();
    if ($tenantFromMethod) {
        echo "✅ tenant() method works: {$tenantFromMethod->name} ({$tenantFromMethod->email})\n";
    } else {
        echo "❌ tenant() method returned null\n";
    }
    
} else {
    echo "❌ Rental 3 NOT FOUND\n";
}

// Check invoices pointing to rental 3
echo "\n=== CHECKING INVOICES FOR RENTAL 3 ===\n";
$invoices = Invoice::where('rental_id', 3)->get();
foreach ($invoices as $invoice) {
    echo "Invoice {$invoice->invoice_id}: ";
    $tenant = $invoice->tenant();
    if ($tenant) {
        echo "✅ Tenant: {$tenant->name}\n";
    } else {
        echo "❌ No tenant found\n";
        
        // Debug the relationship chain
        echo "  - rental_id: {$invoice->rental_id}\n";
        if ($invoice->rental) {
            echo "  - rental exists: {$invoice->rental->rental_id}\n";
            if ($invoice->rental->rentalRequest) {
                echo "  - rentalRequest exists: {$invoice->rental->rentalRequest->rental_request_id}\n";
                if ($invoice->rental->rentalRequest->tenant) {
                    echo "  - tenant exists: {$invoice->rental->rentalRequest->tenant->tenant_id}\n";
                    if ($invoice->rental->rentalRequest->tenant->user) {
                        echo "  - user exists: {$invoice->rental->rentalRequest->tenant->user->name}\n";
                    } else {
                        echo "  - user MISSING\n";
                    }
                } else {
                    echo "  - tenant MISSING\n";
                }
            } else {
                echo "  - rentalRequest MISSING\n";
            }
        } else {
            echo "  - rental MISSING\n";
        }
    }
}

echo "\n=== DONE ===\n";
