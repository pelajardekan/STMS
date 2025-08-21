<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Invoice;

class DebugRental extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:rental {id=3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug rental relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rentalId = $this->argument('id');
        
        $this->info("=== DEBUGGING RENTAL ID {$rentalId} ===");

        // Check if rental exists
        $rental = Rental::find($rentalId);
        if ($rental) {
            $this->info("✅ Rental {$rentalId} exists: {$rental->rental_id}");
            
            // Check rentalRequest relationship
            $rentalRequest = $rental->rentalRequest;
            if ($rentalRequest) {
                $this->info("✅ Has rentalRequest: {$rentalRequest->rental_request_id}");
                
                // Check tenant relationship
                $tenant = $rentalRequest->tenant;
                if ($tenant) {
                    $this->info("✅ Has tenant: {$tenant->tenant_id}");
                    
                    // Check user relationship
                    $user = $tenant->user;
                    if ($user) {
                        $this->info("✅ Has user: {$user->name} ({$user->email})");
                    } else {
                        $this->error("❌ No user found for tenant {$tenant->tenant_id}");
                        $this->line("   user_id field: {$tenant->user_id}");
                    }
                } else {
                    $this->error("❌ No tenant found for rental request {$rentalRequest->rental_request_id}");
                    $this->line("   tenant_id field: {$rentalRequest->tenant_id}");
                }
            } else {
                $this->error("❌ No rental request found for rental {$rental->rental_id}");
                $this->line("   rental_request_id field: {$rental->rental_request_id}");
            }
            
            // Test the tenant() method
            $this->info("\n=== TESTING TENANT() METHOD ===");
            $tenantFromMethod = $rental->tenant();
            if ($tenantFromMethod) {
                $this->info("✅ tenant() method works: {$tenantFromMethod->name} ({$tenantFromMethod->email})");
            } else {
                $this->error("❌ tenant() method returned null");
            }
            
        } else {
            $this->error("❌ Rental {$rentalId} NOT FOUND");
        }

        // Check invoices pointing to this rental
        $this->info("\n=== CHECKING INVOICES FOR RENTAL {$rentalId} ===");
        $invoices = Invoice::where('rental_id', $rentalId)->get();
        
        if ($invoices->count() > 0) {
            foreach ($invoices as $invoice) {
                $this->line("Invoice {$invoice->invoice_id}: ");
                $tenant = $invoice->tenant();
                if ($tenant) {
                    $this->info("  ✅ Tenant: {$tenant->name}");
                } else {
                    $this->error("  ❌ No tenant found");
                    
                    // Debug the relationship chain
                    $this->line("    - rental_id: {$invoice->rental_id}");
                    if ($invoice->rental) {
                        $this->line("    - rental exists: {$invoice->rental->rental_id}");
                        if ($invoice->rental->rentalRequest) {
                            $this->line("    - rentalRequest exists: {$invoice->rental->rentalRequest->rental_request_id}");
                            if ($invoice->rental->rentalRequest->tenant) {
                                $this->line("    - tenant exists: {$invoice->rental->rentalRequest->tenant->tenant_id}");
                                if ($invoice->rental->rentalRequest->tenant->user) {
                                    $this->line("    - user exists: {$invoice->rental->rentalRequest->tenant->user->name}");
                                } else {
                                    $this->error("    - user MISSING");
                                }
                            } else {
                                $this->error("    - tenant MISSING");
                            }
                        } else {
                            $this->error("    - rentalRequest MISSING");
                        }
                    } else {
                        $this->error("    - rental MISSING");
                    }
                }
            }
        } else {
            $this->line("No invoices found for rental {$rentalId}");
        }

        return 0;
    }
}
