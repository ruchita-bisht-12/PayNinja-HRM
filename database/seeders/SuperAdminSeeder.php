<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('super_admins')->insert([
            'name' => 'payninja',
            'email' => 'payninja@info.com',
            'password' => Hash::make('payninja'), // Hash the password
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
