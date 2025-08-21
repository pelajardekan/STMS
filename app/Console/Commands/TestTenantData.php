<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalRequest;
use App\Models\Tenant;

class TestTenantData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:tenant-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test tenant data loading';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== TESTING TENANT DATA ===");
        
        // Test tenants directly
        $this->info("\n--- Direct Tenant Test ---");
        $tenants = Tenant::with('user')->take(3)->get();
        foreach($tenants as $tenant) {
            $this->info("Tenant ID: {$tenant->tenant_id}");
            $this->info("Has User: " . ($tenant->user ? 'YES' : 'NO'));
            if ($tenant->user) {
                $this->info("User Name: {$tenant->user->name}");
                $this->info("User Email: {$tenant->user->email}");
            }
            $this->info("Tenant Name (accessor): " . ($tenant->name ?? 'NULL'));
            $this->info("Tenant Email (accessor): " . ($tenant->email ?? 'NULL'));
            $this->info("---");
        }
        
        // Test rental requests
        $this->info("\n--- Rental Request Test ---");
        $rentalRequests = RentalRequest::with(['tenant.user'])->take(3)->get();
        foreach($rentalRequests as $rr) {
            $this->info("RR ID: {$rr->rental_request_id}");
            $this->info("Tenant ID: " . ($rr->tenant_id ?? 'NULL'));
            $this->info("Has Tenant: " . ($rr->tenant ? 'YES' : 'NO'));
            if ($rr->tenant) {
                $this->info("Tenant Name: " . ($rr->tenant->name ?? 'NULL'));
                $this->info("Tenant Email: " . ($rr->tenant->email ?? 'NULL'));
                $this->info("Has User: " . ($rr->tenant->user ? 'YES' : 'NO'));
            }
            $this->info("---");
        }
    }
}
