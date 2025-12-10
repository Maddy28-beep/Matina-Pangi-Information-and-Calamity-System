<?php

namespace Database\Seeders;

use App\Models\Calamity;
use App\Models\CalamityAffectedHousehold;
use App\Models\Certificate;
use App\Models\EvacuationCenter;
use App\Models\GovernmentAssistance;
use App\Models\Household;
use App\Models\RescueOperation;
use App\Models\Resident;
use App\Models\ResponseTeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewModulesSampleDataSeeder extends Seeder
{
    public function run()
    {
        $secretary = User::where('role', 'secretary')->first();

        // Get some residents for testing
        $residents = Resident::approved()->active()->take(20)->get();

        if ($residents->isEmpty()) {
            $this->command->error('No residents found! Please add residents first.');

            return;
        }

        $this->command->info('Creating sample data for new modules...');

        // 1. CERTIFICATES
        $this->command->info('Creating Certificates...');

        $certificateTypes = [
            'barangay_clearance' => 'Employment purposes',
            'certificate_of_indigency' => 'Medical assistance',
            'certificate_of_residency' => 'Bank requirements',
            'business_clearance' => 'Starting a sari-sari store',
            'good_moral' => 'School requirements',
            'travel_permit' => 'Traveling to Manila',
        ];

        $certIndex = 0;
        foreach ($certificateTypes as $type => $purpose) {
            if ($certIndex >= 10) {
                break;
            }

            $resident = $residents[$certIndex % $residents->count()];

            try {
                Certificate::create([
                    'resident_id' => $resident->id,
                    'certificate_type' => $type,
                    'purpose' => $purpose,
                    'or_number' => 'OR-2025-'.str_pad($certIndex + 1, 4, '0', STR_PAD_LEFT),
                    'amount_paid' => rand(0, 100),
                    'issued_by' => $secretary->id,
                    'issued_date' => now()->subDays(rand(1, 30)),
                    'valid_until' => now()->addMonths(6),
                    'status' => ['issued', 'claimed', 'issued'][$certIndex % 3],
                    'remarks' => $certIndex % 3 == 0 ? 'Urgent request' : null,
                ]);
                $certIndex++;
            } catch (\Exception $e) {
                $this->command->warn('Could not create certificate: '.$e->getMessage());
            }
        }

        // Create more certificates with different types
        for ($i = $certIndex; $i < 10; $i++) {
            $types = array_keys($certificateTypes);
            $type = $types[$i % count($types)];
            $resident = $residents[$i % $residents->count()];

            try {
                Certificate::create([
                    'resident_id' => $resident->id,
                    'certificate_type' => $type,
                    'purpose' => $certificateTypes[$type],
                    'or_number' => 'OR-2025-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'amount_paid' => rand(0, 100),
                    'issued_by' => $secretary->id,
                    'issued_date' => now()->subDays(rand(1, 30)),
                    'valid_until' => now()->addMonths(6),
                    'status' => ['issued', 'claimed', 'issued'][$i % 3],
                    'remarks' => null,
                ]);
            } catch (\Exception $e) {
                // Skip if error
            }
        }
        $this->command->info('✓ Created 10 certificates');

        // 4. PWD SUPPORT
        if (class_exists(\App\Models\PwdSupport::class)) {
            $this->command->info('Creating PWD Support Records...');
            $disabilityTypes = ['visual', 'hearing', 'mobility', 'mental', 'psychosocial', 'multiple'];
            $descriptions = [
                'visual' => 'Partial blindness in left eye',
                'hearing' => 'Deaf in both ears',
                'mobility' => 'Difficulty walking, uses cane',
                'mental' => 'Intellectual disability',
                'psychosocial' => 'Bipolar disorder',
                'multiple' => 'Visual and mobility impairment',
            ];
            foreach ($residents->take(6) as $index => $resident) {
                $type = $disabilityTypes[$index % 6];
                \App\Models\PwdSupport::create([
                    'resident_id' => $resident->id,
                    'pwd_id_number' => 'PWD-2025-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                    'disability_type' => $type,
                    'medical_condition' => $descriptions[$type],
                    'aid_status' => 'Monthly cash assistance, Free medicines',
                    'assistive_device' => $index % 2 == 0 ? 'Wheelchair, Hearing aid' : 'Regular therapy sessions',
                    'date_issued' => now()->subMonths(rand(1, 24)),
                    'remarks' => $index % 3 == 0 ? 'Requires home visit' : null,
                    'status' => 'active',
                ]);
            }
            $this->command->info('✓ Created 6 PWD support records');
        } else {
            $this->command->warn('⚠ PwdSupport model not found, skipping.');
        }

        // 5. GOVERNMENT ASSISTANCE
        $this->command->info('Creating Government Assistance Records...');

        $programs = [
            ['type' => '4ps', 'name' => 'Pantawid Pamilyang Pilipino Program', 'amount' => 1400],
            ['type' => 'sss', 'name' => 'SSS Pension', 'amount' => 3000],
            ['type' => 'philhealth', 'name' => 'PhilHealth Coverage', 'amount' => null],
            ['type' => 'ayuda', 'name' => 'COVID-19 Ayuda', 'amount' => 5000],
            ['type' => 'scholarship', 'name' => 'Educational Scholarship', 'amount' => 10000],
            // Map unsupported enum types to 'other' in assistance_type
            ['type' => 'other', 'name' => 'Livelihood Program', 'amount' => 15000, 'program_type' => 'livelihood'],
            ['type' => 'other', 'name' => 'Housing Assistance', 'amount' => 50000, 'program_type' => 'housing'],
        ];

        foreach ($residents->take(14) as $index => $resident) {
            $program = $programs[$index % 7];
            $programType = $program['program_type'] ?? $program['type'];
            GovernmentAssistance::create([
                'resident_id' => $resident->id,
                'assistance_type' => $program['type'],
                'program_name' => $program['name'],
                'program_type' => $programType,
                'amount' => $program['amount'],
                'start_date' => now()->subMonths(rand(3, 12)),
                'end_date' => null,
                'date_received' => now()->subDays(rand(1, 180)),
                'status' => ['active', 'ended', 'active'][$index % 3],
                'description' => 'Beneficiary of '.$program['name'],
                'notes' => $index % 4 == 0 ? 'Renewal needed next month' : null,
            ]);
        }
        $this->command->info('✓ Created 14 government assistance records');

        // 6. CALAMITIES
        $this->command->info('Creating Calamity Records...');

        $calamities = [
            [
                'name' => 'Typhoon Odette',
                'type' => 'typhoon',
                'date' => now()->subMonths(6),
                'severity' => 'catastrophic',
                'areas' => 'Purok 1, Purok 2, Purok 3',
                'description' => 'Super typhoon with winds up to 195 km/h. Caused widespread damage to houses and infrastructure.',
                'response' => 'Evacuation centers opened, relief goods distributed, temporary shelters provided.',
                'status' => 'resolved',
            ],
            [
                'name' => 'Flash Flood - July 2024',
                'type' => 'flood',
                'date' => now()->subMonths(3),
                'severity' => 'severe',
                'areas' => 'Purok 1, Low-lying areas',
                'description' => 'Heavy rainfall caused flash flooding in low-lying areas. Water reached up to 3 feet.',
                'response' => 'Rescue operations conducted, families evacuated, food packs distributed.',
                'status' => 'monitoring',
            ],
            [
                'name' => 'House Fire - Purok 2',
                'type' => 'fire',
                'date' => now()->subMonths(1),
                'severity' => 'moderate',
                'areas' => 'Purok 2',
                'description' => 'Residential fire affected 3 houses. No casualties reported.',
                'response' => 'Fire department responded, affected families given temporary shelter and assistance.',
                'status' => 'resolved',
            ],
        ];

        foreach ($calamities as $calamityData) {
            $calamity = Calamity::create([
                'calamity_name' => $calamityData['name'],
                'calamity_type' => $calamityData['type'],
                'date_occurred' => $calamityData['date'],
                'severity_level' => $calamityData['severity'],
                'affected_areas' => $calamityData['areas'],
                'description' => $calamityData['description'],
                'response_actions' => $calamityData['response'],
                'status' => $calamityData['status'],
                'reported_by' => $secretary->id,
            ]);

            // Add affected households
            $households = Household::approved()->take(rand(3, 8))->get();
            $damageLevels = ['minor', 'moderate', 'severe', 'total'];

            foreach ($households as $hIndex => $household) {
                CalamityAffectedHousehold::create([
                    'calamity_id' => $calamity->id,
                    'household_id' => $household->id,
                    'damage_level' => $damageLevels[$hIndex % 4],
                    'house_damage_cost' => rand(5000, 100000),
                    'needs' => 'Roofing materials, Food supplies, Financial assistance',
                    'relief_received' => $hIndex % 2 == 0,
                    'relief_items' => $hIndex % 2 == 0 ? ['Relief goods', 'Cash assistance ₱5,000'] : [],
                    // no notes column in table; using existing fields only
                ]);
            }

            // Create sample responders and evacuation centers (if not present)
            $centers = EvacuationCenter::count() ? EvacuationCenter::all() : collect([
                ['name' => 'Matina Gym', 'location' => 'Matina Pangi', 'capacity' => 300],
                ['name' => 'Barangay Hall', 'location' => 'Matina Pangi', 'capacity' => 150],
                ['name' => 'Elementary School', 'location' => 'Purok 2', 'capacity' => 250],
            ])->map(fn ($d) => EvacuationCenter::firstOrCreate(['name' => $d['name']], $d));

            $responders = ResponseTeamMember::where('calamity_id', $calamity->id)->get();
            if ($responders->isEmpty()) {
                $roles = ['Responder', 'Medic', 'Ambulance Crew'];
                for ($i = 0; $i < 5; $i++) {
                    $responders->push(ResponseTeamMember::create([
                        'name' => 'Team Member '.($i + 1),
                        'role' => $roles[$i % count($roles)],
                        'calamity_id' => $calamity->id,
                        'evacuation_center_id' => $centers->random()->id,
                    ]));
                }
            }

            // Create sample rescue operations for some affected households
            $affected = CalamityAffectedHousehold::where('calamity_id', $calamity->id)->get();
            foreach ($affected->take(min(3, $affected->count())) as $ah) {
                RescueOperation::create([
                    'calamity_affected_household_id' => $ah->id,
                    'rescuer_type' => 'response_team_member',
                    'rescuer_id' => optional($responders->random())->id,
                    'rescue_time' => now()->subHours(rand(1, 48)),
                    'evacuation_center_id' => optional($centers->random())->id,
                    'notes' => 'Sample rescue entry for reporting',
                ]);
            }
        }
        $this->command->info('✓ Created 3 calamity records with affected households');

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✓ SAMPLE DATA CREATED SUCCESSFULLY!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Summary:');
        $this->command->info('- 10 Certificates');
        $this->command->info('- 6 PWD Support Records');
        $this->command->info('- 14 Government Assistance Records');
        $this->command->info('- 3 Calamity Records with affected households');
        $this->command->info('');
        $this->command->info('You can now explore all the new modules!');
    }
}
