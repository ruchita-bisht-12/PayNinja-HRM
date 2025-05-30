<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if superadmin already exists
        if (!User::where('email', 'info@payninja.in')->exists()) {
            User::create([
                'name' => 'PayNinja',
                'email' => 'info@payninja.in',
                'password' => Hash::make('Payninja'), // Default password is 'password'
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Super Admin user created successfully!');
            $this->command->warn('Email: info@payninja.in');
            $this->command->warn('Password: Payninja');
        } else {
            $this->command->info('Super Admin user already exists.');
        }
    }
}
