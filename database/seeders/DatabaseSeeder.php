<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user directly without factory to avoid Faker dependency
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'user',
            ]
        );

        // Call AdminUserSeeder to create admin user
        $this->call([
            AdminUserSeeder::class,
            ParameterSeeder::class,
            PropertySeeder::class,
            UnitSeeder::class,
        ]);
    }
}
