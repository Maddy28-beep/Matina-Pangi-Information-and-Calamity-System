<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Purok;
use App\Models\Resident;
use App\Models\SubFamily;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SimpleExtendedFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates a simple household with:
     * - 1 Primary Head with family (4-5 members)
     * - 1 Co-Head with family (3-4 members)
     * Total: 7-9 members, 2 families
     */
    public function run(): void
    {
        $this->command->info('🏠 Creating simple extended family household...');

        // Get a purok
        $purok = Purok::find(6); // Purok 6

        // Create household
        $household = Household::create([
            'household_id' => 'HH-2025-'.str_pad(Household::count() + 1, 4, '0', STR_PAD_LEFT),
            'purok_id' => $purok->id,
            'address' => '789 Riverside Avenue',
            'purok' => $purok->purok_name,
            'housing_type' => 'owned',
            'has_electricity' => true,
            'electric_account_number' => 'ELEC-'.rand(100000, 999999),
            'total_members' => 0, // Will update later
            'household_type' => 'extended',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'created_at' => now()->subDays(rand(180, 365)),
            'updated_at' => now(),
        ]);

        // Create PRIMARY FAMILY
        $primaryFamily = SubFamily::create([
            'sub_family_name' => 'Primary Family',
            'household_id' => $household->id,
            'is_primary_family' => true,
            'approval_status' => 'approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        // PRIMARY HEAD (Father)
        $primaryHead = Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $primaryFamily->id,
            'first_name' => 'Ricardo',
            'middle_name' => 'Santos',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(52),
            'age' => 52,
            'sex' => 'male',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09171234567',
            'email' => 'ricardo.fernandez@email.com',
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Farmer',
            'employment_status' => 'self-employed',
            'monthly_income' => 15000,
            'educational_attainment' => 'high school graduate',
            'blood_type' => 'O+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ]);

        // Update household and primary family
        $household->update(['official_head_id' => $primaryHead->id]);
        $primaryFamily->update(['sub_head_resident_id' => $primaryHead->id]);

        // Mother (Spouse)
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $primaryFamily->id,
            'first_name' => 'Elena',
            'middle_name' => 'Cruz',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(49),
            'age' => 49,
            'sex' => 'female',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09181234567',
            'email' => 'elena.fernandez@email.com',
            'household_role' => 'spouse',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
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
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ]);

        // Child 1
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $primaryFamily->id,
            'first_name' => 'Jose',
            'middle_name' => 'Ricardo',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(20),
            'age' => 20,
            'sex' => 'male',
            'civil_status' => 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09191234567',
            'email' => 'jose.fernandez@email.com',
            'household_role' => 'child',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Student',
            'employment_status' => 'student',
            'monthly_income' => 0,
            'educational_attainment' => 'college level',
            'blood_type' => 'O+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ]);

        // Child 2
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $primaryFamily->id,
            'first_name' => 'Maria',
            'middle_name' => 'Ricardo',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(16),
            'age' => 16,
            'sex' => 'female',
            'civil_status' => 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => null,
            'email' => null,
            'household_role' => 'child',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_teen' => true,
            'occupation' => 'Student',
            'employment_status' => 'student',
            'monthly_income' => 0,
            'educational_attainment' => 'high school level',
            'blood_type' => 'A+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ]);

        // Child 3
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $primaryFamily->id,
            'first_name' => 'Luis',
            'middle_name' => 'Ricardo',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(12),
            'age' => 12,
            'sex' => 'male',
            'civil_status' => 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => null,
            'email' => null,
            'household_role' => 'child',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'occupation' => 'Student',
            'employment_status' => 'student',
            'monthly_income' => 0,
            'educational_attainment' => 'elementary level',
            'blood_type' => 'O+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ]);

        // Create EXTENDED FAMILY (Married Daughter's Family)
        $extendedFamily = SubFamily::create([
            'sub_family_name' => 'Family of Patricia Fernandez',
            'household_id' => $household->id,
            'is_primary_family' => false,
            'approval_status' => 'approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        // CO-HEAD (Married Daughter)
        $coHead = Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $extendedFamily->id,
            'first_name' => 'Patricia',
            'middle_name' => 'Ricardo',
            'last_name' => 'Fernandez',
            'birthdate' => Carbon::now()->subYears(28),
            'age' => 28,
            'sex' => 'female',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09201234567',
            'email' => 'patricia.fernandez@email.com',
            'household_role' => 'child',
            'is_household_head' => true,
            'is_primary_head' => false,
            'is_co_head' => true,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Teacher',
            'employment_status' => 'employed',
            'monthly_income' => 18000,
            'educational_attainment' => 'college graduate',
            'blood_type' => 'A+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Update extended family with co-head
        $extendedFamily->update(['sub_head_resident_id' => $coHead->id]);

        // Spouse of Co-Head
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $extendedFamily->id,
            'first_name' => 'Marco',
            'middle_name' => 'Reyes',
            'last_name' => 'Santos',
            'birthdate' => Carbon::now()->subYears(30),
            'age' => 30,
            'sex' => 'male',
            'civil_status' => 'married',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => '09211234567',
            'email' => 'marco.santos@email.com',
            'household_role' => 'relative',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_voter' => true,
            'precinct_number' => 'PCT-'.rand(1000, 9999),
            'occupation' => 'Electrician',
            'employment_status' => 'employed',
            'monthly_income' => 16000,
            'educational_attainment' => 'vocational',
            'blood_type' => 'B+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Grandchild 1
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $extendedFamily->id,
            'first_name' => 'Sofia',
            'middle_name' => 'Patricia',
            'last_name' => 'Santos',
            'birthdate' => Carbon::now()->subYears(5),
            'age' => 5,
            'sex' => 'female',
            'civil_status' => 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => null,
            'email' => null,
            'household_role' => 'relative',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'occupation' => 'None',
            'employment_status' => null,
            'monthly_income' => 0,
            'educational_attainment' => 'no formal education',
            'blood_type' => 'A+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Grandchild 2
        Resident::create([
            'resident_id' => 'RES-2025-'.str_pad(Resident::count() + 1, 4, '0', STR_PAD_LEFT),
            'household_id' => $household->id,
            'sub_family_id' => $extendedFamily->id,
            'first_name' => 'Gabriel',
            'middle_name' => 'Patricia',
            'last_name' => 'Santos',
            'birthdate' => Carbon::now()->subYears(2),
            'age' => 2,
            'sex' => 'male',
            'civil_status' => 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => 'Roman Catholic',
            'contact_number' => null,
            'email' => null,
            'household_role' => 'relative',
            'is_household_head' => false,
            'is_primary_head' => false,
            'is_co_head' => false,
            'status' => 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'occupation' => 'None',
            'employment_status' => null,
            'monthly_income' => 0,
            'educational_attainment' => 'no formal education',
            'blood_type' => 'B+',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now()->subDays(rand(60, 180)),
            'updated_at' => now(),
        ]);

        // Update household total members
        $totalMembers = $household->residents()->count();
        $household->update(['total_members' => $totalMembers]);

        // Update purok counts
        $purok->increment('total_households');
        $purok->increment('total_population', $totalMembers);

        $this->command->info('✓ Simple extended family household created!');
        $this->command->info('');
        $this->command->info("📊 Household: {$household->household_id}");
        $this->command->info('   Primary Head: Ricardo Fernandez (52 yrs)');
        $this->command->info('   Primary Family: 5 members');
        $this->command->info('   Co-Head: Patricia Fernandez (28 yrs)');
        $this->command->info('   Extended Family: 4 members');
        $this->command->info("   Total: {$totalMembers} members, 2 families");
    }
}
