<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ThirdPartyApiKey;
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
            User::create([
                'name' => 'Varsha Srivastava',
                'email' => 'test@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'manager',
                'email_verified_at' => now(),
            ]);
        }

        // Seed default Ollama configuration for all users
        foreach (User::all() as $user) {
            if (!ThirdPartyApiKey::where('user_id', $user->id)->where('service_name', 'ollama')->exists()) {
                ThirdPartyApiKey::create([
                    'user_id' => $user->id,
                    'service_name' => 'ollama',
                    'api_key' => 'none',
                    'api_url' => 'http://127.0.0.1:11434',
                    'model_name' => 'llama3.1:8b',
                    'is_active' => true,
                ]);
            }
        }
    }
}
