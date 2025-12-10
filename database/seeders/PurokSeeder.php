<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Purok;
use App\Models\Resident;
use App\Models\SubFamily;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PurokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puroksData = [
            [
                'purok_name' => 'Purok 1',
                'purok_code' => 'P1',
                'leader' => [
                    'first_name' => 'Roberto',
                    'middle_name' => 'Santos',
                    'last_name' => 'Martinez',
                    'suffix' => null,
                    'sex' => 'Male',
                    'date_of_birth' => '1975-03-15',
                    'contact_number' => '09171234567',
                    'email' => 'roberto.martinez@example.com',
                ],
                'description' => 'Located at the northern part of the barangay',
                'boundaries' => 'North: National Highway, South: Purok 2, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 2',
                'purok_code' => 'P2',
                'leader' => [
                    'first_name' => 'Elena',
                    'middle_name' => 'Cruz',
                    'last_name' => 'Reyes',
                    'suffix' => null,
                    'sex' => 'Female',
                    'date_of_birth' => '1978-07-22',
                    'contact_number' => '09181234567',
                    'email' => 'elena.reyes@example.com',
                ],
                'description' => 'Central area near the barangay hall',
                'boundaries' => 'North: Purok 1, South: Purok 3, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 3',
                'purok_code' => 'P3',
                'leader' => [
                    'first_name' => 'Carlos',
                    'middle_name' => 'Ramos',
                    'last_name' => 'Villanueva',
                    'suffix' => null,
                    'sex' => 'Male',
                    'date_of_birth' => '1972-11-08',
                    'contact_number' => '09191234567',
                    'email' => 'carlos.villanueva@example.com',
                ],
                'description' => 'Near the elementary school',
                'boundaries' => 'North: Purok 2, South: Purok 4, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 4',
                'purok_code' => 'P4',
                'leader' => [
                    'first_name' => 'Luisa',
                    'middle_name' => 'Mendoza',
                    'last_name' => 'Garcia',
                    'suffix' => null,
                    'sex' => 'Female',
                    'date_of_birth' => '1980-05-14',
                    'contact_number' => '09201234567',
                    'email' => 'luisa.garcia@example.com',
                ],
                'description' => 'Residential area with many young families',
                'boundaries' => 'North: Purok 3, South: Purok 5, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 5',
                'purok_code' => 'P5',
                'leader' => [
                    'first_name' => 'Fernando',
                    'middle_name' => 'Dela Cruz',
                    'last_name' => 'Santos',
                    'suffix' => null,
                    'sex' => 'Male',
                    'date_of_birth' => '1976-09-30',
                    'contact_number' => '09211234567',
                    'email' => 'fernando.santos@example.com',
                ],
                'description' => 'Agricultural area with rice fields',
                'boundaries' => 'North: Purok 4, South: Purok 6, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 6',
                'purok_code' => 'P6',
                'leader' => [
                    'first_name' => 'Rosa',
                    'middle_name' => 'Lopez',
                    'last_name' => 'Mendoza',
                    'suffix' => null,
                    'sex' => 'Female',
                    'date_of_birth' => '1974-02-18',
                    'contact_number' => '09221234567',
                    'email' => 'rosa.mendoza@example.com',
                ],
                'description' => 'Near the basketball court and chapel',
                'boundaries' => 'North: Purok 5, South: Purok 7, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 7',
                'purok_code' => 'P7',
                'leader' => [
                    'first_name' => 'Antonio',
                    'middle_name' => 'Perez',
                    'last_name' => 'Cruz',
                    'suffix' => null,
                    'sex' => 'Male',
                    'date_of_birth' => '1973-12-25',
                    'contact_number' => '09231234567',
                    'email' => 'antonio.cruz@example.com',
                ],
                'description' => 'Hillside area with scenic views',
                'boundaries' => 'North: Purok 6, South: Purok 8, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 8',
                'purok_code' => 'P8',
                'leader' => [
                    'first_name' => 'Gloria',
                    'middle_name' => 'Torres',
                    'last_name' => 'Ramos',
                    'suffix' => null,
                    'sex' => 'Female',
                    'date_of_birth' => '1979-06-10',
                    'contact_number' => '09241234567',
                    'email' => 'gloria.ramos@example.com',
                ],
                'description' => 'Near the health center',
                'boundaries' => 'North: Purok 7, South: Purok 9, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 9',
                'purok_code' => 'P9',
                'leader' => [
                    'first_name' => 'Miguel',
                    'middle_name' => 'Aquino',
                    'last_name' => 'Torres',
                    'suffix' => null,
                    'sex' => 'Male',
                    'date_of_birth' => '1977-04-05',
                    'contact_number' => '09251234567',
                    'email' => 'miguel.torres@example.com',
                ],
                'description' => 'Commercial area with small businesses',
                'boundaries' => 'North: Purok 8, South: Purok 10, East: River, West: Mountain',
            ],
            [
                'purok_name' => 'Purok 10',
                'purok_code' => 'P10',
                'leader' => [
                    'first_name' => 'Carmen',
                    'middle_name' => 'Bautista',
                    'last_name' => 'Flores',
                    'suffix' => null,
                    'sex' => 'Female',
                    'date_of_birth' => '1981-08-20',
                    'contact_number' => '09261234567',
                    'email' => 'carmen.flores@example.com',
                ],
                'description' => 'Southern boundary near the neighboring barangay',
                'boundaries' => 'North: Purok 9, South: Barangay Boundary, East: River, West: Mountain',
            ],
        ];

        foreach ($puroksData as $purokData) {
            // Create the purok first without leader info
            $purok = Purok::firstOrCreate(
                ['purok_code' => $purokData['purok_code']],
                [
                    'purok_name' => $purokData['purok_name'],
                    'purok_code' => $purokData['purok_code'],
                    'description' => $purokData['description'],
                    'boundaries' => $purokData['boundaries'],
                    'total_households' => 0,
                    'total_population' => 0,
                ]
            );

            // Create household for the leader (skip if already exists)
            $household = Household::firstOrCreate(
                ['household_id' => 'HH-'.$purokData['purok_code'].'-001'],
                [
                    'purok_id' => $purok->id,
                    'address' => 'Km'.$purokData['purok_code'].' Matina Pangi, Davao City',
                    'purok' => $purokData['purok_name'],
                    'housing_type' => 'owned',
                    'has_electricity' => true,
                    'total_members' => 1,
                    'household_type' => 'family',
                ]
            );

            // Create the leader as a resident (skip if already exists)
            $leaderData = $purokData['leader'];
            $birthdate = Carbon::parse($leaderData['date_of_birth']);
            $age = $birthdate->age;

            $resident = Resident::firstOrCreate(
                ['resident_id' => 'RES-'.$purokData['purok_code'].'-001'],
                [
                    'household_id' => $household->id,
                    'first_name' => $leaderData['first_name'],
                    'middle_name' => $leaderData['middle_name'],
                    'last_name' => $leaderData['last_name'],
                    'suffix' => $leaderData['suffix'],
                    'sex' => strtolower($leaderData['sex']),
                    'birthdate' => $leaderData['date_of_birth'],
                    'age' => $age,
                    'place_of_birth' => 'Davao City',
                    'civil_status' => 'married',
                    'nationality' => 'Filipino',
                    'religion' => 'Roman Catholic',
                    'contact_number' => $leaderData['contact_number'],
                    'email' => $leaderData['email'],
                    'household_role' => 'head',
                    'is_household_head' => true,
                    'is_voter' => true,
                    'is_pwd' => false,
                    'is_senior_citizen' => $age >= 60,
                    'is_teen' => false,
                    'is_4ps_beneficiary' => false,
                ]
            );

            // Create primary subfamily for this household
            $subfamily = SubFamily::firstOrCreate(
                [
                    'household_id' => $household->id,
                    'is_primary_family' => true,
                ],
                [
                    'sub_family_name' => 'Primary Family',
                    'sub_head_resident_id' => $resident->id,
                ]
            );

            // Update household with head information (not needed as we set is_household_head)
            // Note: household head is tracked via is_household_head field in residents table

            // Update purok with leader information and counts
            $fullName = trim($leaderData['first_name'].' '.($leaderData['middle_name'] ? substr($leaderData['middle_name'], 0, 1).'. ' : '').$leaderData['last_name']);
            $purok->update([
                'purok_leader_name' => $fullName,
                'purok_leader_contact' => $leaderData['contact_number'],
                'total_households' => 1,
                'total_population' => 1,
            ]);
        }

        $this->command->info('✓ 10 Puroks with leaders seeded successfully!');
        $this->command->info('✓ 10 Households created for purok leaders');
        $this->command->info('✓ 10 Residents (purok leaders) created');
    }
}
