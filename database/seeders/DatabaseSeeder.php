<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════╗');
        $this->command->info('║   BARANGAY MATINA PANGI INFORMATION SYSTEM SEEDER    ║');
        $this->command->info('╚════════════════════════════════════════════════════════╝');
        $this->command->info('');

        $this->command->info('🌱 Creating user accounts and purok addresses...');
        $this->command->info('📝 Puroks will be available for both resident profiling and calamity management!');
        $this->command->info('');

        // Seed core users and puroks first
        $this->call([
            UserSeeder::class,
            PurokSeeder::class,
        ]);

        // Seed Residents & Households
        if (class_exists(\Database\Seeders\HouseholdAndResidentSeeder::class)) {
            $this->call([\Database\Seeders\HouseholdAndResidentSeeder::class]);
        }
        if (class_exists(\Database\Seeders\ExtendedFamilySeeder::class)) {
            $this->call([\Database\Seeders\ExtendedFamilySeeder::class]);
        } elseif (class_exists(\Database\Seeders\SimpleExtendedFamilySeeder::class)) {
            $this->call([\Database\Seeders\SimpleExtendedFamilySeeder::class]);
        }

        // Seed Calamity Management
        if (class_exists(\Database\Seeders\CalamityModuleSeeder::class)) {
            $this->call([\Database\Seeders\CalamityModuleSeeder::class]);
        }

        // Seed additional sample data across modules
        if (class_exists(\Database\Seeders\NewModulesSampleDataSeeder::class)) {
            $this->call([\Database\Seeders\NewModulesSampleDataSeeder::class]);
        }
        if (class_exists(\Database\Seeders\CleanSampleDataSeeder::class)) {
            try {
                $this->call([\Database\Seeders\CleanSampleDataSeeder::class]);
            } catch (\Throwable $e) {
                $this->command->warn('⚠ CleanSampleDataSeeder failed: '.$e->getMessage());
                if (class_exists(\Database\Seeders\SimpleSampleDataSeeder::class)) {
                    $this->command->info('➡ Falling back to SimpleSampleDataSeeder...');
                    $this->call([\Database\Seeders\SimpleSampleDataSeeder::class]);
                }
            }
        } elseif (class_exists(\Database\Seeders\SimpleSampleDataSeeder::class)) {
            $this->call([\Database\Seeders\SimpleSampleDataSeeder::class]);
        }

        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════════════════════╗');
        $this->command->info('║    PUROKS CREATED - READY FOR PROFILING & CALAMITY   ║');
        $this->command->info('╚════════════════════════════════════════════════════════╝');
        $this->command->info('');
        $this->command->info('📊 DATABASE SUMMARY:');
        $this->command->info('   • Users: '.\App\Models\User::count());
        $this->command->info('   • Puroks: '.\App\Models\Purok::count());
        $this->command->info('   • Households: '.\App\Models\Household::count());
        $this->command->info('   • Sub-Families: '.\App\Models\SubFamily::count());
        $this->command->info('   • Residents: '.\App\Models\Resident::count());
        $this->command->info('   • Calamities: '.(class_exists('App\\Models\\Calamity') ? \App\Models\Calamity::count() : 0));
        $this->command->info('   • Certificates: '.(class_exists('App\\Models\\Certificate') ? \App\Models\Certificate::count() : 0));
        $this->command->info('');
        $this->command->info('🎯 NEXT STEPS:');
        $this->command->info('   1. Use the 10 puroks for resident addresses');
        $this->command->info('   2. Profile residents and households');
        $this->command->info('   3. Manage calamity operations using same puroks');

        $this->command->info('');
        $this->command->info('🔐 LOGIN CREDENTIALS:');
        $this->command->info('   Secretary: secretary@pangi.gov / password');
        $this->command->info('   Staff 1: maria.santos@pangi.gov / password');
        $this->command->info('   Staff 2: juan.delacruz@pangi.gov / password');
        $this->command->info('');
        $this->command->info('🚀 You can now test the system at: http://127.0.0.1:8000');
        $this->command->info('');
    }
}
