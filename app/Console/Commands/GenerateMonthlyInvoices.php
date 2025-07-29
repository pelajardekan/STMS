<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use App\Models\Invoice;
use Carbon\Carbon;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly invoices for active rentals';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly invoice generation...');

        $activeRentals = Rental::with(['rentalRequest.unit.propertyUnitParameters.pricing'])
            ->where('status', 'active')
            ->where('end_date', '>=', Carbon::today())
            ->get();

        $count = 0;
        foreach ($activeRentals as $rental) {
            // Check if invoice for this month already exists
            $existingInvoice = Invoice::where('rental_id', $rental->rental_id)
                ->whereMonth('issue_date', Carbon::now()->month)
                ->whereYear('issue_date', Carbon::now()->year)
                ->first();

            if (!$existingInvoice) {
                // Get pricing information
                $pricing = $rental->rentalRequest->unit->propertyUnitParameters()
                    ->whereNotNull('pricing_id')
                    ->first();

                if ($pricing && $pricing->pricing) {
                    // Calculate monthly rate
                    $monthlyRate = $this->calculateMonthlyRate($pricing->pricing);
                    
                    // Create monthly invoice
                    $invoice = new Invoice();
                    $invoice->rental_id = $rental->rental_id;
                    $invoice->amount = $monthlyRate;
                    $invoice->issue_date = Carbon::now();
                    $invoice->due_date = Carbon::now()->addWeek(); // 1 week due date
                    $invoice->status = 'unpaid';
                    $invoice->save();
                    
                    $count++;
                }
            }
        }

        $this->info("Generated {$count} new monthly invoice(s).");
        
        return Command::SUCCESS;
    }

    /**
     * Calculate monthly rate from pricing
     */
    private function calculateMonthlyRate($pricing)
    {
        // Use base monthly rate if available, otherwise calculate from yearly rate
        if ($pricing->base_monthly_rate) {
            return $pricing->base_monthly_rate;
        } elseif ($pricing->base_yearly_rate) {
            return $pricing->base_yearly_rate / 12;
        } else {
            return $pricing->price_amount; // Fallback to base price
        }
    }
} 