<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NewUserSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete existing users
        DB::table('users')->delete();

        // Reset auto-increment
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1;');

        // Create Admin account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@barangaypangi.com',
            'password' => Hash::make('admin123'),
            'role' => 'secretary',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Staff account
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@barangaypangi.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
