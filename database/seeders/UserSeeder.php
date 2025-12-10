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
        // Secretary account (create or update password)
        $secretary = User::where('email', 'secretary@pangi.gov')->first();
        if (!$secretary) {
            User::create([
                'name' => 'Barangay Secretary',
                'email' => 'secretary@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'secretary',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Secretary account created');
        } else {
            $secretary->update([
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'secretary',
                'email_verified_at' => $secretary->email_verified_at ?? now(),
            ]);
            $this->command->info('✓ Secretary account password updated');
        }

        $roleCalHead = DB::getDriverName() === 'sqlite' ? 'staff' : 'calamity_head';
        $calHead = User::where('email', 'calamityhead@pangi.gov')->first();
        if (!$calHead) {
            User::create([
                'name' => 'Calamity Head',
                'email' => 'calamityhead@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('kwatrolangsir444'),
                'role' => $roleCalHead,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Calamity Head account created');
        } else {
            $calHead->update([
                'password' => Hash::make('kwatrolangsir444'),
                'role' => $roleCalHead,
                'email_verified_at' => $calHead->email_verified_at ?? now(),
            ]);
            $this->command->info('✓ Calamity Head account password updated');
        }

        // Staff accounts (create or update password)
        $maria = User::where('email', 'maria.santos@pangi.gov')->first();
        if (!$maria) {
            User::create([
                'name' => 'Maria Santos',
                'email' => 'maria.santos@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'staff',
                'assigned_app' => 'profiling_only',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Staff account (Maria) created');
        } else {
            $maria->update([
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'staff',
                'assigned_app' => $maria->assigned_app ?? 'profiling_only',
                'email_verified_at' => $maria->email_verified_at ?? now(),
            ]);
            $this->command->info('✓ Staff account (Maria) password updated');
        }

        $juan = User::where('email', 'juan.delacruz@pangi.gov')->first();
        if (!$juan) {
            User::create([
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@pangi.gov',
                'email_verified_at' => now(),
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'staff',
                'assigned_app' => 'profiling_only',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('✓ Staff account (Juan) created');
        } else {
            $juan->update([
                'password' => Hash::make('kwatrolangsir444'),
                'role' => 'staff',
                'assigned_app' => $juan->assigned_app ?? 'profiling_only',
                'email_verified_at' => $juan->email_verified_at ?? now(),
            ]);
            $this->command->info('✓ Staff account (Juan) password updated');
        }

        $this->command->info('✓ Users seeded successfully!');
    }
}
