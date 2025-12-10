<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test household if none exists
        $household = Household::firstOrCreate(
            ['household_number' => 'HH-2023-001'],
            [
                'purok_id' => 1,
                'address' => '123 Test Street, Test Barangay',
                'latitude' => 14.5995,
                'longitude' => 120.9842,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Create test residents
        $residents = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Dela',
                'last_name' => 'Cruz',
                'suffix' => 'Jr',
                'birth_date' => Carbon::now()->subYears(30),
                'gender' => 'Male',
                'civil_status' => 'Single',
                'blood_type' => 'A+',
                'household_id' => $household->id,
                'is_head' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'last_name' => 'Reyes',
                'suffix' => null,
                'birth_date' => Carbon::now()->subYears(28),
                'gender' => 'Female',
                'civil_status' => 'Married',
                'blood_type' => 'B+',
                'household_id' => $household->id,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Reyes',
                'last_name' => 'Cruz',
                'suffix' => null,
                'birth_date' => Carbon::now()->subYears(65),
                'gender' => 'Male',
                'civil_status' => 'Widowed',
                'blood_type' => 'O+',
                'household_id' => $household->id,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Reyes',
                'last_name' => 'Cruz',
                'suffix' => null,
                'birth_date' => Carbon::now()->subYears(70),
                'gender' => 'Female',
                'civil_status' => 'Widowed',
                'blood_type' => 'AB+',
                'household_id' => $household->id,
                'is_head' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($residents as $resident) {
            Resident::firstOrCreate(
                [
                    'first_name' => $resident['first_name'],
                    'last_name' => $resident['last_name'],
                    'birth_date' => $resident['birth_date'],
                ],
                $resident
            );
        }

        $this->command->info('Test residents created successfully!');
    }
}
