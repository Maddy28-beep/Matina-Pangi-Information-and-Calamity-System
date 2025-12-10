<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Resident;
use App\Models\SubFamily;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExtendedFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder adds:
     * 1. Extended families (sub-families) to existing households
     * 2. Relocated residents
     * 3. Deceased residents
     */
    public function run(): void
    {
        $this->command->info('🏠 Adding extended families to households...');

        // Get the first household (HH-2025-0001) and add extended families
        $household1 = Household::where('household_id', 'HH-2025-0001')->first();

        if ($household1) {
            // Add married daughter's family living in the same house
            $this->addMarriedChildFamily($household1, 'female', 'Daughter');

            // Add married son's family living in the same house
            $this->addMarriedChildFamily($household1, 'male', 'Son');
        }

        // Get another household and add extended family
        $household2 = Household::where('household_id', 'HH-2025-0005')->first();

        if ($household2) {
            // Add sibling's family
            $this->addSiblingFamily($household2);
        }

        $this->command->info('👥 Adding relocated and deceased residents...');

        // Add relocated residents
        $this->addRelocatedResidents();

        // Add deceased residents
        $this->addDeceasedResidents();

        $this->command->info('✓ Extended families, relocated, and deceased residents added successfully!');
        $this->command->info('');
        $this->command->info('📊 Updated Statistics:');
        $this->command->info('   • Total Sub-Families: '.SubFamily::count());
        $this->command->info('   • Total Residents: '.Resident::count());
        $this->command->info('   • Active Residents: '.Resident::where('status', 'active')->count());
        $this->command->info('   • Reallocated Residents: '.Resident::where('status', 'reallocated')->count());
        $this->command->info('   • Deceased Residents: '.Resident::where('status', 'deceased')->count());
    }

    private function addMarriedChildFamily($household, $sex, $relation)
    {
        // Get the primary family head
        $primaryHead = $household->officialHead;

        // Create sub-family
        $childName = $sex === 'male' ? 'Miguel' : 'Rosa';
        $spouseName = $sex === 'male' ? 'Sofia' : 'Carlos';

        $subFamily = SubFamily::create([
            'sub_family_name' => "Family of {$childName} {$primaryHead->last_name}",
            'household_id' => $household->id,
            'is_primary_family' => false,
            'approval_status' => 'approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        // Create married child (sub-family head)
        $marriedChild = Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $subFamily->id,
            'first_name' => $childName,
            'middle_name' => $primaryHead->first_name,
            'last_name' => $primaryHead->last_name,
            'suffix' => null,
            'birthdate' => Carbon::now()->subYears(rand(28, 35)),
            'age' => rand(28, 35),
            'sex' => $sex,
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09'.rand(100000000, 999999999),
            'email' => strtolower($childName.'.'.$primaryHead->last_name.'@email.com'),
            'household_role' => 'child',
            'is_household_head' => true,
            'is_primary_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_pwd' => false,
            'is_senior_citizen' => false,
            'is_teen' => false,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'is_4ps_beneficiary' => false,
            'occupation' => ['Teacher', 'Nurse', 'Office Worker', 'Store Owner'][rand(0, 3)],
            'employment_status' => 'employed',
            'monthly_income' => rand(15000, 25000),
            'educational_attainment' => 'college graduate',
            'blood_type' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Update sub-family with head
        $subFamily->update(['sub_head_resident_id' => $marriedChild->id]);

        // Create spouse
        $spouse = Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $subFamily->id,
            'first_name' => $spouseName,
            'middle_name' => ['Santos', 'Reyes', 'Garcia'][rand(0, 2)],
            'last_name' => $sex === 'male' ? $primaryHead->last_name : ['Santos', 'Reyes', 'Garcia'][rand(0, 2)],
            'birthdate' => Carbon::now()->subYears($marriedChild->age - rand(0, 3)),
            'age' => $marriedChild->age - rand(0, 3),
            'sex' => $sex === 'male' ? 'female' : 'male',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09'.rand(100000000, 999999999),
            'email' => strtolower($spouseName.'.'.($sex === 'male' ? $primaryHead->last_name : 'santos').'@email.com'),
            'household_role' => 'relative',
            'is_household_head' => false,
            'is_primary_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => ['Vendor', 'Housewife', 'Office Worker'][rand(0, 2)],
            'employment_status' => rand(0, 1) ? 'employed' : 'unemployed',
            'monthly_income' => rand(8000, 15000),
            'educational_attainment' => ['high school graduate', 'college level', 'college graduate'][rand(0, 2)],
            'blood_type' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Create children (2-3)
        $childrenCount = rand(2, 3);
        for ($i = 0; $i < $childrenCount; $i++) {
            $childAge = rand(2, 10);
            Resident::create([
                'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
                'household_id' => $household->id,
                'sub_family_id' => $subFamily->id,
                'first_name' => ['Luis', 'Elena', 'Marco', 'Ana', 'Pedro', 'Maria'][rand(0, 5)],
                'middle_name' => $marriedChild->first_name,
                'last_name' => $marriedChild->last_name,
                'birthdate' => Carbon::now()->subYears($childAge),
                'age' => $childAge,
                'sex' => rand(0, 1) ? 'male' : 'female',
                'civil_status' => 'single',
                'place_of_birth' => 'Davao City',
                'nationality' => 'Filipino',
                'religion' => 'Roman Catholic',
                'contact_number' => null,
                'email' => null,
                'household_role' => 'relative',
                'is_household_head' => false,
                'is_primary_head' => false,
                'status' => 'active',
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => 1,
                'is_teen' => $childAge >= 13,
                'occupation' => $childAge >= 6 ? 'Student' : 'None',
                'employment_status' => $childAge >= 6 ? 'student' : null,
                'monthly_income' => 0,
                'educational_attainment' => $childAge >= 6 ? 'elementary level' : 'no formal education',
                'blood_type' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now()->subDays(rand(60, 180)),
                'updated_at' => now(),
            ]);
        }

        // Update household total members
        $household->update(['total_members' => $household->residents()->count()]);
    }

    private function addSiblingFamily($household)
    {
        $primaryHead = $household->officialHead;

        $subFamily = SubFamily::create([
            'sub_family_name' => "Family of Roberto {$primaryHead->last_name}",
            'household_id' => $household->id,
            'is_primary_family' => false,
            'approval_status' => 'approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        // Create sibling (sub-family head)
        $sibling = Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $subFamily->id,
            'first_name' => 'Roberto',
            'middle_name' => $primaryHead->middle_name,
            'last_name' => $primaryHead->last_name,
            'birthdate' => Carbon::now()->subYears($primaryHead->age - rand(2, 5)),
            'age' => $primaryHead->age - rand(2, 5),
            'sex' => 'male',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09'.rand(100000000, 999999999),
            'email' => 'roberto.'.$primaryHead->last_name.'@email.com',
            'household_role' => 'sibling',
            'is_household_head' => true,
            'is_primary_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Construction Worker',
            'employment_status' => 'employed',
            'monthly_income' => rand(12000, 18000),
            'educational_attainment' => 'high school graduate',
            'blood_type' => 'O+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        $subFamily->update(['sub_head_resident_id' => $sibling->id]);

        // Create spouse
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $subFamily->id,
            'first_name' => 'Carmen',
            'middle_name' => 'Flores',
            'last_name' => $primaryHead->last_name,
            'birthdate' => Carbon::now()->subYears($sibling->age - 2),
            'age' => $sibling->age - 2,
            'sex' => 'female',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09'.rand(100000000, 999999999),
            'email' => 'carmen.'.$primaryHead->last_name.'@email.com',
            'household_role' => 'relative',
            'is_household_head' => false,
            'is_primary_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Housewife',
            'employment_status' => 'unemployed',
            'monthly_income' => 0,
            'educational_attainment' => 'high school graduate',
            'blood_type' => 'A+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        $household->update(['total_members' => $household->residents()->count()]);
    }

    private function addRelocatedResidents()
    {
        // Get some random residents and mark them as reallocated
        $residents = Resident::where('status', 'active')
            ->where('is_household_head', false)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        foreach ($residents as $resident) {
            $resident->update([
                'status' => 'reallocated',
                'status_notes' => 'Moved to '.['Manila', 'Cebu', 'Cagayan de Oro', 'General Santos'][rand(0, 3)].' for work/study',
                'status_changed_at' => now()->subDays(rand(30, 180)),
                'status_changed_by' => 1,
            ]);
        }
    }

    private function addDeceasedResidents()
    {
        // Get some senior citizens and mark them as deceased
        $seniors = Resident::where('is_senior_citizen', true)
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(2)
            ->get();

        foreach ($seniors as $senior) {
            $senior->update([
                'status' => 'deceased',
                'status_notes' => 'Passed away due to '.['natural causes', 'illness', 'old age'][rand(0, 2)],
                'status_changed_at' => now()->subDays(rand(30, 365)),
                'status_changed_by' => 1,
            ]);
        }
    }
}
