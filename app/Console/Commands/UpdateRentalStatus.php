<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Carbon\Carbon;

class UpdateRentalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update rental status to past when rental duration ends';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting rental status update...');

        $expiredRentals = Rental::where('status', 'active')
            ->where('end_date', '<', Carbon::today())
            ->get();

        $count = 0;
        foreach ($expiredRentals as $rental) {
            $rental->update(['status' => 'past']);
            $count++;
        }

        $this->info("Updated {$count} rental(s) to past status.");
        
        return Command::SUCCESS;
    }
} 