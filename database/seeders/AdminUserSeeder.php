<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin pjp berjaya',
            'email' => 'adminpjp@gmail.com',
            'phone_number' => '012345678',
            'role' => 'admin',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
        ]);
    }
}
