<?php

namespace Database\Seeders;

// Health module removed; guard usage dynamically
use App\Models\PwdSupport;
use App\Models\Resident;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CleanSampleDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_PH');
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

        $this->command->info('Creating sample data for all modules...');

        // 1. Update residents with more details
        $this->command->info('Updating residents with sample data...');
        foreach ($residents as $index => $resident) {
            $purok = 'Purok '.(($index % 5) + 1);
            $updates = [
                'contact_number' => '09'.$faker->numerify('#########'),
                'occupation' => $faker->randomElement(['Vendor', 'Driver', 'Teacher', 'Housewife', 'Student', 'Fisherman', 'Farmer', 'Construction Worker']),
                'civil_status' => $faker->randomElement(['Single', 'Married', 'Widowed', 'Separated']),
                'blood_type' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', null]),
            ];

            // Only update household_id if the column exists
            if (\Schema::hasColumn('residents', 'household_id')) {
                $updates['household_id'] = 'HH-'.str_pad((($index % 5) + 1), 4, '0', STR_PAD_LEFT);
            }

            // Only update address if the column exists
            if (\Schema::hasColumn('residents', 'address')) {
                $updates['address'] = $purok.', Matina Pangi, Davao City';
            }

            $resident->update($updates);
        }
        // 3. Create PWD support records
        $this->command->info('Creating PWD Support Records...');
        $pwdTypes = [
            'Visual Impairment', 'Hearing Impairment', 'Physical Disability',
            'Intellectual Disability', 'Learning Disability', 'Mental Health Condition',
            'Chronic Illness', 'Multiple Disabilities',
        ];

        foreach ($residents->take(5) as $index => $resident) {
            PwdSupport::create([
                'resident_id' => $resident->id,
                'pwd_id_number' => 'PWD-'.now()->format('Y').'-'.str_pad(($index + 1), 4, '0', STR_PAD_LEFT),
                'disability_type' => $faker->randomElement($pwdTypes),
                'disability_cause' => $faker->randomElement(['Congenital', 'Illness', 'Accident', 'Aging']),
                'diagnosis_date' => $faker->dateTimeBetween('-10 years', '-6 months'),
                'medical_specialist' => 'Dr. '.$faker->lastName.', '.
                    $faker->randomElement(['Ophthalmology', 'ENT', 'Rehabilitation Medicine', 'Neurology']),
                'mobility_aid' => $faker->randomElement(['Wheelchair', 'Cane', 'Hearing Aid', 'Prosthesis', 'None']),
                'emergency_contact' => $faker->name.' ('.$faker->randomElement(['Spouse', 'Child', 'Sibling', 'Relative']).')',
                'emergency_contact_number' => '09'.$faker->numerify('#########'),
                'created_by' => $secretary->id,
            ]);
        }

        // 4. Create disease monitoring records (optional)
        if (class_exists(\App\Models\DiseaseCase::class)) {
            $this->command->info('Creating Disease Monitoring Records...');
            $diseases = [
                ['Dengue', 'Dengue Fever'],
                ['Influenza', 'Flu'],
                ['COVID-19', 'Coronavirus Disease 2019'],
                ['Tuberculosis', 'TB'],
            ];

            foreach (range(1, 8) as $i) {
                $disease = $faker->randomElement($diseases);
                $status = $faker->randomElement(['Suspected', 'Probable', 'Confirmed', 'Recovered']);

                \App\Models\DiseaseCase::create([
                    'disease_code' => strtoupper(substr($disease[0], 0, 3)).'-'.$faker->numberBetween(100, 999),
                    'disease_name' => $disease[0],
                    'disease_description' => $disease[1],
                    'symptoms' => $this->getSymptomsForDisease($disease[0]),
                    'date_reported' => $faker->dateTimeBetween('-6 months', 'now'),
                    'status' => $status,
                    'purok' => 'Purok '.$faker->numberBetween(1, 5),
                    'age_group' => $faker->randomElement(['0-5', '6-12', '13-19', '20-39', '40-59', '60+']),
                    'gender' => $faker->randomElement(['Male', 'Female']),
                    'reported_by' => $secretary->id,
                    'created_by' => $secretary->id,
                ]);
            }
        } else {
            $this->command->warn('⚠ DiseaseCase model not found, skipping disease monitoring records.');
        }

        $this->command->info('Sample data creation completed successfully!');
    }

    private function getSymptomsForDisease($disease)
    {
        $symptoms = [
            'Dengue' => 'High fever, severe headache, pain behind the eyes, joint and muscle pain, rash',
            'Influenza' => 'Fever, cough, sore throat, runny or stuffy nose, body aches, headache, fatigue',
            'COVID-19' => 'Fever, dry cough, tiredness, loss of taste or smell, difficulty breathing',
            'Tuberculosis' => 'Persistent cough, fever, night sweats, weight loss, fatigue',
        ];

        return $symptoms[$disease] ?? 'Fever, body malaise, headache';
    }
}
