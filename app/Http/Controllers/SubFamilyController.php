<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Resident;
use App\Models\SubFamily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubFamilyController extends Controller
{
    /**
     * Display a listing of pending sub-families (for secretary approval)
     */
    public function index()
    {
        $subFamilies = SubFamily::with(['household', 'subHead', 'members'])
            ->pending()
            ->latest()
            ->paginate(15);

        return view('sub-families.index', compact('subFamilies'));
    }

    /**
     * Show the form for creating a new sub-family
     */
    public function create(Request $request)
    {
        // Get household if specified
        $household = null;
        if ($request->filled('household_id')) {
            $household = Household::with('officialHead')->findOrFail($request->household_id);
        }

        // Get all households with their official heads for selection
        $households = Household::with('officialHead')
            ->approved()
            ->get()
            ->map(function ($h) {
                return [
                    'id' => $h->id,
                    'label' => "{$h->household_id} - ".($h->officialHead ? $h->officialHead->full_name : 'No Head')." ({$h->address})",
                ];
            });

        return view('sub-families.create', compact('households', 'household'));
    }

    /**
     * Store a newly created sub-family
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'household_id' => 'required|exists:households,id',
            'sub_family_name' => 'required|string|max:255',

            // Sub-family head data
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
            'head.occupation' => 'nullable|string|max:255',
            'head.employment_status' => 'nullable|in:employed,unemployed,self-employed,student,retired',
            'head.monthly_income' => 'nullable|numeric|min:0',

            // Members data (optional)
            'members' => 'nullable|array',
            'members.*.first_name' => 'required|string|max:255',
            'members.*.middle_name' => 'nullable|string|max:255',
            'members.*.last_name' => 'required|string|max:255',
            'members.*.suffix' => 'nullable|string|max:50',
            'members.*.birthdate' => 'required|date|before:today',
            'members.*.sex' => 'required|in:male,female',
            'members.*.civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'members.*.household_role' => 'required|in:spouse,child,parent,sibling,relative,other',
        ]);

        DB::beginTransaction();
        try {
            $household = Household::findOrFail($validated['household_id']);

            // Auto-approve if user is secretary, otherwise pending
            $approvalStatus = auth()->user()->isSecretary() ? 'approved' : 'pending';
            $approvedBy = auth()->user()->isSecretary() ? auth()->id() : null;
            $approvedAt = auth()->user()->isSecretary() ? now() : null;

            // Create sub-family
            $subFamily = SubFamily::create([
                'sub_family_name' => $validated['sub_family_name'],
                'household_id' => $validated['household_id'],
                'is_primary_family' => false,
                'approval_status' => $approvalStatus,
                'approved_by' => $approvedBy,
                'approved_at' => $approvedAt,
            ]);

            // Create sub-family head
            $headData = $validated['head'];
            $headData['household_id'] = $household->id;
            $headData['sub_family_id'] = $subFamily->id;
            $headData['household_role'] = 'head';
            $headData['is_household_head'] = true;
            $headData['is_primary_head'] = false;
            $headData['status'] = 'active';
            $headData['approval_status'] = $approvalStatus;
            $headData['approved_by'] = $approvedBy;
            $headData['approved_at'] = $approvedAt;
            $headData['created_by'] = auth()->id();
            $headData['updated_by'] = auth()->id();

            $head = Resident::create($headData);

            // Update sub-family with head reference
            $subFamily->update(['sub_head_resident_id' => $head->id]);

            // Create members if any
            if (! empty($validated['members'])) {
                foreach ($validated['members'] as $memberData) {
                    $memberData['household_id'] = $household->id;
                    $memberData['sub_family_id'] = $subFamily->id;
                    $memberData['is_household_head'] = false;
                    $memberData['is_primary_head'] = false;
                    $memberData['status'] = 'active';
                    $memberData['approval_status'] = $approvalStatus;
                    $memberData['approved_by'] = $approvedBy;
                    $memberData['approved_at'] = $approvedAt;
                    $memberData['created_by'] = auth()->id();
                    $memberData['updated_by'] = auth()->id();

                    Resident::create($memberData);
                }
            }

            // Update household total members
            $household->total_members = $household->residents()->count();
            $household->save();

            AuditLog::logAction(
                'create',
                'SubFamily',
                $subFamily->id,
                "Created sub-family: {$subFamily->sub_family_name} in household {$household->household_id}",
                null,
                $validated
            );

            DB::commit();

            $message = auth()->user()->isSecretary()
                ? 'Sub-family created and approved successfully!'
                : 'Sub-family created! Waiting for secretary approval.';

            return redirect()->route(auth()->user()->isStaff() ? 'staff.households.show' : 'households.show', $household)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('SubFamily create failed', ['error' => $e->getMessage()]);
            DB::rollBack();

            return back()->withInput()->with('error', 'Failed to create sub-family: '.$e->getMessage());
        }
    }

    /**
     * Approve a sub-family
     */
    public function approve(SubFamily $subFamily)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only secretary can approve sub-families.');
        }

        DB::beginTransaction();
        try {
            $subFamily->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Approve all members of this sub-family
            $subFamily->members()->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            AuditLog::logAction(
                'approve',
                'SubFamily',
                $subFamily->id,
                "Approved sub-family: {$subFamily->sub_family_name}",
                ['approval_status' => 'pending'],
                ['approval_status' => 'approved']
            );

            DB::commit();

            return back()->with('success', 'Sub-family approved successfully!');

        } catch (\Exception $e) {
            Log::error('SubFamily approve failed', ['id' => $subFamily->id, 'error' => $e->getMessage()]);
            DB::rollBack();

            return back()->with('error', 'Failed to approve sub-family: '.$e->getMessage());
        }
    }

    /**
     * Reject a sub-family
     */
    public function reject(Request $request, SubFamily $subFamily)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only secretary can reject sub-families.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $subFamily->update([
                'approval_status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            AuditLog::logAction(
                'reject',
                'SubFamily',
                $subFamily->id,
                "Rejected sub-family: {$subFamily->sub_family_name}",
                ['approval_status' => 'pending'],
                ['approval_status' => 'rejected', 'rejection_reason' => $validated['rejection_reason']]
            );

            DB::commit();

            return back()->with('success', 'Sub-family rejected.');

        } catch (\Exception $e) {
            Log::error('SubFamily reject failed', ['id' => $subFamily->id, 'error' => $e->getMessage()]);
            DB::rollBack();

            return back()->with('error', 'Failed to reject sub-family: '.$e->getMessage());
        }
    }

    /**
     * Delete a sub-family
     */
    public function destroy(SubFamily $subFamily)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Only secretary can delete sub-families.');
        }

        DB::beginTransaction();
        try {
            // Soft delete all members
            $subFamily->members()->delete();

            // Delete sub-family
            $subFamily->delete();

            AuditLog::logAction(
                'delete',
                'SubFamily',
                $subFamily->id,
                "Deleted sub-family: {$subFamily->sub_family_name}",
                $subFamily->toArray(),
                null
            );

            DB::commit();

            return back()->with('success', 'Sub-family deleted successfully!');

        } catch (\Exception $e) {
            Log::error('SubFamily delete failed', ['id' => $subFamily->id, 'error' => $e->getMessage()]);
            DB::rollBack();

            return back()->with('error', 'Failed to delete sub-family: '.$e->getMessage());
        }
    }
}
