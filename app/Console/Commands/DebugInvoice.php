<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class DebugInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:invoice {id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug invoice relationships and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceId = $this->argument('id');
        
        $invoice = Invoice::with(['rental.rentalRequest.tenant', 'booking.bookingRequest.tenant'])
            ->find($invoiceId);
            
        if (!$invoice) {
            $this->error("Invoice with ID {$invoiceId} not found");
            return;
        }

        $this->info("=== INVOICE DEBUG INFO ===");
        $this->info("Invoice ID: {$invoice->invoice_id}");
        $this->info("Rental ID: " . ($invoice->rental_id ?? 'NULL'));
        $this->info("Booking ID: " . ($invoice->booking_id ?? 'NULL'));
        $this->info("Type: {$invoice->type}");
        
        $this->info("\n=== RENTAL RELATIONSHIP ===");
        if ($invoice->rental_id) {
            if ($invoice->rental) {
                $this->info("Rental exists: YES");
                $this->info("Rental ID: {$invoice->rental->rental_id}");
                if ($invoice->rental->rentalRequest) {
                    $this->info("Rental Request exists: YES");
                    if ($invoice->rental->rentalRequest->tenant) {
                        $this->info("Tenant exists: YES");
                        $this->info("Tenant Name: {$invoice->rental->rentalRequest->tenant->name}");
                    } else {
                        $this->error("Tenant MISSING");
                    }
                } else {
                    $this->error("Rental Request MISSING");
                }
            } else {
                $this->error("Rental MISSING (relationship broken)");
            }
        } else {
            $this->info("No rental_id set");
        }
        
        $this->info("\n=== BOOKING RELATIONSHIP ===");
        if ($invoice->booking_id) {
            if ($invoice->booking) {
                $this->info("Booking exists: YES");
                $this->info("Booking ID: {$invoice->booking->booking_id}");
                if ($invoice->booking->bookingRequest) {
                    $this->info("Booking Request exists: YES");
                    if ($invoice->booking->bookingRequest->tenant) {
                        $this->info("Tenant exists: YES");
                        $this->info("Tenant Name: {$invoice->booking->bookingRequest->tenant->name}");
                    } else {
                        $this->error("Tenant MISSING");
                    }
                } else {
                    $this->error("Booking Request MISSING");
                }
            } else {
                $this->error("Booking MISSING (relationship broken)");
            }
        } else {
            $this->info("No booking_id set");
        }
        
        $this->info("\n=== TENANT RESOLUTION ===");
        $tenant = $invoice->tenant();
        if ($tenant) {
            $this->info("Resolved Tenant: {$tenant->name}");
        } else {
            $this->error("Could not resolve tenant");
        }
    }
}
