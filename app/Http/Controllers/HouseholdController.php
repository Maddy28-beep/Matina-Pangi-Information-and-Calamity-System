<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HouseholdController extends Controller
{
    /**
     * Display a listing of households
     */
    public function index(Request $request)
    {
        $query = Household::with(['officialHead', 'head', 'residents', 'purok']);

        // Search by household ID or address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('household_id', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Filter by primary head name
        if ($request->filled('head_name')) {
            $headName = $request->head_name;
            $query->whereHas('officialHead', function ($q) use ($headName) {
                $q->where('first_name', 'like', "%{$headName}%")
                    ->orWhere('last_name', 'like', "%{$headName}%");
            });
        }

        // Filter by purok
        if ($request->filled('purok_id')) {
            $query->where('purok_id', $request->purok_id);
        }

        // Filter by beneficiary type
        if ($request->filled('beneficiary_type')) {
            switch ($request->beneficiary_type) {
                case 'pwd':
                    $query->whereHas('residents', function ($q) {
                        $q->where('is_pwd', true);
                    });
                    break;
                case '4ps':
                    $query->whereHas('residents', function ($q) {
                        $q->where('is_4ps_beneficiary', true);
                    });
                    break;
                case 'senior':
                    $query->whereHas('residents', function ($q) {
                        $q->where('is_senior_citizen', true);
                    });
                    break;
                case 'teen':
                    $query->whereHas('residents', function ($q) {
                        $q->where('is_teen', true);
                    });
                    break;
            }
        }

        // Filter by household type
        if ($request->filled('type')) {
            $query->where('household_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by member count range
        if ($request->filled('member_count_min')) {
            $query->having('total_members', '>=', $request->member_count_min);
        }
        if ($request->filled('member_count_max')) {
            $query->having('total_members', '<=', $request->member_count_max);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Add member count to query
        $query->withCount('residents as total_members');

        $households = $query->latest()->paginate(15)->appends($request->except('page'));

        // Get puroks for filter dropdown
        $puroks = \App\Models\Purok::orderBy('purok_code')->get();

        return view('households.index', compact('households', 'puroks'));
    }

    /**
     * Show the form for creating a new household
     */
    public function create()
    {
        $households = Household::with('head')->get(); // For parent household selection

        // Get distinct puroks and addresses for dropdowns
        $puroks = [
            'Purok 1',
            'Purok 2',
            'Purok 3',
            'Purok 4',
            'Purok 5',
            'Purok 6',
            'Purok 7',
            'Purok 8',
            'Purok 9',
            'Purok 10',
        ];

        // Extract unique street addresses (first part before comma)
        $addressPurokMap = Household::approved()
            ->get()
            ->mapWithKeys(function ($household) {
                $parts = explode(',', $household->address);
                $address = trim($parts[0]);
                $purok = $household->purok;

                return $address ? [$address => $purok] : [];
            })
            ->toArray();

        $addresses = array_keys($addressPurokMap);

        return view('households.create', compact('households', 'puroks', 'addresses', 'addressPurokMap'));
    }

    /**
     * Store a newly created household with head and members
     */
    public function store(Request $request)
    {
        // Validate household data
        $validated = $request->validate([
            'household_type' => 'required|in:solo,family,extended',
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'housing_type' => 'required|in:owned,rented,rent-free',
            'has_electricity' => 'boolean',
            'electric_account_number' => 'nullable|string|max:100',
            'total_members' => 'required|integer|min:1',
            'parent_household_id' => 'nullable|exists:households,id',

            // Household head data
            'head.first_name' => 'required|string|max:255',
            'head.middle_name' => 'nullable|string|max:255',
            'head.last_name' => 'required|string|max:255',
            'head.suffix' => 'nullable|string|max:50',
            'head.birthdate' => 'required|date|before:today',
            'head.sex' => 'required|in:male,female',
            'head.civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'head.place_of_birth' => 'nullable|string|max:255',
            'head.nationality' => 'nullable|string|max:100',
            'head.religion' => 'nullable|string|max:100',
            'head.contact_number' => 'nullable|string|max:20',
            'head.email' => 'nullable|email|max:255',
            'head.is_pwd' => 'boolean',
            'head.pwd_id' => 'nullable|string|max:100',
            'head.disability_type' => 'nullable|string|max:255',
            'head.is_voter' => 'boolean',
            'head.precinct_number' => 'nullable|string|max:50',
            'head.is_4ps_beneficiary' => 'boolean',
            'head.4ps_id' => 'nullable|string|max:100',
            'head.occupation' => 'nullable|string|max:255',
            'head.employment_status' => 'nullable|in:employed,unemployed,self-employed,student,retired',
            'head.employer_name' => 'nullable|string|max:255',
            'head.monthly_income' => 'nullable|numeric|min:0',
            'head.educational_attainment' => 'nullable|string',
            'head.blood_type' => 'nullable|string|max:10',
            'head.medical_conditions' => 'nullable|string',
            'head.remarks' => 'nullable|string',

            // Members data (dynamic)
            'members' => 'nullable|array',
            'members.*.first_name' => 'required|string|max:255',
            'members.*.middle_name' => 'nullable|string|max:255',
            'members.*.last_name' => 'required|string|max:255',
            'members.*.suffix' => 'nullable|string|max:50',
            'members.*.birthdate' => 'required|date|before:today',
            'members.*.sex' => 'required|in:male,female',
            'members.*.civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'members.*.place_of_birth' => 'nullable|string|max:255',
            'members.*.nationality' => 'nullable|string|max:100',
            'members.*.religion' => 'nullable|string|max:100',
            'members.*.contact_number' => 'nullable|string|max:20',
            'members.*.email' => 'nullable|email|max:255',
            'members.*.household_role' => 'required|in:spouse,child,parent,sibling,relative,other',
            'members.*.is_pwd' => 'boolean',
            'members.*.pwd_id' => 'nullable|string|max:100',
            'members.*.disability_type' => 'nullable|string|max:255',
            'members.*.is_voter' => 'boolean',
            'members.*.precinct_number' => 'nullable|string|max:50',
            'members.*.is_4ps_beneficiary' => 'boolean',
            'members.*.4ps_id' => 'nullable|string|max:100',
            'members.*.occupation' => 'nullable|string|max:255',
            'members.*.employment_status' => 'nullable|in:employed,unemployed,self-employed,student,retired',
            'members.*.employer_name' => 'nullable|string|max:255',
            'members.*.monthly_income' => 'nullable|numeric|min:0',
            'members.*.educational_attainment' => 'nullable|string',
            'members.*.blood_type' => 'nullable|string|max:10',
            'members.*.medical_conditions' => 'nullable|string',
            'members.*.remarks' => 'nullable|string',
        ]);
        $headDuplicate = Resident::query()
            ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($validated['head']['first_name']))])
            ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($validated['head']['last_name']))])
            ->whereRaw('LOWER(COALESCE(middle_name, "")) = ?', [strtolower(trim($validated['head']['middle_name'] ?? ''))])
            ->whereRaw('LOWER(COALESCE(suffix, "")) = ?', [strtolower(trim($validated['head']['suffix'] ?? ''))])
            ->exists();

        if ($headDuplicate) {
            $n = $validated['head'];
            $label = trim($n['first_name'].' '.($n['middle_name'] ?? '').' '.$n['last_name'].' '.($n['suffix'] ?? ''));

            return back()->withInput()->withErrors(['error' => "Resident '{$label}' already exists in the system."]);
        }

        if (isset($validated['members']) && count($validated['members']) > 0) {
            foreach ($validated['members'] as $member) {
                $memberExists = Resident::query()
                    ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($member['first_name']))])
                    ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($member['last_name']))])
                    ->whereRaw('LOWER(COALESCE(middle_name, "")) = ?', [strtolower(trim($member['middle_name'] ?? ''))])
                    ->whereRaw('LOWER(COALESCE(suffix, "")) = ?', [strtolower(trim($member['suffix'] ?? ''))])
                    ->exists();

                if ($memberExists) {
                    $label = trim($member['first_name'].' '.($member['middle_name'] ?? '').' '.$member['last_name'].' '.($member['suffix'] ?? ''));

                    return back()->withInput()->withErrors(['error' => "Resident '{$label}' already exists in the system."]);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Check if this is an Extended Family registration
            if ($validated['household_type'] === 'extended' && isset($validated['parent_household_id'])) {
                // Extended Family: Add to existing parent household
                $household = Household::findOrFail($validated['parent_household_id']);

                // Create new sub-family (extended family group) for this household
                $extendedFamily = \App\Models\SubFamily::create([
                    'sub_family_name' => $validated['head']['last_name'].' Extended Family',
                    'household_id' => $household->id,
                    'is_primary_family' => false, // This is an extended/co-head family
                    'approval_status' => $household->approval_status,
                    'approved_by' => $household->approved_by,
                    'approved_at' => $household->approved_at,
                ]);

                // Create extended family head (co-head)
                $headData = $validated['head'];
                $headData['household_id'] = $household->id;
                $headData['sub_family_id'] = $extendedFamily->id;
                $headData['household_role'] = 'head'; // Head of their sub-family
                $headData['is_household_head'] = true; // Head of sub-family
                $headData['is_primary_head'] = false; // NOT the primary household head
                $headData['created_by'] = auth()->id();
                $headData['updated_by'] = auth()->id();

                // Approval for extended-family head based on role
                if (auth()->user()->isStaff()) {
                    $headData['approval_status'] = 'pending';
                    $headData['approved_at'] = null;
                    $headData['approved_by'] = null;
                } else {
                    $headData['approval_status'] = 'approved';
                    $headData['approved_at'] = now();
                    $headData['approved_by'] = auth()->id();
                }
                $head = Resident::create($headData);

                // Update sub-family with head reference
                $extendedFamily->update(['sub_head_resident_id' => $head->id]);

                // Create members of extended family if any
                if (isset($validated['members']) && count($validated['members']) > 0) {
                    foreach ($validated['members'] as $memberData) {
                        $memberData['household_id'] = $household->id;
                        $memberData['sub_family_id'] = $extendedFamily->id;
                        $memberData['is_household_head'] = false;
                        $memberData['is_primary_head'] = false;
                        $memberData['created_by'] = auth()->id();
                        $memberData['updated_by'] = auth()->id();

                        if (auth()->user()->isStaff()) {
                            $memberData['approval_status'] = 'pending';
                            $memberData['approved_at'] = null;
                            $memberData['approved_by'] = null;
                        } else {
                            $memberData['approval_status'] = 'approved';
                            $memberData['approved_at'] = now();
                            $memberData['approved_by'] = auth()->id();
                        }
                        Resident::create($memberData);
                    }
                }

                // Update total members count
                $household->update([
                    'total_members' => $household->total_members + $validated['total_members'],
                ]);

                AuditLog::logAction(
                    'create',
                    'SubFamily',
                    $extendedFamily->id,
                    "Added extended family to household {$household->household_id} with {$validated['total_members']} member(s)",
                    null,
                    $validated
                );

            } else {
                // Solo or Family: Create new household
                $householdData = [
                    'address' => $validated['address'],
                    'purok' => $validated['purok'] ?? null,
                    'housing_type' => $validated['housing_type'],
                    'has_electricity' => $validated['has_electricity'] ?? true,
                    'electric_account_number' => $validated['electric_account_number'] ?? null,
                    'total_members' => $validated['total_members'],
                    'household_type' => $validated['household_type'],
                    'parent_household_id' => null, // No parent for solo/family
                ];

                // Approval for household: staff pending, secretary approved
                if (auth()->user()->isStaff()) {
                    $householdData['approval_status'] = 'pending';
                    $householdData['approved_at'] = null;
                    $householdData['approved_by'] = null;
                } else {
                    $householdData['approval_status'] = 'approved';
                    $householdData['approved_at'] = now();
                    $householdData['approved_by'] = auth()->id();
                }

                $household = Household::create($householdData);

                // Create primary sub-family for the official household head
                $primaryFamily = \App\Models\SubFamily::create([
                    'sub_family_name' => 'Primary Family',
                    'household_id' => $household->id,
                    'is_primary_family' => true,
                    'approval_status' => $household->approval_status,
                    'approved_by' => $household->approved_by,
                    'approved_at' => $household->approved_at,
                ]);

                // Create household head (official/primary head)
                $headData = $validated['head'];
                $headData['household_id'] = $household->id;
                $headData['sub_family_id'] = $primaryFamily->id;
                $headData['household_role'] = 'head';
                $headData['is_household_head'] = true;
                $headData['is_primary_head'] = true; // Mark as official barangay-recognized head
                $headData['created_by'] = auth()->id();
                $headData['updated_by'] = auth()->id();

                // Approval for head and members follow household approval
                if (auth()->user()->isStaff()) {
                    $headData['approval_status'] = 'pending';
                    $headData['approved_at'] = null;
                    $headData['approved_by'] = null;
                } else {
                    $headData['approval_status'] = 'approved';
                    $headData['approved_at'] = now();
                    $headData['approved_by'] = auth()->id();
                }
                $head = Resident::create($headData);

                // Update household and sub-family with official head reference
                $household->update(['official_head_id' => $head->id]);
                $primaryFamily->update(['sub_head_resident_id' => $head->id]);

                // Create members if any
                if (isset($validated['members']) && count($validated['members']) > 0) {
                    foreach ($validated['members'] as $memberData) {
                        $memberData['household_id'] = $household->id;
                        $memberData['sub_family_id'] = $primaryFamily->id;
                        $memberData['is_household_head'] = false;
                        $memberData['is_primary_head'] = false;
                        $memberData['created_by'] = auth()->id();
                        $memberData['updated_by'] = auth()->id();

                        if (auth()->user()->isStaff()) {
                            $memberData['approval_status'] = 'pending';
                            $memberData['approved_at'] = null;
                            $memberData['approved_by'] = null;
                        } else {
                            $memberData['approval_status'] = 'approved';
                            $memberData['approved_at'] = now();
                            $memberData['approved_by'] = auth()->id();
                        }
                        Resident::create($memberData);
                    }
                }

                AuditLog::logAction(
                    'create',
                    'Household',
                    $household->id,
                    "Created household {$household->household_id} with {$validated['total_members']} member(s)",
                    null,
                    $validated
                );
            }

            DB::commit();

            $message = auth()->user()->isStaff()
                ? 'Household registered successfully! Pending secretary approval.'
                : 'Household registered successfully!';

            return redirect()->route(auth()->user()->isStaff() ? 'staff.households.show' : 'households.show', $household)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->withErrors(['error' => 'Failed to register household: '.$e->getMessage()]);
        }
    }

    /**
     * Display the specified household with unified view of all sub-families
     */
    public function show(Household $household)
    {
        // Load all relationships including sub-families
        $household->load([
            'officialHead',
            'head',
            'residents',
            'subFamilies.subHead',
            'subFamilies.members',
            'parentHousehold',
            'childHouseholds',
            'purok',
        ]);

        // Get primary family (official head's family)
        $primaryFamily = $household->subFamilies()->where('is_primary_family', true)->first();
        $primaryMembers = $primaryFamily ? $primaryFamily->members()->withTrashed()->orderBy('age', 'desc')->get() : collect();

        // Get extended families (co-heads and their members)
        $extendedFamilies = $household->subFamilies()
            ->where('is_primary_family', false)
            ->with([
                'subHead',
                'members' => function ($query) {
                    $query->withTrashed()->orderBy('is_co_head', 'desc')->orderBy('age', 'desc');
                },
            ])
            ->get();

        // Calculate statistics for each family group
        $primaryStats = [
            'total' => $primaryMembers->count(),
            'seniors' => $primaryMembers->where('is_senior_citizen', true)->count(),
            'teens' => $primaryMembers->where('is_teen', true)->count(),
            'pwd' => $primaryMembers->where('is_pwd', true)->count(),
            'four_ps' => $primaryMembers->where('is_4ps_beneficiary', true)->count(),
            'voters' => $primaryMembers->where('is_voter', true)->count(),
            'active' => $primaryMembers->where('status', 'active')->count(),
            'reallocated' => $primaryMembers->where('status', 'reallocated')->count(),
            'deceased' => $primaryMembers->where('status', 'deceased')->count(),
        ];

        // Overall household statistics
        $statistics = $household->statistics;

        return view('households.show', compact(
            'household',
            'statistics',
            'primaryFamily',
            'primaryMembers',
            'primaryStats',
            'extendedFamilies'
        ));
    }

    /**
     * Show the form for editing the specified household
     */
    public function edit(Household $household)
    {
        // Only secretary can edit
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $household->load('residents');
        $households = Household::where('id', '!=', $household->id)->with('head')->get();

        // Get distinct puroks and addresses for dropdowns
        $puroks = [
            'Purok 1',
            'Purok 2',
            'Purok 3',
            'Purok 4',
            'Purok 5',
            'Purok 6',
            'Purok 7',
            'Purok 8',
            'Purok 9',
            'Purok 10',
        ];

        // Extract unique street addresses (first part before comma)
        $addresses = Household::approved()
            ->get()
            ->map(function ($household) {
                $parts = explode(',', $household->address);

                return trim($parts[0]);
            })
            ->unique()
            ->filter()
            ->sort()
            ->values();

        return view('households.edit', compact('household', 'households', 'puroks', 'addresses'));
    }

    /**
     * Update the specified household
     */
    public function update(Request $request, Household $household)
    {
        // Only secretary can update
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $oldValues = $household->toArray();

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'purok' => 'nullable|string|max:100',
            'housing_type' => 'required|in:owned,rented,rent-free',
            'has_electricity' => 'boolean',
            'electric_account_number' => 'nullable|string|max:100',
            'household_type' => 'required|in:solo,family,extended',
            'parent_household_id' => 'nullable|exists:households,id',
        ]);

        // Update total members based on actual residents
        $validated['total_members'] = $household->residents()->count();

        $household->update($validated);

        AuditLog::logAction(
            'update',
            'Household',
            $household->id,
            "Updated household {$household->household_id}",
            $oldValues,
            $validated
        );

        return redirect()->route('households.show', $household)
            ->with('success', 'Household updated successfully!');
    }

    /**
     * Archive the specified household (move to global archives)
     */
    public function destroy(Household $household)
    {
        // Only secretary can archive
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $householdId = $household->household_id;
        $householdDbId = $household->id;

        // Use the Archivable trait to archive (this will also cascade delete residents)
        $household->archive('Archived by '.auth()->user()->name);

        AuditLog::logAction(
            'archive',
            'Household',
            $householdDbId,
            "Archived household {$householdId}"
        );

        return redirect()->route('households.index')
            ->with('success', 'Household archived successfully!');
    }

    /**
     * Add a new member to existing household
     */
    public function addMember(Household $household)
    {
        // Secretary and Staff can add members
        if (! auth()->user()->isSecretary() && ! auth()->user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        // Load subfamilies with their sub heads
        $household->load(['subFamilies.subHead']);

        // Build address-to-purok mapping from all approved households
        $addressPurokMap = \App\Models\Household::approved()
            ->get()
            ->mapWithKeys(function ($h) {
                $parts = explode(',', $h->address);
                $address = trim($parts[0]);
                $purok = $h->purok;

                return [$address => $purok];
            })
            ->filter()
            ->toArray();

        // Also get unique addresses for the dropdown
        $addresses = array_keys($addressPurokMap);

        return view('households.add-member', compact('household', 'addresses', 'addressPurokMap'));
    }

    /**
     * Store new member to household
     */
    public function storeMember(Request $request, Household $household)
    {
        // Secretary and Staff can add members
        if (! auth()->user()->isSecretary() && ! auth()->user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'sub_family_id' => 'required|exists:sub_families,id',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'birthdate' => 'required|date|before:today',
            'sex' => 'required|in:male,female',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'place_of_birth' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'household_role' => 'required|in:spouse,child,parent,sibling,relative,other',
            'is_pwd' => 'boolean',
            'pwd_id' => 'nullable|string|max:100',
            'disability_type' => 'nullable|string|max:255',
            'is_voter' => 'boolean',
            'precinct_number' => 'nullable|string|max:50',
            'is_4ps_beneficiary' => 'boolean',
            '4ps_id' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:employed,unemployed,self-employed,student,retired',
            'employer_name' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|numeric|min:0',
            'educational_attainment' => 'nullable|string',
            'blood_type' => 'nullable|string|max:10',
            'medical_conditions' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $duplicate = Resident::query()
            ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($validated['first_name']))])
            ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($validated['last_name']))])
            ->whereRaw('LOWER(COALESCE(middle_name, "")) = ?', [strtolower(trim($validated['middle_name'] ?? ''))])
            ->whereRaw('LOWER(COALESCE(suffix, "")) = ?', [strtolower(trim($validated['suffix'] ?? ''))])
            ->exists();

        if ($duplicate) {
            $label = trim($validated['first_name'].' '.($validated['middle_name'] ?? '').' '.$validated['last_name'].' '.($validated['suffix'] ?? ''));

            return back()->withInput()->withErrors(['error' => "Resident '{$label}' already exists in the system."]);
        }

        // Calculate age from birthdate
        $birthdate = Carbon::parse($validated['birthdate']);
        $age = $birthdate->age;

        $validated['household_id'] = $household->id;
        $validated['age'] = $age;
        $validated['is_household_head'] = false;
        $validated['is_primary_head'] = false;
        $validated['is_co_head'] = false;
        $validated['is_senior_citizen'] = $age >= 60;
        $validated['is_teen'] = $age >= 13 && $age <= 19;
        $validated['status'] = 'active';

        // If added by staff, set as pending approval
        if (auth()->user()->isStaff()) {
            $validated['approval_status'] = 'pending';
            $validated['approved_at'] = null;
            $validated['approved_by'] = null;
        } else {
            // Secretary can directly approve
            $validated['approval_status'] = 'approved';
            $validated['approved_at'] = now();
            $validated['approved_by'] = auth()->id();
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $resident = Resident::create($validated);

        // Update household member count
        $household->update([
            'total_members' => $household->residents()->count(),
        ]);

        AuditLog::logAction(
            'create',
            'Resident',
            $resident->id,
            "Added member {$resident->full_name} to household {$household->household_id}"
        );

        // Different message based on who added the member
        if (auth()->user()->isStaff()) {
            return redirect()->route('staff.households.show', $household)
                ->with('success', 'Member added successfully! Pending approval from Secretary.');
        } else {
            return redirect()->route('households.show', $household)
                ->with('success', 'Member added and approved successfully!');
        }
    }
}
