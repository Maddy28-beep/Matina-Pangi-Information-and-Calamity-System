<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\ResidentTransfer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ApprovalController extends Controller
{
    /**
     * Show pending approvals dashboard
     */
    public function index()
    {
        // Only secretary can access
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $pendingResidents = Resident::with(['household', 'creator'])
            ->pending()
            ->latest()
            ->get();

        $pendingHouseholds = Household::with(['head', 'residents', 'head.creator'])
            ->pending()
            ->latest()
            ->get();

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
            ->get();

        $items = collect();

        $pendingCertificates = CertificateRequest::with(['resident'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        foreach ($pendingResidents as $r) {
            $items->push([
                'type' => 'resident',
                'model' => $r,
                'created_at' => $r->created_at,
                'updated_at' => $r->updated_at,
                'requester' => optional($r->creator)->name,
            ]);
        }

        foreach ($pendingHouseholds as $h) {
            $items->push([
                'type' => 'household',
                'model' => $h,
                'created_at' => $h->created_at,
                'updated_at' => $h->updated_at,
                'requester' => optional(optional($h->head)->creator)->name,
            ]);
        }

        foreach ($pendingTransfers as $t) {
            $items->push([
                'type' => 'transfer',
                'model' => $t,
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
                'requester' => optional($t->creator)->name,
            ]);
        }

        foreach ($pendingCertificates as $c) {
            $items->push([
                'type' => 'certificate',
                'model' => $c,
                'created_at' => $c->created_at,
                'updated_at' => $c->updated_at,
                'requester' => optional($c->resident)->full_name,
            ]);
        }

        $items = $items->sortByDesc('created_at')->values();

        $page = (int) request()->get('page', 1);
        $perPage = 20;
        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $pending = new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('approvals.index', compact('pending'));
    }

    /**
     * Approve a resident
     */
    public function approveResident(Resident $resident)
    {
        // Only secretary can approve
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $resident->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        AuditLog::logAction(
            'approve',
            'Resident',
            $resident->id,
            "Approved resident: {$resident->full_name}"
        );

        return back()->with('success', 'Resident approved successfully!');
    }

    /**
     * Reject a resident
     */
    public function rejectResident(Request $request, Resident $resident)
    {
        // Only secretary can reject
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $resident->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Archive rejected resident
        $resident->delete();

        AuditLog::logAction(
            'reject',
            'Resident',
            $resident->id,
            "Rejected and archived resident: {$resident->full_name}",
            null,
            ['rejection_reason' => $request->rejection_reason]
        );

        return back()->with('success', 'Resident rejected and archived.');
    }

    /**
     * Approve a household
     */
    public function approveHousehold(Household $household)
    {
        // Only secretary can approve
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $household->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        // Also approve all residents in the household
        $household->residents()->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        AuditLog::logAction(
            'approve',
            'Household',
            $household->id,
            "Approved household: {$household->household_id}"
        );

        return back()->with('success', 'Household and all members approved successfully!');
    }

    /**
     * Reject a household
     */
    public function rejectHousehold(Request $request, Household $household)
    {
        // Only secretary can reject
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $household->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Archive rejected household (will cascade to residents)
        $household->delete();

        AuditLog::logAction(
            'reject',
            'Household',
            $household->id,
            "Rejected and archived household: {$household->household_id}",
            null,
            ['rejection_reason' => $request->rejection_reason]
        );

        return back()->with('success', 'Household rejected and archived.');
    }

    /**
     * Change resident status
     */
    public function changeResidentStatus(Request $request, Resident $resident)
    {
        // Only secretary can change status
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:active,reallocated,deceased',
            'status_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $resident->status;

        $resident->update([
            'status' => $request->status,
            'status_notes' => $request->status_notes,
            'status_changed_at' => now(),
            'status_changed_by' => auth()->id(),
        ]);

        AuditLog::logAction(
            'status_change',
            'Resident',
            $resident->id,
            "Changed resident status from {$oldStatus} to {$request->status}: {$resident->full_name}",
            ['status' => $oldStatus],
            ['status' => $request->status, 'notes' => $request->status_notes]
        );

        return back()->with('success', 'Resident status updated successfully!');
    }

    /**
     * Archive a resident (move to global archives)
     */
    public function archiveResident(Resident $resident)
    {
        // Only secretary can archive
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $residentName = $resident->full_name;

        // Use the Archivable trait to archive
        $resident->archive('Archived by '.auth()->user()->name);

        AuditLog::logAction(
            'archive',
            'Resident',
            $resident->id,
            "Archived resident: {$residentName}"
        );

        return back()->with('success', 'Resident archived successfully!');
    }

    /**
     * Archive a household (move to global archives)
     */
    public function archiveHousehold(Household $household)
    {
        // Only secretary can archive
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $householdId = $household->household_id;

        // Use the Archivable trait to archive
        $household->archive('Archived by '.auth()->user()->name);

        AuditLog::logAction(
            'archive',
            'Household',
            $household->id,
            "Archived household: {$householdId}"
        );

        return back()->with('success', 'Household archived successfully!');
    }

    /**
     * Show archived records
     */
    public function archived()
    {
        // Only secretary can view archived
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $archivedResidents = Resident::onlyTrashed()
            ->with(['household' => function ($query) {
                $query->withTrashed();
            }])
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'residents');

        $archivedHouseholds = Household::onlyTrashed()
            ->with(['head' => function ($query) {
                $query->withTrashed();
            }])
            ->latest('deleted_at')
            ->paginate(15, ['*'], 'households');

        return view('approvals.archived', compact('archivedResidents', 'archivedHouseholds'));
    }

    public function approveCertificate(CertificateRequest $certificateRequest)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $certificateRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $cert = Certificate::create([
            'resident_id' => $certificateRequest->resident_id,
            'certificate_type' => $certificateRequest->certificate_type,
            'purpose' => $certificateRequest->purpose,
            'or_number' => null,
            'amount_paid' => 0,
            'issued_by' => auth()->id(),
            'issued_date' => now(),
            'valid_until' => null,
            'status' => 'issued',
            'remarks' => $certificateRequest->notes,
        ]);

        AuditLog::logAction(
            'approve',
            'CertificateRequest',
            $certificateRequest->id,
            'Approved certificate request and issued certificate',
            null,
            ['certificate_id' => $cert->id]
        );

        return redirect()->route('certificates.show', $cert)
            ->with('success', 'Certificate request approved and certificate issued.');
    }

    public function rejectCertificate(Request $request, CertificateRequest $certificateRequest)
    {
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $certificateRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->rejection_reason,
        ]);

        AuditLog::logAction(
            'reject',
            'CertificateRequest',
            $certificateRequest->id,
            'Rejected certificate request',
            null,
            ['rejection_reason' => $request->rejection_reason]
        );

        return back()->with('success', 'Certificate request rejected.');
    }

    /**
     * Restore archived resident
     */
    public function restoreResident($id)
    {
        // Only secretary can restore
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $resident = Resident::onlyTrashed()->findOrFail($id);
        $resident->restore();

        AuditLog::logAction(
            'restore',
            'Resident',
            $resident->id,
            "Restored resident: {$resident->full_name}"
        );

        return back()->with('success', 'Resident restored successfully!');
    }

    /**
     * Restore archived household
     */
    public function restoreHousehold($id)
    {
        // Only secretary can restore
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $household = Household::onlyTrashed()->findOrFail($id);
        $household->restore();

        // Also restore all residents in the household
        Resident::onlyTrashed()
            ->where('household_id', $household->id)
            ->restore();

        AuditLog::logAction(
            'restore',
            'Household',
            $household->id,
            "Restored household: {$household->household_id}"
        );

        return back()->with('success', 'Household and all members restored successfully!');
    }

    /**
     * Permanently delete a resident
     */
    public function deleteResident($id)
    {
        // Only secretary can permanently delete
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $resident = Resident::onlyTrashed()->findOrFail($id);
        $residentName = $resident->full_name;

        // Permanently delete (forceDelete)
        $resident->forceDelete();

        AuditLog::logAction(
            'force_delete',
            'Resident',
            $id,
            "Permanently deleted resident: {$residentName}"
        );

        return back()->with('success', 'Resident permanently deleted!');
    }

    /**
     * Permanently delete a household
     */
    public function deleteHousehold($id)
    {
        // Only secretary can permanently delete
        if (! auth()->user()->isSecretary()) {
            abort(403, 'Unauthorized action.');
        }

        $household = Household::onlyTrashed()->findOrFail($id);
        $householdId = $household->household_id;

        // Permanently delete all residents in the household first
        Resident::onlyTrashed()
            ->where('household_id', $household->id)
            ->forceDelete();

        // Then permanently delete the household
        $household->forceDelete();

        AuditLog::logAction(
            'force_delete',
            'Household',
            $id,
            "Permanently deleted household and all members: {$householdId}"
        );

        return back()->with('success', 'Household and all members permanently deleted!');
    }
}
