<?php

namespace Database\Seeders;

use App\Models\Calamity;
use App\Models\Household;
use App\Models\ReliefDistribution;
use App\Models\ReliefItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReliefDistributionSeeder extends Seeder
{
    public function run(): void
    {
        $secretary = User::where('role', 'secretary')->first();
        if (! $secretary) {
            $this->command->warn('No secretary user found. Skipping relief distributions.');
            return;
        }

        $calamity = Calamity::latest('date_occurred')->first();
        if (! $calamity) {
            $calamity = Calamity::create([
                'calamity_name' => 'Sample Relief Operation',
                'calamity_type' => 'drill',
                'severity' => 'low',
                'date_occurred' => now()->toDateString(),
                'affected_puroks' => json_encode([]),
                'description' => 'Preparedness drill entry',
            ]);
        }

        if (ReliefItem::count() === 0) {
            foreach ([
                ['name' => 'Rice (10kg)', 'quantity' => 200, 'unit' => 'sack'],
                ['name' => 'Canned Goods', 'quantity' => 500, 'unit' => 'box'],
                ['name' => 'Bottled Water', 'quantity' => 800, 'unit' => 'case'],
                ['name' => 'Blankets', 'quantity' => 150, 'unit' => 'piece'],
                ['name' => 'Instant Noodles', 'quantity' => 600, 'unit' => 'box'],
            ] as $d) {
                ReliefItem::firstOrCreate(['name' => $d['name']], $d);
            }
        }

        $households = Household::inRandomOrder()->take(5)->get();
        if ($households->isEmpty()) {
            $this->command->warn('No households found. Skipping relief distributions.');
            return;
        }

        $items = ReliefItem::all();
        foreach ($households as $hh) {
            $item = $items->random();
            ReliefDistribution::create([
                'calamity_id' => $calamity->id,
                'household_id' => $hh->id,
                'relief_item_id' => $item->id,
                'quantity' => rand(1, 3),
                'staff_in_charge' => $secretary->id,
                'distributed_at' => now()->subDays(rand(1, 7)),
            ]);
        }

        $this->command->info('✓ Relief distributions seeded (5 entries)');
    }
}

