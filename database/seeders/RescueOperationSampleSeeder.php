<?php

namespace Database\Seeders;

use App\Models\Calamity;
use App\Models\CalamityAffectedHousehold;
use App\Models\EvacuationCenter;
use App\Models\RescueOperation;
use App\Models\ResponseTeamMember;
use Illuminate\Database\Seeder;

class RescueOperationSampleSeeder extends Seeder
{
    public function run()
    {
        $calamity = Calamity::latest('date_occurred')->first();
        if (! $calamity) {
            $this->command->warn('No calamity records found. Skipping rescue sample seeding.');

            return;
        }

        $affected = CalamityAffectedHousehold::where('calamity_id', $calamity->id)->take(5)->get();
        if ($affected->isEmpty()) {
            $households = \App\Models\Household::approved()->take(5)->get();
            if ($households->isEmpty()) {
                $this->command->warn('No households found to create affected households. Skipping.');

                return;
            }
            foreach ($households as $idx => $hh) {
                $affected->push(CalamityAffectedHousehold::create([
                    'calamity_id' => $calamity->id,
                    'household_id' => $hh->id,
                    'damage_level' => ['minor', 'moderate', 'severe', 'total'][$idx % 4],
                    'casualties' => 0,
                    'injured' => rand(0, 2),
                    'missing' => 0,
                    'evacuation_status' => ['in_home', 'evacuated', 'returned'][$idx % 3],
                ]));
            }
        }

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

        foreach ($affected as $ah) {
            RescueOperation::create([
                'calamity_affected_household_id' => $ah->id,
                'rescuer_type' => 'response_team_member',
                'rescuer_id' => optional($responders->random())->id,
                'rescue_time' => now()->subHours(rand(1, 48)),
                'evacuation_center_id' => optional($centers->random())->id,
                'notes' => 'Sample rescue entry for visibility',
            ]);
        }

        $this->command->info('✓ Seeded sample rescue operations');
    }
}
