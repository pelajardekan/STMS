<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        Property::updateOrCreate(
            ['property_code' => 'PROP001'],
            [
                'property_code' => 'PROP001',
                'name' => 'Main Building',
                'address' => '123 Main Street, Kuala Lumpur',
                'city' => 'Kuala Lumpur',
                'state' => 'Selangor',
                'postal_code' => '50000',
                'country' => 'Malaysia',
                'description' => 'Main residential and commercial building',
                'status' => 'active',
            ]
        );

        Property::updateOrCreate(
            ['property_code' => 'PROP002'],
            [
                'property_code' => 'PROP002',
                'name' => 'Office Tower',
                'address' => '456 Business Avenue, Kuala Lumpur',
                'city' => 'Kuala Lumpur',
                'state' => 'Selangor',
                'postal_code' => '50100',
                'country' => 'Malaysia',
                'description' => 'Modern office building with meeting rooms',
                'status' => 'active',
            ]
        );

        $this->command->info('Properties created successfully.');
    }
}
