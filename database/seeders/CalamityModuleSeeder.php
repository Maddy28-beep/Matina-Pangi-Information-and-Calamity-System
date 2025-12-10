<?php

namespace Database\Seeders;

use App\Models\Calamity;
use App\Models\CalamityAffectedHousehold;
use App\Models\DamageAssessment;
use App\Models\EvacuationCenter;
use App\Models\Household;
use App\Models\Notification;
use App\Models\Purok;
use App\Models\ReliefDistribution;
use App\Models\ReliefItem;
use App\Models\ResponseTeamMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CalamityModuleSeeder extends Seeder
{
    public function run(): void
    {
        $secretary = User::first();
        if (! $secretary) {
            $secretary = User::factory()->create([
                'name' => 'Barangay Secretary',
                'email' => 'secretary@pangi.gov',
                'password' => bcrypt('password'),
            ]);
        }

        $purokNames = Purok::pluck('purok_name')->all();
        if (empty($purokNames)) {
            $purokNames = ['Purok 1', 'Purok 2', 'Purok 3'];
        }

        $centers = collect([
            ['name' => 'Matina Pangi Gymnasium', 'location' => 'Km1 Matina Pangi, Davao City', 'capacity' => 300, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Kitchen', 'Water Supply', 'Medical Station'])],
            ['name' => 'Barangay Hall Evacuation Area', 'location' => 'Barangay Center, Matina Pangi, Davao City', 'capacity' => 150, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Water Supply', 'Power Generator'])],
            ['name' => 'Matina Pangi Elementary School', 'location' => 'Km2 Matina Pangi, Davao City', 'capacity' => 250, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Classrooms', 'Playground', 'Water Supply'])],
            ['name' => 'Community Center', 'location' => 'Km3 Matina Pangi, Davao City', 'capacity' => 200, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Kitchen', 'Stage Area'])],
            ['name' => 'Covered Court', 'location' => 'Km4 Matina Pangi, Davao City', 'capacity' => 180, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Water Supply', 'Benches'])],
        ])->map(function ($d) {
            return EvacuationCenter::firstOrCreate(['name' => $d['name']], $d);
        });

        $items = collect([
            ['name' => 'Rice (10kg)', 'quantity' => 200, 'unit' => 'sack'],
            ['name' => 'Canned Goods', 'quantity' => 500, 'unit' => 'box'],
            ['name' => 'Bottled Water', 'quantity' => 800, 'unit' => 'case'],
            ['name' => 'Blankets', 'quantity' => 150, 'unit' => 'piece'],
        ])->map(function ($d) {
            return ReliefItem::firstOrCreate(['name' => $d['name']], $d);
        });

        $calamities = [];
        $calamities[] = Calamity::create([
            'calamity_name' => 'Flooding in Purok 2',
            'calamity_type' => 'flood',
            'severity' => 'moderate',
            'date_occurred' => Carbon::now()->subDays(15)->toDateString(),
            'affected_puroks' => json_encode([$purokNames[1] ?? 'Purok 2']),
            'description' => 'Heavy rain caused river to overflow.',
        ]);
        $calamities[] = Calamity::create([
            'calamity_name' => 'Fire Incident at Purok 1',
            'calamity_type' => 'fire',
            'severity' => 'minor',
            'date_occurred' => Carbon::now()->subDays(30)->toDateString(),
            'affected_puroks' => json_encode([$purokNames[0] ?? 'Purok 1']),
            'description' => 'Small residential fire contained.',
        ]);

        $households = Household::inRandomOrder()->take(40)->get();
        if ($households->isEmpty()) {
            $households = collect();
        }

        foreach ($calamities as $calamity) {
            $selectedHouseholds = $households->shuffle()->take(20);
            foreach ($selectedHouseholds as $hh) {
                CalamityAffectedHousehold::create([
                    'calamity_id' => $calamity->id,
                    'household_id' => $hh->id,
                    'damage_level' => ['minor', 'moderate', 'severe'][rand(0, 2)],
                    'needs' => 'Assessed during rapid survey.',
                ]);
            }

            DamageAssessment::create([
                'calamity_id' => $calamity->id,
                'assessed_by' => $secretary->id,
                'damage_level' => ['minor', 'moderate', 'severe'][rand(0, 2)],
                'estimated_cost' => rand(50000, 250000),
                'description' => 'Initial damage assessment prepared.',
            ]);

            Notification::create([
                'title' => 'Relief Operation',
                'message' => 'Relief distribution scheduled for affected households.',
                'calamity_id' => $calamity->id,
                'type' => 'system',
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $teamNames = ['Team Alpha', 'Team Bravo', 'Team Charlie'];
            foreach ($teamNames as $tn) {
                ResponseTeamMember::create([
                    'name' => $tn.' Member '.Str::random(4),
                    'role' => 'Volunteer',
                    'calamity_id' => $calamity->id,
                    'evacuation_center_id' => $centers->random()->id,
                ]);
            }

            $targets = $selectedHouseholds->take(8);
            foreach ($targets as $hh) {
                $item = $items->random();
                ReliefDistribution::create([
                    'calamity_id' => $calamity->id,
                    'household_id' => $hh->id,
                    'relief_item_id' => $item->id,
                    'quantity' => rand(1, 3),
                    'staff_in_charge' => $secretary->id,
                    'distributed_at' => Carbon::now()->subDays(rand(1, 7))->toDateTimeString(),
                ]);
            }
        }
    }
}
