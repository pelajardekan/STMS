<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Rental;
use App\Models\Booking;
use App\Models\Payment;

class FixOrphanedInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:orphaned-invoices {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoices that point to missing rentals or bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info("=== FINDING ORPHANED INVOICES ===");
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }
        
        // Find invoices with rental_id that don't have matching rentals
        $orphanedRentalInvoices = Invoice::whereNotNull('rental_id')
            ->whereDoesntHave('rental')
            ->get();
            
        // Find invoices with booking_id that don't have matching bookings
        $orphanedBookingInvoices = Invoice::whereNotNull('booking_id')
            ->whereDoesntHave('booking')
            ->get();
            
        $this->info("Found {$orphanedRentalInvoices->count()} rental invoices with missing rentals");
        $this->info("Found {$orphanedBookingInvoices->count()} booking invoices with missing bookings");
        
        // Show details of orphaned rental invoices
        if ($orphanedRentalInvoices->count() > 0) {
            $this->error("\n--- ORPHANED RENTAL INVOICES ---");
            foreach ($orphanedRentalInvoices as $invoice) {
                $this->line("Invoice ID: {$invoice->invoice_id} → Missing Rental ID: {$invoice->rental_id}");
                $this->line("  Amount: {$invoice->amount}, Status: {$invoice->status}");
                $this->line("  Created: {$invoice->created_at}");
            }
        }
        
        // Show details of orphaned booking invoices
        if ($orphanedBookingInvoices->count() > 0) {
            $this->error("\n--- ORPHANED BOOKING INVOICES ---");
            foreach ($orphanedBookingInvoices as $invoice) {
                $this->line("Invoice ID: {$invoice->invoice_id} → Missing Booking ID: {$invoice->booking_id}");
                $this->line("  Amount: {$invoice->amount}, Status: {$invoice->status}");
                $this->line("  Created: {$invoice->created_at}");
            }
        }
        
        // Show available rentals and bookings
        $availableRentals = Rental::with('rentalRequest.tenant.user')->get();
        $availableBookings = Booking::with('bookingRequest.tenant.user')->get();
        
        $this->info("\n--- AVAILABLE RENTALS ---");
        foreach ($availableRentals as $rental) {
            $tenantName = $rental->tenant() ? $rental->tenant()->name : 'No Tenant';
            $this->line("Rental ID: {$rental->rental_id} → Tenant: {$tenantName}");
        }
        
        $this->info("\n--- AVAILABLE BOOKINGS ---");
        foreach ($availableBookings as $booking) {
            $tenantName = $booking->tenant() ? $booking->tenant()->name : 'No Tenant';
            $this->line("Booking ID: {$booking->booking_id} → Tenant: {$tenantName}");
        }
        
        if (!$dryRun && ($orphanedRentalInvoices->count() > 0 || $orphanedBookingInvoices->count() > 0)) {
            $this->warn("\nTo fix these issues, you have several options:");
            $this->line("1. Delete the orphaned invoices if they're invalid");
            $this->line("2. Reassign them to correct rental/booking IDs");
            $this->line("3. Recreate the missing rental/booking records");
            $this->line("\nRun this command with --dry-run first to see what needs fixing.");
        }

        // Debug payment issues
        $this->info("\n=== PAYMENT DEBUG INFO ===");
        
        // Check all invoices and their statuses
        $allInvoices = Invoice::all();
        $this->info("Total invoices in system: {$allInvoices->count()}");
        
        $invoicesByStatus = $allInvoices->groupBy('status');
        foreach ($invoicesByStatus as $status => $invoices) {
            $this->line("  {$status}: {$invoices->count()} invoices");
        }
        
        // Check which invoices are available for payment
        $payableInvoices = Invoice::with(['rental.rentalRequest.tenant.user', 'booking.bookingRequest.tenant.user'])
            ->whereIn('status', ['pending', 'overdue'])
            ->get();
            
        $this->info("\nInvoices available for payment: {$payableInvoices->count()}");
        foreach ($payableInvoices as $invoice) {
            $tenant = $invoice->tenant();
            $tenantName = $tenant ? $tenant->name : 'No Tenant';
            $this->line("  Invoice #{$invoice->invoice_id} - {$tenantName} - RM{$invoice->amount} ({$invoice->status})");
        }
        
        // Check recent payments
        $recentPayments = Payment::with('invoice')->orderBy('created_at', 'desc')->take(5)->get();
        $this->info("\nRecent payments: {$recentPayments->count()}");
        foreach ($recentPayments as $payment) {
            $this->line("  Payment #{$payment->payment_id} - Invoice #{$payment->invoice_id} - RM{$payment->amount} ({$payment->status})");
        }
        
        return 0;
    }
}
