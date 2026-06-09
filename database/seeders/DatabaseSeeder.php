<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder                                                                    
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user for login (password is "password" by default in Laravel Breeze)
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Varsha Manager',
                'email' => 'test@example.com',
            ]);
        }
    }
}
