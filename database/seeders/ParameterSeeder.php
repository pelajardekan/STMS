<?php

namespace Database\Seeders;

use App\Models\Pricing;
use App\Models\Amenity;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample pricing data
        Pricing::create([
            'name' => 'Standard Rental',
            'pricing_type' => 'rental',
            'price_amount' => 1200.00,
            'duration_type' => 'monthly',
            'discount' => 0,
            'notes' => '-',
        ]);

        Pricing::create([
            'name' => 'Hourly Booking',
            'pricing_type' => 'booking',
            'price_amount' => 100.00,
            'duration_type' => 'hourly',
            'discount' => 10,
            'notes' => 'If booking for 8 hours, get 10% discount',
        ]);

        // Create sample amenity data
        Amenity::create([
            'name' => 'Swimming Pool',
            'description' => 'Olympic size pool available for all tenants',
        ]);

        Amenity::create([
            'name' => 'Gym',
            'description' => '24/7 access to gym facilities',
        ]);

        // Create sample service data
        Service::create([
            'name' => 'Cleaning',
            'description' => 'Weekly cleaning service for all units',
        ]);

        Service::create([
            'name' => 'Laundry',
            'description' => 'On-demand laundry service',
        ]);
    }
} 