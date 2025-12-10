<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Purok;
use App\Models\Resident;
use App\Models\SubFamily;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HouseholdAndResidentSeeder extends Seeder
{
    private $householdCounter = 1;

    private $residentCounter = 1;

    private $subFamilyCounter = 1;

    private $firstNames = [
        'male' => ['Juan', 'Pedro', 'Jose', 'Antonio', 'Carlos', 'Miguel', 'Roberto', 'Fernando', 'Ricardo', 'Manuel', 'Luis', 'Rafael', 'Eduardo', 'Andres', 'Ramon'],
        'female' => ['Maria', 'Ana', 'Rosa', 'Elena', 'Carmen', 'Luz', 'Gloria', 'Teresa', 'Sofia', 'Isabel', 'Patricia', 'Cristina', 'Angela', 'Beatriz', 'Diana'],
    ];

    private $lastNames = ['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Martinez', 'Rodriguez', 'Fernandez', 'Lopez', 'Gonzales', 'Perez', 'Sanchez', 'Ramirez', 'Torres', 'Flores', 'Rivera', 'Gomez', 'Diaz', 'Cruz', 'Morales', 'Jimenez'];

    private $religions = ['Roman Catholic', 'Iglesia ni Cristo', 'Born Again Christian', 'Islam', 'Baptist', 'Seventh-day Adventist'];

    private $occupations = ['Farmer', 'Tricycle Driver', 'Vendor', 'Teacher', 'Construction Worker', 'Store Owner', 'Housewife', 'Student', 'Retired', 'OFW', 'Carpenter', 'Electrician', 'Fisherman', 'Barangay Tanod', 'None'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puroks = Purok::all();

        // Create 15 households with realistic family structures
        $this->createHousehold($puroks[0], 'Nuclear Family with 4 children');
        $this->createHousehold($puroks[0], 'Extended Family with grandparents');
        $this->createHousehold($puroks[1], 'Young couple with baby');
        $this->createHousehold($puroks[1], 'Single parent household');
        $this->createHousehold($puroks[2], 'Large extended family');
        $this->createHousehold($puroks[2], 'Senior couple living alone');
        $this->createHousehold($puroks[3], 'Family with PWD member');
        $this->createHousehold($puroks[3], 'OFW family');
        $this->createHousehold($puroks[4], '4Ps beneficiary family');
        $this->createHousehold($puroks[4], 'Multi-generational household');
        $this->createHousehold($puroks[5], 'Young professional couple');
        $this->createHousehold($puroks[6], 'Farmer family');
        $this->createHousehold($puroks[7], 'Vendor family');
        $this->createHousehold($puroks[8], 'Teacher household');
        $this->createHousehold($puroks[9], 'Mixed occupation family');

        $this->command->info('✓ 15 Households with 40-60 residents seeded successfully!');
    }

    private function createHousehold($purok, $type)
    {
        $householdId = 'HH-2025-'.str_pad($this->householdCounter++, 4, '0', STR_PAD_LEFT);

        $addresses = [
            '123 Main Street',
            '456 Riverside Avenue',
            '789 Hillside Road',
            '321 Market Street',
            '654 Church Avenue',
            '987 School Road',
            '147 Farm Road',
            '258 Coastal Drive',
            '369 Mountain View',
            '741 Plaza Street',
            '852 Garden Lane',
            '963 Park Avenue',
            '159 Valley Road',
            '357 Summit Street',
            '486 Meadow Lane',
        ];

        $address = $addresses[($this->householdCounter - 2) % count($addresses)];

        // Skip if household already exists (idempotent)
        if (Household::where('household_id', $householdId)->exists()) {
            return;
        }

        // Create household
        $household = Household::create([
            'household_id' => $householdId,
            'purok_id' => $purok->id,
            'address' => $address,
            'purok' => $purok->purok_name,
            'housing_type' => $this->randomHousingType(),
            'has_electricity' => rand(0, 10) > 1, // 90% have electricity
            'electric_account_number' => rand(0, 10) > 1 ? 'ELEC-'.rand(100000, 999999) : null,
            'total_members' => 0, // Will update after adding members
            'household_type' => 'family',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'created_at' => now()->subDays(rand(30, 365)),
            'updated_at' => now(),
        ]);

        // Create primary sub-family
        $primaryFamily = SubFamily::create([
            'sub_family_name' => 'Primary Family',
            'household_id' => $household->id,
            'is_primary_family' => true,
            'approval_status' => 'approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        // Generate family based on type
        $members = [];

        switch ($type) {
            case 'Nuclear Family with 4 children':
                $members = $this->createNuclearFamily($household, $primaryFamily, 4);
                break;
            case 'Extended Family with grandparents':
                $members = $this->createExtendedFamily($household, $primaryFamily);
                break;
            case 'Young couple with baby':
                $members = $this->createYoungCouple($household, $primaryFamily);
                break;
            case 'Single parent household':
                $members = $this->createSingleParent($household, $primaryFamily);
                break;
            case 'Large extended family':
                $members = $this->createLargeExtendedFamily($household, $primaryFamily);
                break;
            case 'Senior couple living alone':
                $members = $this->createSeniorCouple($household, $primaryFamily);
                break;
            case 'Family with PWD member':
                $members = $this->createFamilyWithPWD($household, $primaryFamily);
                break;
            case 'OFW family':
                $members = $this->createOFWFamily($household, $primaryFamily);
                break;
            case '4Ps beneficiary family':
                $members = $this->create4PsFamily($household, $primaryFamily);
                break;
            case 'Multi-generational household':
                $members = $this->createMultiGenerational($household, $primaryFamily);
                break;
            default:
                $members = $this->createNuclearFamily($household, $primaryFamily, rand(2, 3));
        }

        // Update household with official head and total members
        $officialHead = collect($members)->firstWhere('is_primary_head', true);
        $household->update([
            'official_head_id' => $officialHead->id,
            'total_members' => count($members),
        ]);

        // Update primary family with sub-head
        $primaryFamily->update([
            'sub_head_resident_id' => $officialHead->id,
        ]);

        // Update purok counts
        $purok->increment('total_households');
        $purok->increment('total_population', count($members));
    }

    private function createNuclearFamily($household, $subFamily, $childrenCount = 2)
    {
        $members = [];

        // Father (Head)
        $father = $this->createResident($household, $subFamily, [
            'sex' => 'male',
            'age' => rand(35, 50),
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'civil_status' => 'married',
            'occupation' => $this->occupations[array_rand($this->occupations)],
            'monthly_income' => rand(8000, 25000),
        ]);
        $members[] = $father;

        // Mother (Spouse)
        $mother = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => $father->age - rand(0, 5),
            'household_role' => 'spouse',
            'civil_status' => 'married',
            'last_name' => $father->last_name,
            'occupation' => rand(0, 1) ? 'Housewife' : $this->occupations[array_rand($this->occupations)],
            'monthly_income' => rand(0, 15000),
        ]);
        $members[] = $mother;

        // Children
        for ($i = 0; $i < $childrenCount; $i++) {
            $childAge = rand(3, 18);
            $child = $this->createResident($household, $subFamily, [
                'sex' => rand(0, 1) ? 'male' : 'female',
                'age' => $childAge,
                'household_role' => 'child',
                'civil_status' => 'single',
                'last_name' => $father->last_name,
                'occupation' => $childAge >= 15 ? 'Student' : 'None',
                'monthly_income' => 0,
                'educational_attainment' => $this->getEducationByAge($childAge),
            ]);
            $members[] = $child;
        }

        return $members;
    }

    private function createExtendedFamily($household, $subFamily)
    {
        $members = $this->createNuclearFamily($household, $subFamily, 2);

        $father = $members[0];

        // Grandfather
        $grandfather = $this->createResident($household, $subFamily, [
            'sex' => 'male',
            'age' => rand(65, 80),
            'household_role' => 'parent',
            'civil_status' => 'married',
            'last_name' => $father->last_name,
            'occupation' => 'Retired',
            'monthly_income' => 0,
            'is_senior_citizen' => true,
            'senior_id' => 'SC-'.rand(100000, 999999),
        ]);
        $members[] = $grandfather;

        // Grandmother
        $grandmother = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => $grandfather->age - rand(0, 5),
            'household_role' => 'parent',
            'civil_status' => 'married',
            'last_name' => $father->last_name,
            'occupation' => 'Retired',
            'monthly_income' => 0,
            'is_senior_citizen' => true,
            'senior_id' => 'SC-'.rand(100000, 999999),
        ]);
        $members[] = $grandmother;

        return $members;
    }

    private function createYoungCouple($household, $subFamily)
    {
        $members = [];

        // Husband
        $husband = $this->createResident($household, $subFamily, [
            'sex' => 'male',
            'age' => rand(25, 32),
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'civil_status' => 'married',
            'monthly_income' => rand(12000, 20000),
        ]);
        $members[] = $husband;

        // Wife
        $wife = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => $husband->age - rand(0, 3),
            'household_role' => 'spouse',
            'civil_status' => 'married',
            'last_name' => $husband->last_name,
            'monthly_income' => rand(8000, 15000),
        ]);
        $members[] = $wife;

        // Baby
        $baby = $this->createResident($household, $subFamily, [
            'sex' => rand(0, 1) ? 'male' : 'female',
            'age' => rand(0, 2),
            'household_role' => 'child',
            'civil_status' => 'single',
            'last_name' => $husband->last_name,
            'occupation' => 'None',
            'monthly_income' => 0,
            'educational_attainment' => 'no formal education',
        ]);
        $members[] = $baby;

        return $members;
    }

    private function createSingleParent($household, $subFamily)
    {
        $members = [];

        // Single parent (mother)
        $mother = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => rand(30, 45),
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'civil_status' => rand(0, 1) ? 'widowed' : 'separated',
            'monthly_income' => rand(6000, 12000),
        ]);
        $members[] = $mother;

        // Children (2-3)
        $childrenCount = rand(2, 3);
        for ($i = 0; $i < $childrenCount; $i++) {
            $childAge = rand(5, 16);
            $child = $this->createResident($household, $subFamily, [
                'sex' => rand(0, 1) ? 'male' : 'female',
                'age' => $childAge,
                'household_role' => 'child',
                'civil_status' => 'single',
                'last_name' => $mother->last_name,
                'occupation' => $childAge >= 15 ? 'Student' : 'None',
                'monthly_income' => 0,
                'educational_attainment' => $this->getEducationByAge($childAge),
            ]);
            $members[] = $child;
        }

        return $members;
    }

    private function createLargeExtendedFamily($household, $subFamily)
    {
        $members = $this->createExtendedFamily($household, $subFamily);

        $father = $members[0];

        // Add married child with spouse
        $marriedChild = $this->createResident($household, $subFamily, [
            'sex' => 'male',
            'age' => rand(25, 30),
            'household_role' => 'child',
            'civil_status' => 'married',
            'last_name' => $father->last_name,
            'monthly_income' => rand(10000, 18000),
        ]);
        $members[] = $marriedChild;

        // Spouse of married child
        $childSpouse = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => $marriedChild->age - rand(0, 3),
            'household_role' => 'relative',
            'civil_status' => 'married',
            'monthly_income' => rand(8000, 15000),
        ]);
        $members[] = $childSpouse;

        // Grandchild
        $grandchild = $this->createResident($household, $subFamily, [
            'sex' => rand(0, 1) ? 'male' : 'female',
            'age' => rand(1, 5),
            'household_role' => 'relative',
            'civil_status' => 'single',
            'last_name' => $marriedChild->last_name,
            'occupation' => 'None',
            'monthly_income' => 0,
            'educational_attainment' => 'no formal education',
        ]);
        $members[] = $grandchild;

        return $members;
    }

    private function createSeniorCouple($household, $subFamily)
    {
        $members = [];

        // Husband
        $husband = $this->createResident($household, $subFamily, [
            'sex' => 'male',
            'age' => rand(65, 78),
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'civil_status' => 'married',
            'occupation' => 'Retired',
            'monthly_income' => 0,
            'is_senior_citizen' => true,
            'senior_id' => 'SC-'.rand(100000, 999999),
        ]);
        $members[] = $husband;

        // Wife
        $wife = $this->createResident($household, $subFamily, [
            'sex' => 'female',
            'age' => $husband->age - rand(0, 5),
            'household_role' => 'spouse',
            'civil_status' => 'married',
            'last_name' => $husband->last_name,
            'occupation' => 'Retired',
            'monthly_income' => 0,
            'is_senior_citizen' => true,
            'senior_id' => 'SC-'.rand(100000, 999999),
        ]);
        $members[] = $wife;

        return $members;
    }

    private function createFamilyWithPWD($household, $subFamily)
    {
        $members = $this->createNuclearFamily($household, $subFamily, 3);

        // Make one child PWD
        $pwdChild = $members[3]; // One of the children
        $pwdChild->update([
            'is_pwd' => true,
            'pwd_id' => 'PWD-'.rand(100000, 999999),
            'disability_type' => ['Physical Disability', 'Visual Impairment', 'Hearing Impairment', 'Intellectual Disability'][rand(0, 3)],
        ]);

        return $members;
    }

    private function createOFWFamily($household, $subFamily)
    {
        $members = [];

        // OFW (Head)
        $ofw = $this->createResident($household, $subFamily, [
            'sex' => rand(0, 1) ? 'male' : 'female',
            'age' => rand(32, 48),
            'household_role' => 'head',
            'is_household_head' => true,
            'is_primary_head' => true,
            'civil_status' => 'married',
            'occupation' => 'OFW',
            'employment_status' => 'employed',
            'employment_type' => 'employed_abroad_ofw',
            'ofw_country' => ['Saudi Arabia', 'UAE', 'Singapore', 'Hong Kong', 'Taiwan'][rand(0, 4)],
            'ofw_occupation' => ['Domestic Helper', 'Nurse', 'Engineer', 'Construction Worker', 'Factory Worker'][rand(0, 4)],
            'monthly_income' => rand(30000, 60000),
            'status' => 'active',
        ]);
        $members[] = $ofw;

        // Spouse (staying in PH)
        $spouse = $this->createResident($household, $subFamily, [
            'sex' => $ofw->sex === 'male' ? 'female' : 'male',
            'age' => $ofw->age - rand(0, 5),
            'household_role' => 'spouse',
            'civil_status' => 'married',
            'last_name' => $ofw->last_name,
            'monthly_income' => rand(5000, 12000),
        ]);
        $members[] = $spouse;

        // Children
        for ($i = 0; $i < rand(2, 3); $i++) {
            $childAge = rand(5, 17);
            $child = $this->createResident($household, $subFamily, [
                'sex' => rand(0, 1) ? 'male' : 'female',
                'age' => $childAge,
                'household_role' => 'child',
                'civil_status' => 'single',
                'last_name' => $ofw->last_name,
                'occupation' => $childAge >= 15 ? 'Student' : 'None',
                'monthly_income' => 0,
                'educational_attainment' => $this->getEducationByAge($childAge),
            ]);
            $members[] = $child;
        }

        return $members;
    }

    private function create4PsFamily($household, $subFamily)
    {
        $members = $this->createNuclearFamily($household, $subFamily, 3);

        // Mark all children as 4Ps beneficiaries
        foreach ($members as $member) {
            if ($member->household_role === 'child') {
                $member->update([
                    'is_4ps_beneficiary' => true,
                    '4ps_id' => '4PS-'.rand(1000000, 9999999),
                ]);
            }
        }

        return $members;
    }

    private function createMultiGenerational($household, $subFamily)
    {
        return $this->createLargeExtendedFamily($household, $subFamily);
    }

    private function createResident($household, $subFamily, $attributes = [])
    {
        $residentId = 'RES-2025-'.str_pad($this->residentCounter++, 4, '0', STR_PAD_LEFT);

        $sex = $attributes['sex'] ?? (rand(0, 1) ? 'male' : 'female');
        $age = $attributes['age'] ?? rand(20, 60);
        $birthdate = Carbon::now()->subYears($age)->subDays(rand(0, 364));

        $firstName = $this->firstNames[$sex][array_rand($this->firstNames[$sex])];
        $lastName = $attributes['last_name'] ?? $this->lastNames[array_rand($this->lastNames)];

        $defaults = [
            'resident_id' => $residentId,
            'household_id' => $household->id,
            'sub_family_id' => $subFamily->id,
            'first_name' => $firstName,
            'middle_name' => $this->lastNames[array_rand($this->lastNames)],
            'last_name' => $lastName,
            'suffix' => rand(0, 20) === 0 ? ['Jr.', 'Sr.', 'III'][rand(0, 2)] : null,
            'birthdate' => $birthdate,
            'age' => $age,
            'sex' => $sex,
            'civil_status' => $attributes['civil_status'] ?? 'single',
            'place_of_birth' => 'Davao City',
            'nationality' => 'Filipino',
            'religion' => $this->religions[array_rand($this->religions)],
            'contact_number' => '09'.rand(100000000, 999999999),
            'email' => strtolower($firstName.'.'.$lastName.'@email.com'),
            'household_role' => $attributes['household_role'] ?? 'relative',
            'is_household_head' => $attributes['is_household_head'] ?? false,
            'is_primary_head' => $attributes['is_primary_head'] ?? false,
            'status' => $attributes['status'] ?? 'active',
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'is_pwd' => $attributes['is_pwd'] ?? false,
            'pwd_id' => $attributes['pwd_id'] ?? null,
            'disability_type' => $attributes['disability_type'] ?? null,
            'is_senior_citizen' => $age >= 60,
            'senior_id' => $age >= 60 ? ($attributes['senior_id'] ?? 'SC-'.rand(100000, 999999)) : null,
            'is_teen' => $age >= 13 && $age <= 19,
            'is_voter' => $age >= 18 ? (rand(0, 10) > 2) : false, // 80% of adults are voters
            'precinct_number' => ($age >= 18 && rand(0, 10) > 2) ? 'PCT-'.rand(1000, 9999) : null,
            'is_4ps_beneficiary' => $attributes['is_4ps_beneficiary'] ?? false,
            '4ps_id' => $attributes['4ps_id'] ?? null,
            'occupation' => $attributes['occupation'] ?? ($age >= 18 ? $this->occupations[array_rand($this->occupations)] : 'None'),
            'employment_status' => $attributes['employment_status'] ?? $this->getEmploymentStatus($age),
            'employment_type' => $attributes['employment_type'] ?? null,
            'ofw_country' => $attributes['ofw_country'] ?? null,
            'ofw_occupation' => $attributes['ofw_occupation'] ?? null,
            'employer_name' => null,
            'monthly_income' => $attributes['monthly_income'] ?? ($age >= 18 ? rand(0, 20000) : 0),
            'educational_attainment' => $attributes['educational_attainment'] ?? $this->getEducationByAge($age),
            'blood_type' => ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'][rand(0, 7)],
            'medical_conditions' => rand(0, 10) === 0 ? ['Hypertension', 'Diabetes', 'Asthma', 'Heart Disease'][rand(0, 3)] : null,
            'remarks' => null,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => $household->created_at,
            'updated_at' => now(),
        ];

        $data = array_merge($defaults, $attributes);

        return Resident::create($data);
    }

    private function randomHousingType()
    {
        $types = ['owned', 'owned', 'owned', 'rented', 'rent-free']; // 60% owned

        return $types[array_rand($types)];
    }

    private function getEmploymentStatus($age)
    {
        if ($age < 18) {
            return null;
        }
        if ($age >= 60) {
            return 'retired';
        }

        $statuses = ['employed', 'employed', 'unemployed', 'self-employed', 'student'];

        return $statuses[array_rand($statuses)];
    }

    private function getEducationByAge($age)
    {
        if ($age < 6) {
            return 'no formal education';
        }
        if ($age < 12) {
            return 'elementary level';
        }
        if ($age < 16) {
            return 'high school level';
        }
        if ($age < 18) {
            return 'high school graduate';
        }
        if ($age < 22) {
            return rand(0, 1) ? 'college level' : 'high school graduate';
        }

        $educations = ['high school graduate', 'college level', 'college graduate', 'vocational'];

        return $educations[array_rand($educations)];
    }
}
