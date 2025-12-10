<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Household;
use App\Models\HouseholdEvent;
use App\Models\Resident;
use App\Models\ResidentTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResidentTransferController extends Controller
{
    /**
     * Display a listing of transfers
     */
    public function index(Request $request)
    {
        $query = ResidentTransfer::with(['resident', 'oldHousehold', 'newHousehold', 'creator', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by transfer type
        if ($request->filled('type')) {
            $query->where('transfer_type', $request->type);
        }

        // Search by resident name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('resident', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by reason
        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        // Filter by processed by
        if ($request->filled('processed_by')) {
            $query->where('processed_by', $request->processed_by);
        }

        // Filter by from purok
        if ($request->filled('from_purok')) {
            $query->whereHas('oldHousehold', function ($q) use ($request) {
                $q->whereHas('purok', function ($q) use ($request) {
                    $q->where('purok_name', $request->from_purok);
                });
            });
        }

        // Load relationships including soft-deleted residents
        $transfers = $query->with([
            'resident' => function ($q) {
                $q->withTrashed();
            },
            'oldHousehold.purok',
            'newHousehold.purok',
            'creator',
            'approver',
        ])->latest()->paginate(20)->appends($request->except('page'));

        // Get staff for filter dropdown
        $staff = \App\Models\User::where('role', '!=', 'resident')->get(['id', 'name']);

        return view('resident-transfers.index', compact('transfers', 'staff'));
    }

    /**
     * Show pending transfers (Secretary only)
     */
    public function pending()
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'This action requires Secretary privileges.');
        }

        $pendingTransfers = ResidentTransfer::with([
            'resident' => function ($q) {
                $q->withTrashed();
            },
            'oldHousehold',
            'newHousehold',
            'creator',
        ])
            ->pending()
            ->latest()
            ->paginate(20);

        return view('resident-transfers.pending', compact('pendingTransfers'));
    }

    /**
     * Show the form for creating a new transfer
     */
    public function create(Request $request)
    {
        $resident = null;
        if ($request->filled('resident_id')) {
            $resident = Resident::with('household')->findOrFail($request->resident_id);
        }

        $residents = Resident::approved()->active()
            ->with('household')
            ->orderBy('last_name')
            ->get()
            ->filter(function ($r) {
                return $r->household !== null;
            })
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'label' => "{$r->full_name} - {$r->household->household_id}",
                ];
            })
            ->values();

        $households = Household::approved()
            ->with(['subFamilies' => function ($query) {
                $query->where('is_primary_family', true)->with('subHead');
            }])
            ->get()
            ->map(function ($h) {
                $primaryFamily = $h->subFamilies->first();
                $headName = $primaryFamily && $primaryFamily->subHead
                    ? $primaryFamily->subHead->full_name
                    : 'No Head';

                return [
                    'id' => $h->id,
                    'label' => "{$h->household_id} - {$headName} (Purok {$h->purok})",
                ];
            });

        // Get unique street addresses and address-to-purok mapping
        $addressPurokMap = Household::approved()
            ->get()
            ->mapWithKeys(function ($h) {
                $parts = explode(',', $h->address);
                $address = trim($parts[0]);
                $purok = $h->purok;

                return $address ? [$address => $purok] : [];
            })
            ->toArray();

        $streetAddresses = array_keys($addressPurokMap);

        return view('resident-transfers.create', compact('residents', 'households', 'resident', 'streetAddresses', 'addressPurokMap'));
    }

    /**
     * Store a newly created transfer request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'transfer_type' => 'required|in:internal,external',
            'internal_transfer_type' => 'required_if:transfer_type,internal|nullable|in:join_existing,create_new',
            'new_household_id' => 'required_if:internal_transfer_type,join_existing|nullable|exists:households,id',
            'sub_family_id' => 'required_if:internal_transfer_type,join_existing|nullable|exists:sub_families,id',
            'new_purok' => 'required_if:internal_transfer_type,create_new|nullable|string|max:100',
            'new_address' => 'required_if:internal_transfer_type,create_new|nullable|string|max:255',
            'new_household_head_option' => 'required_if:internal_transfer_type,create_new|nullable|in:self,existing_resident,new_person',
            'new_household_head_id' => 'required_if:new_household_head_option,existing_resident|nullable|exists:residents,id',
            'destination_address' => 'required_if:transfer_type,external|nullable|string',
            'destination_barangay' => 'nullable|string|max:255',
            'destination_municipality' => 'nullable|string|max:255',
            'destination_province' => 'nullable|string|max:255',
            'reason' => 'required|in:work,marriage,school,family,health,other',
            'reason_for_transfer' => 'required|string',
            'transfer_date' => 'required|date',
        ]);

        // Get resident's current household
        $resident = Resident::with('household')->findOrFail($validated['resident_id']);

        // Check if resident has a household
        if (! $resident->household) {
            return back()->withErrors(['resident_id' => 'Resident not assigned to a household.'])->withInput();
        }

        $validated['old_household_id'] = $resident->household_id;
        $validated['old_purok'] = $resident->household->purok;

        // Map transfer_type: 'internal'/'external' -> 'transfer_in'/'transfer_out'
        if ($validated['transfer_type'] === 'external') {
            $validated['transfer_type'] = 'transfer_out';
        } else {
            $validated['transfer_type'] = 'transfer_in';
        }

        // Set initial status and creator
        $validated['created_by'] = auth()->id();

        // Always mark as pending on submission (approval required)
        $validated['status'] = 'pending';

        $transfer = ResidentTransfer::create($validated);

        AuditLog::logAction(
            'create',
            'ResidentTransfer',
            $transfer->id,
            "Transfer request created for {$resident->full_name}"
        );

        return redirect()->route(auth()->user()->isSecretary() ? 'resident-transfers.show' : 'staff.resident-transfers.show', $transfer)
            ->with('success', 'Transfer request submitted successfully! Pending approval from Secretary.');
    }

    /**
     * Display the specified transfer
     */
    public function show(ResidentTransfer $residentTransfer)
    {
        // Load relationships including soft-deleted residents (for external transfers)
        $residentTransfer->load([
            'resident' => function ($query) {
                $query->withTrashed(); // Include soft-deleted residents
            },
            'oldHousehold',
            'newHousehold',
            'creator',
            'approver',
        ]);

        return view('resident-transfers.show', compact('residentTransfer'));
    }

    /**
     * Approve a transfer request (Secretary only)
     */
    public function approve(ResidentTransfer $residentTransfer)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'This action requires Secretary privileges.');
        }

        if ($residentTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be approved.');
        }

        DB::transaction(function () use ($residentTransfer) {
            // Update transfer status
            $residentTransfer->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Process the transfer
            $this->processTransfer($residentTransfer);

            AuditLog::logAction(
                'approve',
                'ResidentTransfer',
                $residentTransfer->id,
                "Transfer approved for {$residentTransfer->resident->full_name}"
            );
        });

        return redirect()->route('resident-transfers.show', $residentTransfer)
            ->with('success', 'Transfer approved and processed successfully!');
    }

    /**
     * Reject a transfer request (Secretary only)
     */
    public function reject(Request $request, ResidentTransfer $residentTransfer)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'This action requires Secretary privileges.');
        }

        if ($residentTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be rejected.');
        }

        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        $residentTransfer->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'remarks' => $validated['remarks'],
        ]);

        AuditLog::logAction(
            'reject',
            'ResidentTransfer',
            $residentTransfer->id,
            "Transfer rejected for {$residentTransfer->resident->full_name}"
        );

        return redirect()->route('resident-transfers.show', $residentTransfer)
            ->with('success', 'Transfer request rejected.');
    }

    /**
     * Process the approved transfer
     */
    protected function processTransfer(ResidentTransfer $transfer)
    {
        $resident = $transfer->resident;
        $oldHousehold = $transfer->oldHousehold;

        if ($transfer->transfer_type === 'transfer_in') {
            // Internal transfer within Matina Pangi

            if ($transfer->internal_transfer_type === 'create_new') {
                // CREATE NEW HOUSEHOLD SCENARIO

                // Generate new household ID
                $lastHousehold = Household::orderBy('household_id', 'desc')->first();
                $lastNumber = $lastHousehold ? intval(substr($lastHousehold->household_id, 2)) : 0;
                $newHouseholdId = 'HH'.str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                // Create full address
                $fullAddress = trim($transfer->new_address).', '.$transfer->new_purok.', Matina Pangi, Davao City';

                // Create new household
                $newHousehold = Household::create([
                    'household_id' => $newHouseholdId,
                    'address' => $fullAddress,
                    'purok' => $transfer->new_purok,
                    'total_members' => 0, // Will be updated based on who gets added
                    'household_type' => 'family',
                    'created_by' => auth()->id(),
                ]);

                // Determine who will be the head based on user selection
                $headOption = $transfer->new_household_head_option ?? 'self';
                $householdHead = null;
                $memberCount = 0;

                if ($headOption === 'self') {
                    // OPTION 1: Transferring resident becomes the head
                    $householdHead = $resident;

                    // Create primary family with resident as head
                    $primaryFamily = \App\Models\SubFamily::create([
                        'household_id' => $newHousehold->id,
                        'sub_head_id' => $resident->id,
                        'is_primary_family' => true,
                        'sub_family_name' => 'Primary Family',
                    ]);

                    // Update resident to be the head
                    $resident->update([
                        'household_id' => $newHousehold->id,
                        'sub_family_id' => $primaryFamily->id,
                        'is_household_head' => true,
                        'is_primary_head' => true,
                        'household_role' => 'head',
                    ]);

                    $memberCount = 1;
                    $description = "New household created. {$resident->full_name} (from household {$oldHousehold->household_id}) became the household head.";

                } elseif ($headOption === 'existing_resident' && $transfer->new_household_head_id) {
                    // OPTION 2: Another existing resident becomes the head
                    $householdHead = Resident::find($transfer->new_household_head_id);
                    $headOldHousehold = $householdHead->household;

                    // Create primary family with the selected resident as head
                    $primaryFamily = \App\Models\SubFamily::create([
                        'household_id' => $newHousehold->id,
                        'sub_head_id' => $householdHead->id,
                        'is_primary_family' => true,
                        'sub_family_name' => 'Primary Family',
                    ]);

                    // Update the head resident
                    $householdHead->update([
                        'household_id' => $newHousehold->id,
                        'sub_family_id' => $primaryFamily->id,
                        'is_household_head' => true,
                        'is_primary_head' => true,
                        'household_role' => 'head',
                    ]);

                    // Update the transferring resident as a member
                    $resident->update([
                        'household_id' => $newHousehold->id,
                        'sub_family_id' => $primaryFamily->id,
                        'is_household_head' => false,
                        'is_primary_head' => false,
                        // household_role stays as is or set to appropriate role
                    ]);

                    $memberCount = 2;

                    // Create event for head's old household if different
                    if ($headOldHousehold && $headOldHousehold->id != $oldHousehold->id) {
                        HouseholdEvent::create([
                            'household_id' => $headOldHousehold->id,
                            'event_type' => 'member_removed',
                            'description' => "Resident {$householdHead->full_name} transferred to new household {$newHousehold->household_id} as the household head",
                            'reason' => 'transfer',
                            'event_date' => $transfer->transfer_date,
                            'processed_by' => auth()->id(),
                        ]);
                        $headOldHousehold->update(['total_members' => $headOldHousehold->residents()->count()]);
                    }

                    $description = "New household created. {$householdHead->full_name} became the household head. {$resident->full_name} transferred from household {$oldHousehold->household_id} as a member.";

                } elseif ($headOption === 'new_person') {
                    // OPTION 3: A new person (not yet registered) will be the head
                    // For now, create household with transferring resident temporarily
                    // The system should require registering the new person first

                    $primaryFamily = \App\Models\SubFamily::create([
                        'household_id' => $newHousehold->id,
                        'sub_head_id' => null, // No head yet
                        'is_primary_family' => true,
                        'sub_family_name' => 'Primary Family',
                    ]);

                    // Add transferring resident as member (not head)
                    $resident->update([
                        'household_id' => $newHousehold->id,
                        'sub_family_id' => $primaryFamily->id,
                        'is_household_head' => false,
                        'is_primary_head' => false,
                        // household_role stays as is
                    ]);

                    $memberCount = 1;
                    $description = "New household created. {$resident->full_name} transferred from household {$oldHousehold->household_id}. Household head to be registered.";
                }

                // Update household member count
                $newHousehold->update(['total_members' => $memberCount]);

                // Update transfer record
                $transfer->update([
                    'new_household_id' => $newHousehold->id,
                ]);

                // Create household event for old household
                HouseholdEvent::create([
                    'household_id' => $oldHousehold->id,
                    'event_type' => 'member_removed',
                    'description' => "Resident {$resident->full_name} transferred to new household {$newHousehold->household_id} at {$fullAddress}",
                    'reason' => 'transfer',
                    'event_date' => $transfer->transfer_date,
                    'processed_by' => auth()->id(),
                ]);

                // Create household event for new household
                HouseholdEvent::create([
                    'household_id' => $newHousehold->id,
                    'event_type' => 'new_family_created',
                    'description' => $description,
                    'reason' => 'transfer',
                    'event_date' => $transfer->transfer_date,
                    'processed_by' => auth()->id(),
                ]);

                // Update old household member count
                $oldHousehold->update(['total_members' => $oldHousehold->residents()->count()]);

            } else {
                // JOIN EXISTING HOUSEHOLD SCENARIO (original logic)
                $newHousehold = $transfer->newHousehold;

                // Create household event for old household
                HouseholdEvent::create([
                    'household_id' => $oldHousehold->id,
                    'event_type' => 'member_removed',
                    'description' => "Resident {$resident->full_name} transferred to household {$newHousehold->household_id}",
                    'reason' => 'transfer', // Map to valid ENUM value
                    'event_date' => $transfer->transfer_date,
                    'processed_by' => auth()->id(),
                ]);

                // Get the selected sub-family or default to primary family
                $subFamilyId = $transfer->sub_family_id;

                if (! $subFamilyId) {
                    // Fallback to primary family if not specified (backward compatibility)
                    $primaryFamily = $newHousehold->subFamilies()->where('is_primary_family', true)->first();
                    if (! $primaryFamily) {
                        throw new \Exception('New household has no primary family. Cannot transfer resident.');
                    }
                    $subFamilyId = $primaryFamily->id;
                }

                $selectedFamily = \App\Models\SubFamily::find($subFamilyId);

                // Update resident's household and assign to selected family
                $resident->update([
                    'household_id' => $newHousehold->id,
                    'sub_family_id' => $subFamilyId,
                    'is_household_head' => false, // Transferred member is not a head
                    'is_primary_head' => false,
                ]);

                // Create household event for new household
                $familyLabel = $selectedFamily->is_primary_family ? 'primary head' : "co-head ({$selectedFamily->sub_family_name})";
                HouseholdEvent::create([
                    'household_id' => $newHousehold->id,
                    'event_type' => 'member_added',
                    'description' => "Resident {$resident->full_name} transferred from household {$oldHousehold->household_id}. Assigned to {$familyLabel}'s family.",
                    'reason' => 'transfer', // Map to valid ENUM value
                    'event_date' => $transfer->transfer_date,
                    'processed_by' => auth()->id(),
                ]);

                // Update household member counts
                $oldHousehold->update(['total_members' => $oldHousehold->residents()->count()]);
                $newHousehold->update(['total_members' => $newHousehold->residents()->count()]);
            }

        } elseif ($transfer->transfer_type === 'transfer_out') {
            // External transfer - moving out of Matina Pangi
            HouseholdEvent::create([
                'household_id' => $oldHousehold->id,
                'event_type' => 'relocation',
                'description' => "Resident {$resident->full_name} relocated to {$transfer->destination_barangay}, {$transfer->destination_municipality}",
                'reason' => 'transfer', // Map to valid ENUM value
                'event_date' => $transfer->transfer_date,
                'processed_by' => auth()->id(),
            ]);

            // Mark resident as relocated and archive
            $resident->update([
                'status' => 'reallocated',
            ]);
            $resident->delete(); // Soft delete

            // Update household member count
            $oldHousehold->update(['total_members' => $oldHousehold->residents()->count()]);
        }

        // Mark transfer as completed
        $transfer->update(['status' => 'completed']);
    }

    /**
     * Remove the specified transfer
     */
    public function destroy(ResidentTransfer $residentTransfer)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'This action requires Secretary privileges.');
        }

        if ($residentTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be deleted.');
        }

        $residentTransfer->delete();

        return redirect()->route('resident-transfers.index')
            ->with('success', 'Transfer request deleted successfully.');
    }
}
