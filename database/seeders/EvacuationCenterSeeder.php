<?php

namespace Database\Seeders;

use App\Models\EvacuationCenter;
use Illuminate\Database\Seeder;

class EvacuationCenterSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Matina Pangi Gymnasium', 'location' => 'Km1 Matina Pangi, Davao City', 'capacity' => 300, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Kitchen', 'Water Supply', 'Medical Station'])],
            ['name' => 'Barangay Hall Evacuation Area', 'location' => 'Barangay Center, Matina Pangi, Davao City', 'capacity' => 150, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Water Supply', 'Power Generator'])],
            ['name' => 'Matina Pangi Elementary School', 'location' => 'Km2 Matina Pangi, Davao City', 'capacity' => 250, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Classrooms', 'Playground', 'Water Supply'])],
            ['name' => 'Community Center', 'location' => 'Km3 Matina Pangi, Davao City', 'capacity' => 200, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Kitchen', 'Stage Area'])],
            ['name' => 'Covered Court', 'location' => 'Km4 Matina Pangi, Davao City', 'capacity' => 180, 'current_occupancy' => 0, 'facilities' => json_encode(['Restrooms', 'Water Supply', 'Benches'])],
        ];

        foreach ($data as $d) {
            EvacuationCenter::firstOrCreate(['name' => $d['name']], $d);
        }

        $this->command->info('✓ Evacuation centers seeded (5 entries)');
    }
}

