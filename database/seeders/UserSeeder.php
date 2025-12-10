<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Secretary account (full access)
        if (! User::where('email', 'secretary@pangi.gov')->exists()) {
            User::create([
                'name' => 'Barangay Secretary',
                'email' => 'secretary@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'secretary',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Secretary account created');
        } else {
            $this->command->warn('⚠ Secretary account already exists');
        }

        if (! User::where('email', 'calamityhead@pangi.gov')->exists()) {
            $roleCalHead = DB::getDriverName() === 'sqlite' ? 'staff' : 'calamity_head';
            User::create([
                'name' => 'Calamity Head',
                'email' => 'calamityhead@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => $roleCalHead,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Calamity Head account created');
        } else {
            $this->command->warn('⚠ Calamity Head account already exists');
        }

        // Create Staff accounts (limited access)
        if (! User::where('email', 'maria.santos@pangi.gov')->exists()) {
            User::create([
                'name' => 'Maria Santos',
                'email' => 'maria.santos@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'staff',
                'assigned_app' => 'profiling_only',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Staff account (Maria) created');
        } else {
            $this->command->warn('⚠ Maria Santos account already exists');
        }

        if (! User::where('email', 'juan.delacruz@pangi.gov')->exists()) {
            User::create([
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'staff',
                'assigned_app' => 'profiling_only',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Staff account (Juan) created');
        } else {
            $this->command->warn('⚠ Juan Dela Cruz account already exists');
        }

        $this->command->info('✓ Users seeded successfully!');
    }
}
