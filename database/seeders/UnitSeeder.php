<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Property;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $properties = Property::all();

        foreach ($properties as $property) {
            // Create rental units
            for ($i = 1; $i <= 3; $i++) {
                Unit::create([
                    'property_id' => $property->property_id,
                    'name' => "Rental Unit {$i}",
                    'type' => 'apartment',
                    'status' => 'active',
                    'description' => "Rental unit {$i} in {$property->name}",
                    'leasing_type' => 'rental',
                    'availability' => 'available',
                ]);
            }

            // Create booking units
            for ($i = 1; $i <= 2; $i++) {
                Unit::create([
                    'property_id' => $property->property_id,
                    'name' => "Booking Unit {$i}",
                    'type' => 'meeting_room',
                    'status' => 'active',
                    'description' => "Booking unit {$i} in {$property->name}",
                    'leasing_type' => 'booking',
                    'availability' => 'available', // Always available for booking units
                ]);
            }

            // Create some unavailable units
            Unit::create([
                'property_id' => $property->property_id,
                'name' => "Occupied Rental Unit",
                'type' => 'apartment',
                'status' => 'active',
                'description' => "Currently occupied rental unit in {$property->name}",
                'leasing_type' => 'rental',
                'availability' => 'not_available',
            ]);
        }
    }
} 