<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Purok;
use App\Models\Resident;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    /**
     * Show the form to add members to a household after head registration (transfer workflow)
     */
    public function addMembers($household_id)
    {
        $household = Household::with('subFamilies', 'subFamilies.subHead')->findOrFail($household_id);

        return view('residents.add-members', compact('household'));
    }

    /**
     * Store the newly added members for a household (transfer workflow)
     */
    public function storeMembers(Request $request, $household_id)
    {
        $household = Household::findOrFail($household_id);
        $members = $request->input('members', []);
        foreach ($members as $member) {
            $member['household_id'] = $household->id;
            $member['created_by'] = auth()->id();
            $member['updated_by'] = auth()->id();
            Resident::create($member);
        }

        return redirect()->route(auth()->user()->isStaff() ? 'staff.households.show' : 'households.show', $household)
            ->with('success', 'Household members added successfully!');
    }

    /**
     * Display a listing of residents
     */
    public function index(Request $request)
    {
        $query = Resident::with('household')->approved();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('resident_id', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            switch ($request->category) {
                case 'pwd':
                    $query->pwd();
                    break;
                case 'senior':
                    $query->seniorCitizens();
                    break;
                case 'teen':
                    $query->teens();
                    break;
                case 'voter':
                    $query->voters();
                    break;
                case '4ps':
                    $query->where('is_4ps_beneficiary', true);
                    break;
                case 'head':
                    $query->householdHeads();
                    break;
            }
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by civil status
        if ($request->filled('civil_status')) {
            $query->where('civil_status', $request->civil_status);
        }

        // Filter by age range
        if ($request->filled('age_from')) {
            $query->where('age', '>=', $request->age_from);
        }
        if ($request->filled('age_to')) {
            $query->where('age', '<=', $request->age_to);
        }

        // Filter by purok
        if ($request->filled('purok')) {
            $query->where('purok', $request->purok);
        }

        // Filter by employment status
        if ($request->filled('employment')) {
            $query->where('employment_status', $request->employment);
        }

        $residents = $query->latest()->paginate(15)->appends($request->except('page'));
        $purokOptions = Purok::orderBy('purok_name')->pluck('purok_name', 'purok_name')->toArray();

        return view('residents.index', compact('residents', 'purokOptions'));
    }

    /**
     * Show the form for creating a new resident
     */
    public function create()
    {
        // Only Secretary can create standalone residents
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Staff cannot register residents independently. Use household registration instead.');
        }

        $households = Household::with('head')->get();
        $address = null;
        $purok = null;

        if (request()->has('household_id')) {
            $household = Household::find(request()->household_id);
            if ($household) {
                $address = $household->address;
                $purok = $household->purok;
            }
        }

        return view('residents.create', compact('households', 'address', 'purok'));
    }

    /**
     * Store a newly created resident
     */
    public function store(Request $request)
    {
        // Only Secretary can create standalone residents
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Staff cannot register residents independently. Use household registration instead.');
        }

        $validated = $request->validate([
            'household_id' => 'required|exists:households,id',
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
            'household_role' => 'required|in:head,spouse,child,parent,sibling,relative,other',
            'is_household_head' => 'boolean',
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
            'status' => 'nullable|in:active,reallocated,deceased',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        // Secretary always auto-approves when creating residents directly
        $validated['approval_status'] = 'approved';
        $validated['approved_at'] = now();
        $validated['approved_by'] = auth()->id();

        if (isset($validated['status']) && $validated['status'] === 'deceased') {
            $validated['status_changed_at'] = now();
            $validated['status_changed_by'] = auth()->id();
        }
        $resident = Resident::create($validated);

        AuditLog::logAction(
            'create',
            'Resident',
            $resident->id,
            "Created resident: {$resident->full_name}",
            null,
            $validated
        );

        // If there are members to add, redirect to member registration form
        if ($request->has('add_members') && $request->input('add_members') == 1) {
            // Pass household_id to the member registration route
            return redirect()->route('residents.add-members', ['household_id' => $resident->household_id])
                ->with('success', 'Head registered. Please add household members.');
        }

        return redirect()->route('residents.show', $resident)
            ->with('success', 'Resident registered successfully!');
    }

    /**
     * Display the specified resident
     */
    public function show(Resident $resident)
    {
        $resident->load('household', 'creator', 'updater');

        return view('residents.show', compact('resident'));
    }

    /**
     * Show the form for editing the specified resident
     */
    public function edit(Resident $resident)
    {
        // Only secretary can edit
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $households = Household::with('head')->get();

        return view('residents.edit', compact('resident', 'households'));
    }

    /**
     * Update the specified resident
     */
    public function update(Request $request, Resident $resident)
    {
        // Only secretary can update
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $oldValues = $resident->toArray();

        $validated = $request->validate([
            'household_id' => 'required|exists:households,id',
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
            'household_role' => 'required|in:head,spouse,child,parent,sibling,relative,other',
            'is_household_head' => 'boolean',
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
            'status' => 'nullable|in:active,reallocated,deceased',
        ]);

        $validated['updated_by'] = auth()->id();

        if (isset($validated['status']) && $validated['status'] !== $resident->status) {
            $validated['status_changed_at'] = now();
            $validated['status_changed_by'] = auth()->id();
        }
        $resident->update($validated);

        AuditLog::logAction(
            'update',
            'Resident',
            $resident->id,
            "Updated resident: {$resident->full_name}",
            $oldValues,
            $validated
        );

        return redirect()->route('residents.show', $resident)
            ->with('success', 'Resident updated successfully!');
    }

    /**
     * Remove the specified resident
     */
    public function destroy(Resident $resident)
    {
        // Only secretary can delete
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $residentName = $resident->full_name;
        $residentId = $resident->id;

        $resident->delete();

        AuditLog::logAction(
            'delete',
            'Resident',
            $residentId,
            "Deleted resident: {$residentName}"
        );

        return redirect()->route('residents.index')
            ->with('success', 'Resident deleted successfully!');
    }
}
