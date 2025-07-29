<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Console\Command;

class FixTenantProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:tenant-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tenant profiles for existing tenant users who do not have profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for tenant users without profiles...');

        $usersWithoutProfiles = User::where('role', 'tenant')
            ->whereDoesntHave('tenant')
            ->get();

        if ($usersWithoutProfiles->isEmpty()) {
            $this->info('All tenant users already have profiles!');
            return 0;
        }

        $this->info("Found {$usersWithoutProfiles->count()} tenant users without profiles.");

        $bar = $this->output->createProgressBar($usersWithoutProfiles->count());
        $bar->start();

        foreach ($usersWithoutProfiles as $user) {
            Tenant::create([
                'user_id' => $user->id,
                'IC_number' => null,
                'address' => null,
                'emergency_contact' => null,
                'additional_info' => null,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Successfully created tenant profiles for all users!');

        return 0;
    }
} 