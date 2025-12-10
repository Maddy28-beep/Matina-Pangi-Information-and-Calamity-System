<?php

namespace Database\Seeders;

use App\Models\PwdSupport;
use App\Models\Resident;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SimpleSampleDataSeeder extends Seeder
{
    protected $faker;

    public function run()
    {
        $this->faker = Faker::create('en_PH');
        $secretary = User::where('role', 'secretary')->first();

        if (! $secretary) {
            $this->command->error('No secretary user found. Please run UserSeeder first.');

            return;
        }

        // Get some residents for testing
        $residents = Resident::approved()->active()->take(20)->get();

        if ($residents->isEmpty()) {
            $this->command->error('No residents found! Please add residents first.');

            return;
        }

        $this->command->info('Updating residents with sample data...');

        // Update basic resident information
        foreach ($residents as $index => $resident) {
            $updates = [
                'contact_number' => '09'.$this->faker->numerify('#########'),
                'occupation' => $this->faker->randomElement(['Vendor', 'Driver', 'Teacher', 'Housewife', 'Student', 'Fisherman', 'Farmer', 'Construction Worker']),
                'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
                'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', null]),
            ];

            if (\Schema::hasColumn('residents', 'household_id')) {
                // Use integer values for household_id
                $updates['household_id'] = ($index % 5) + 1;
            }

            $resident->update($updates);
        }

        $this->command->info('Sample data updated successfully!');
        $this->command->info('Updated '.$residents->count().' residents with sample data.');

        // Create PWD support records
        if (class_exists(\App\Models\PwdSupport::class)) {
            $this->createPwdSupportRecords($residents, $secretary);
        } else {
            $this->command->warn('⚠ PwdSupport model not found, skipping.');
        }

        $this->command->info('Sample data has been seeded successfully!');
    }

    private function createPwdSupportRecords($residents, $secretary)
    {
        $this->command->info('Creating PWD support records...');
        $count = 0;

        $disabilityTypes = [
            'Visual Impairment', 'Hearing Impairment', 'Physical Disability',
            'Intellectual Disability', 'Learning Disability', 'Autism Spectrum Disorder',
            'Mental Health Condition', 'Chronic Illness',
        ];

        $assistiveDevices = [
            'Wheelchair', 'Crutches', 'Walker', 'Cane', 'Prosthetic Limb',
            'Hearing Aid', 'White Cane', 'Glasses', 'Orthopedic Shoes', 'Communication Board',
        ];

        $supportServices = [
            'Physical Therapy', 'Occupational Therapy', 'Speech Therapy',
            'Special Education', 'Vocational Training', 'Counseling',
            'Home Care Services', 'Transportation Assistance',
        ];

        foreach ($residents as $resident) {
            // Only create for some residents (5-10% chance)
            if ($this->faker->boolean(7) && $count < 5) {
                try {
                    $devicesNeeded = $this->faker->randomElements(
                        $assistiveDevices,
                        $this->faker->numberBetween(0, 3)
                    );

                    $servicesReceived = $this->faker->randomElements(
                        $supportServices,
                        $this->faker->numberBetween(0, 2)
                    );

                    PwdSupport::create([
                        'resident_id' => $resident->id,
                        'pwd_id_number' => 'PWD-'.date('Y').'-'.str_pad($count + 1, 4, '0', STR_PAD_LEFT),
                        'disability_type' => $this->faker->randomElement($disabilityTypes),
                        'medical_condition' => $this->faker->optional(0.7)->sentence(),
                        'assistive_device' => ! empty($devicesNeeded) ? implode(', ', $devicesNeeded) : null,
                        'aid_status' => ! empty($servicesReceived) ? implode(', ', $servicesReceived) : null,
                        'date_issued' => now()->subMonths(rand(1, 6)),
                        'pwd_id_expiry' => now()->addYears(3),
                        'remarks' => $this->faker->optional(0.5)->sentence(),
                        'status' => 'active',
                        'created_by' => $secretary->id,
                        'updated_by' => $secretary->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $count++;
                } catch (\Exception $e) {
                    $this->command->error('Error creating PWD support record for resident '.$resident->id.': '.$e->getMessage());
                }
            }
        }

        $this->command->info('Created '.$count.' PWD support records.');
    }

    private function getRandomChildNutritionalStatus()
    {
        return $this->faker->randomElement([
            'Normal', 'Underweight', 'Severely Underweight', 'Overweight', 'Wasted', 'Stunted',
        ]);
    }

    private function getRandomDisabilityCause()
    {
        return $this->faker->randomElement([
            'Congenital', 'Illness', 'Accident', 'Aging', 'Unknown',
        ]);
    }

    private function getRandomMobilityAid()
    {
        return $this->faker->randomElement([
            'Wheelchair', 'Cane', 'Walker', 'Crutches', 'Prosthesis', 'Hearing Aid', 'None',
        ]);
    }

    private function getRandomMedications()
    {
        $medications = [
            'Lisinopril 10mg once daily for hypertension',
            'Metformin 500mg twice daily for diabetes',
            'Amlodipine 5mg once daily for high blood pressure',
            'Simvastatin 20mg at bedtime for high cholesterol',
            'Losartan 50mg once daily for hypertension',
            'Metoprolol 25mg twice daily for high blood pressure',
            'Atorvastatin 20mg at bedtime for high cholesterol',
            'Omeprazole 20mg once daily for acid reflux',
            'Levothyroxine 50mcg once daily for hypothyroidism',
            'Albuterol inhaler as needed for asthma',
            'None',
        ];

        return $this->faker->randomElements($medications, $this->faker->numberBetween(0, 3));
    }
}
