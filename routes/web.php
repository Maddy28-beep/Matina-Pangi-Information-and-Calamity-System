<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalamityController;
use App\Http\Controllers\CensusController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\HouseholdEventController;
use App\Http\Controllers\PurokController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ResidentTransferController;
use App\Http\Controllers\SecurityAuditController;
use App\Http\Controllers\SubFamilyController;
use Illuminate\Support\Facades\Route;

//

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('welcome-enhanced');
})->name('landing');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Require Authentication)
Route::middleware(['auth', 'user_access'])->group(function () {
    // Staff-readable residents routes
    Route::get('/staff/residents', [\App\Http\Controllers\ResidentController::class, 'index'])
        ->name('staff.residents.index');
    Route::get('/staff/residents/{resident}', [\App\Http\Controllers\ResidentController::class, 'show'])
        ->name('staff.residents.show');
    // Staff registration routes (Resident registration removed per policy)
    // Staff household registration via Households module (pending approval)
    Route::get('/staff/households/create', [\App\Http\Controllers\HouseholdController::class, 'create'])
        ->name('staff.households.create');
    Route::post('/staff/households', [\App\Http\Controllers\HouseholdController::class, 'store'])
        ->name('staff.households.store');
    Route::get('/staff/households', [\App\Http\Controllers\HouseholdController::class, 'index'])
        ->name('staff.households.index');
    Route::get('/staff/households/{household}', [\App\Http\Controllers\HouseholdController::class, 'show'])
        ->name('staff.households.show');
    // Staff: My Submissions
    Route::get('/staff/my-submissions', function (\Illuminate\Http\Request $request) {
        if (! auth()->user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $from = $request->date('from');
        $to = $request->date('to');

        // Residents
        $resStatus = $request->get('res_status', 'pending');
        $residentQuery = \App\Models\Resident::with(['household'])
            ->where('created_by', auth()->id());
        if ($resStatus === 'approved') {
            $residentQuery->approved();
        } elseif ($resStatus === 'rejected') {
            $residentQuery->rejected();
        } else {
            $residentQuery->pending();
        }
        if ($from) {
            $residentQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $residentQuery->whereDate('created_at', '<=', $to);
        }
        $myResidents = $residentQuery->latest()->paginate(10, ['*'], 'residents');

        // Households (use official head creator as proxy)
        $hhStatus = $request->get('hh_status', 'pending');
        $householdQuery = \App\Models\Household::with(['officialHead', 'residents'])
            ->whereHas('officialHead', function ($q) {
                $q->where('created_by', auth()->id());
            });
        if ($hhStatus === 'approved') {
            $householdQuery->approved();
        } elseif ($hhStatus === 'rejected') {
            $householdQuery->rejected();
        } else {
            $householdQuery->pending();
        }
        if ($from) {
            $householdQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $householdQuery->whereDate('created_at', '<=', $to);
        }
        $myHouseholds = $householdQuery->latest()->paginate(10, ['*'], 'households');

        // Transfers
        $trStatus = $request->get('tr_status', 'all');
        $transferQuery = \App\Models\ResidentTransfer::with(['resident', 'oldHousehold', 'newHousehold'])
            ->where('created_by', auth()->id());
        if (in_array($trStatus, ['pending', 'approved', 'completed', 'rejected'])) {
            $transferQuery->where('status', $trStatus);
        }
        if ($from) {
            $transferQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $transferQuery->whereDate('created_at', '<=', $to);
        }
        $myTransfers = $transferQuery->orderBy('created_at', 'desc')->paginate(10, ['*'], 'transfers');

        return view('staff.my-submissions', compact('myResidents', 'myHouseholds', 'myTransfers', 'resStatus', 'hhStatus', 'trStatus', 'from', 'to'));
    })->name('staff.submissions.index');

    // Export CSV for My Submissions
    Route::get('/staff/my-submissions/export', function (\Illuminate\Http\Request $request) {
        if (! auth()->user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $section = $request->get('section', 'residents');
        $status = $request->get('status', 'pending');
        $from = $request->date('from');
        $to = $request->date('to');

        $filename = "my_submissions_{$section}_".now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $rows = [];
        if ($section === 'households') {
            $query = \App\Models\Household::with(['officialHead'])
                ->whereHas('officialHead', function ($q) {
                    $q->where('created_by', auth()->id());
                });
            if ($status === 'approved') {
                $query->approved();
            } elseif ($status === 'rejected') {
                $query->rejected();
            } else {
                $query->pending();
            }
            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
            $data = $query->latest()->get();
            $rows[] = ['Household ID', 'Address', 'Members', 'Approval Status', 'Created At'];
            foreach ($data as $h) {
                $rows[] = [$h->household_id, $h->full_address, $h->residents->count(), $h->approval_status, $h->created_at->toDateTimeString()];
            }
        } elseif ($section === 'transfers') {
            $query = \App\Models\ResidentTransfer::with(['resident'])
                ->where('created_by', auth()->id());
            if (in_array($status, ['pending', 'approved', 'completed', 'rejected'])) {
                $query->where('status', $status);
            }
            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
            $data = $query->orderBy('created_at', 'desc')->get();
            $rows[] = ['ID', 'Resident', 'Type', 'Status', 'Created At'];
            foreach ($data as $t) {
                $rows[] = [$t->id, optional($t->resident)->full_name, $t->transfer_type, $t->status, $t->created_at->toDateTimeString()];
            }
        } else { // residents
            $query = \App\Models\Resident::with(['household'])
                ->where('created_by', auth()->id());
            if ($status === 'approved') {
                $query->approved();
            } elseif ($status === 'rejected') {
                $query->rejected();
            } else {
                $query->pending();
            }
            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }
            $data = $query->latest()->get();
            $rows[] = ['Resident ID', 'Name', 'Household', 'Approval Status', 'Created At'];
            foreach ($data as $r) {
                $rows[] = [$r->resident_id, $r->full_name, optional($r->household)->household_id, $r->approval_status, $r->created_at->toDateTimeString()];
            }
        }

        $callback = function () use ($rows) {
            $FH = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    })->name('staff.submissions.export');

    Route::get('/staff/census', [CensusController::class, 'index'])->name('staff.census.index');
    Route::get('/staff/puroks', [PurokController::class, 'index'])->name('staff.puroks.index');
    Route::get('/staff/puroks/{purok}', [PurokController::class, 'show'])->name('staff.puroks.show');

    // Read-only Purok access for authenticated users (including Staff) via default path
    Route::get('/puroks', [PurokController::class, 'index'])->name('puroks.index');
    Route::get('/puroks/{purok}', [PurokController::class, 'show'])->name('puroks.show');

    Route::get('/staff/resident-transfers', [ResidentTransferController::class, 'index'])->name('staff.resident-transfers.index');
    Route::get('/staff/resident-transfers/create', [ResidentTransferController::class, 'create'])->name('staff.resident-transfers.create');
    Route::post('/staff/resident-transfers', [ResidentTransferController::class, 'store'])->name('staff.resident-transfers.store');
    Route::get('/staff/resident-transfers/{residentTransfer}', [ResidentTransferController::class, 'show'])->name('staff.resident-transfers.show');

    // Unified Dashboard (Secretary and Staff)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }
        if ($user->role === 'calamity_head') {
            return redirect()->route('calamities.dashboard');
        }
        if (! in_array($user->role, ['secretary', 'staff'])) {
            abort(403, 'This action is unauthorized.');
        }

        return app(\App\Http\Controllers\DashboardController::class)->index(request());
    })->name('dashboard');

    // Global search endpoint
    Route::get('/api/global-search', function (Request $request) {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $residents = \App\Models\Resident::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('middle_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('resident_id', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'resident_id', 'household_id']);

        $households = \App\Models\Household::where('household_id', 'LIKE', "%{$query}%")
            ->orWhere('address', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'household_id', 'address', 'total_members']);

        return response()->json([
            'residents' => $residents->map(function ($r) {
                return [
                    'id' => $r->id,
                    'full_name' => $r->first_name.' '.($r->middle_name ? $r->middle_name.' ' : '').$r->last_name,
                    'resident_id' => $r->resident_id,
                    'household_id' => $r->household_id,
                    'url' => route('residents.show', $r->id),
                ];
            }),
            'households' => $households->map(function ($h) {
                return [
                    'id' => $h->id,
                    'household_id' => $h->household_id,
                    'address' => $h->address,
                    'total_members' => $h->total_members,
                    'url' => route('households.show', $h->id),
                ];
            }),
        ]);
    })->name('global-search');

    // Global Archive Routes
    Route::get('/archives', [ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/archives/{archive}', [ArchiveController::class, 'show'])->name('archives.show');
    Route::delete('/archives/{archive}', [ArchiveController::class, 'destroy'])->name('archives.destroy');

    // Certificates Routes (Accessible to authenticated users including staff)
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::post('/certificates/{certificate}/update-status', [CertificateController::class, 'updateStatus'])->name('certificates.update-status');
    Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'generatePdf'])->name('certificates.pdf');
    Route::get('/certificates/{certificate}/print', [CertificateController::class, 'print'])->name('certificates.print');

    // Staff Certificate Requests (Staff submit for approval; Secretary reviews/issues)
    Route::post('/certificates/requests', [CertificateController::class, 'requestStore'])->name('certificates.requests.store');
    Route::get('/certificates/requests/{certificateRequest}', [CertificateController::class, 'requestShow'])->name('certificates.requests.show');

    // Non-Calamity Modules (Secretary only)
    Route::middleware('secretary')->group(function () {
        // Residents Routes
        Route::get('/residents/create', [ResidentController::class, 'create'])->name('residents.create');
        Route::post('/residents', [ResidentController::class, 'store'])->name('residents.store');
        Route::resource('residents', ResidentController::class)->except(['create', 'store']);

        // Households Routes
        Route::resource('households', HouseholdController::class);
        // Add members
        Route::get('/households/{household}/add-member', [HouseholdController::class, 'addMember'])
            ->name('households.add-member');
        Route::post('/households/{household}/add-member', [HouseholdController::class, 'storeMember'])
            ->name('households.store-member');

        // Sub-Family Routes
        Route::get('/sub-families/create', [SubFamilyController::class, 'create'])->name('sub-families.create');
        Route::post('/sub-families', [SubFamilyController::class, 'store'])->name('sub-families.store');
        // Secretary-only sub-family routes (kept within super admin)
        Route::get('/sub-families', [SubFamilyController::class, 'index'])->name('sub-families.index');
        Route::post('/sub-families/{subFamily}/approve', [SubFamilyController::class, 'approve'])->name('sub-families.approve');
        Route::post('/sub-families/{subFamily}/reject', [SubFamilyController::class, 'reject'])->name('sub-families.reject');
        Route::delete('/sub-families/{subFamily}', [SubFamilyController::class, 'destroy'])->name('sub-families.destroy');

        // Census Routes
        Route::get('/census', [CensusController::class, 'index'])->name('census.index');

        // Resident Transfer Routes
        Route::resource('resident-transfers', ResidentTransferController::class);
        Route::get('/resident-transfers-pending', [ResidentTransferController::class, 'pending'])
            ->name('resident-transfers.pending')
            ->middleware('secretary');
        Route::post('/resident-transfers/{residentTransfer}/approve', [ResidentTransferController::class, 'approve'])
            ->name('resident-transfers.approve')
            ->middleware('secretary');
        Route::post('/resident-transfers/{residentTransfer}/reject', [ResidentTransferController::class, 'reject'])
            ->name('resident-transfers.reject')
            ->middleware('secretary');

        // Household Events Routes (View only)
        Route::get('/household-events', [HouseholdEventController::class, 'index'])->name('household-events.index');
        Route::get('/household-events/{householdEvent}', [HouseholdEventController::class, 'show'])->name('household-events.show');
        Route::get('/households/{household}/events', [HouseholdEventController::class, 'byHousehold'])->name('household-events.by-household');
    });

    Route::middleware('calamity_access')->name('web.')->group(function () {
        Route::get('/calamity-affected-households', [\App\Http\Controllers\Calamity\AffectedHouseholdPageController::class, 'index'])->name('calamity-affected-households.index');
        Route::view('/calamity-affected-households/create', 'calamity.affected.create')->name('calamity-affected-households.create');
        Route::get('/calamity-affected-households/{calamity_affected_household}', [\App\Http\Controllers\Calamity\AffectedHouseholdPageController::class, 'show'])->name('calamity-affected-households.show');
        Route::post('/calamity-affected-households', [\App\Http\Controllers\Calamity\AffectedHouseholdPageController::class, 'store'])->name('calamity-affected-households.store');
        Route::post('/rescue-operations', [\App\Http\Controllers\Calamity\RescueOperationController::class, 'store'])->name('rescue-operations.store');
    });

    // Export Routes (Secretary only) and Non-Calamity Admin
    Route::middleware('secretary')->group(function () {
        // Census Exports
        Route::get('/census/export/pdf', [CensusController::class, 'exportPdf'])->name('census.export.pdf');
        Route::get('/census/export/excel', [CensusController::class, 'exportExcel'])->name('census.export.excel');

        // Residents Exports
        Route::get('/residents/export/pdf', [CensusController::class, 'exportResidentsPdf'])->name('residents.export.pdf');
        Route::get('/residents/export/excel', [CensusController::class, 'exportResidentsExcel'])->name('residents.export.excel');

        // Households Exports
        Route::get('/households/export/pdf', [CensusController::class, 'exportHouseholdsPdf'])->name('households.export.pdf');
        Route::get('/households/export/excel', [CensusController::class, 'exportHouseholdsExcel'])->name('households.export.excel');

        // Approval & Status Management Routes (Secretary Only)
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/residents/{resident}/approve', [ApprovalController::class, 'approveResident'])->name('approvals.resident.approve');
        Route::post('/approvals/residents/{resident}/reject', [ApprovalController::class, 'rejectResident'])->name('approvals.resident.reject');
        Route::post('/approvals/households/{household}/approve', [ApprovalController::class, 'approveHousehold'])->name('approvals.household.approve');
        Route::post('/approvals/households/{household}/reject', [ApprovalController::class, 'rejectHousehold'])->name('approvals.household.reject');
        Route::post('/approvals/certificates/{certificateRequest}/approve', [ApprovalController::class, 'approveCertificate'])->name('approvals.certificate.approve');
        Route::post('/approvals/certificates/{certificateRequest}/reject', [ApprovalController::class, 'rejectCertificate'])->name('approvals.certificate.reject');

        // Status Change Routes
        Route::post('/residents/{resident}/change-status', [ApprovalController::class, 'changeResidentStatus'])->name('residents.change-status');

        // Archive Routes
        Route::post('/residents/{resident}/archive', [ApprovalController::class, 'archiveResident'])->name('residents.archive');
        Route::post('/households/{household}/archive', [ApprovalController::class, 'archiveHousehold'])->name('households.archive');

        // Archived Records
        Route::get('/archived', [ApprovalController::class, 'archived'])->name('archived.index');
        Route::post('/archived/residents/{id}/restore', [ApprovalController::class, 'restoreResident'])->name('archived.resident.restore');
        Route::delete('/archived/residents/{id}/delete', [ApprovalController::class, 'deleteResident'])->name('archived.resident.delete');
        Route::post('/archived/households/{id}/restore', [ApprovalController::class, 'restoreHousehold'])->name('archived.household.restore');
        Route::delete('/archived/households/{id}/delete', [ApprovalController::class, 'deleteHousehold'])->name('archived.household.delete');

        // Purok Management Routes (Secretary Only - write ops only)
        Route::resource('puroks', PurokController::class)->except(['index', 'show']);
        Route::post('/puroks/{purok}/update-counts', [PurokController::class, 'updateCounts'])->name('puroks.update-counts');

        // Announcements
        Route::resource('announcements', \App\Http\Controllers\AnnouncementController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('/announcements/bell', [\App\Http\Controllers\AnnouncementController::class, 'bell'])->name('announcements.bell');

    });

    // Staff-accessible Census exports
    Route::get('/staff/census/export/pdf', [CensusController::class, 'exportPdf'])->name('staff.census.export.pdf');
    Route::get('/staff/census/export/excel', [CensusController::class, 'exportExcel'])->name('staff.census.export.excel');

    // Calamity Management Routes (Calamity access)
    Route::middleware('calamity_access')->group(function () {
        Route::get('/calamities/dashboard', [CalamityController::class, 'dashboard'])->name('calamities.dashboard');
        Route::get('/calamities/export', [CalamityController::class, 'export'])->name('calamities.export');
        Route::post('/calamities/import', [CalamityController::class, 'import'])->name('calamities.import');
        // Place static routes BEFORE resource to avoid collision with /calamities/{calamity}
        Route::get('/calamities/archived', [CalamityController::class, 'archived'])->name('calamities.archived');
        Route::post('/calamities/{id}/restore', [CalamityController::class, 'restore'])->name('calamities.restore');
        Route::delete('/calamities/{id}/delete', [CalamityController::class, 'forceDelete'])->name('calamities.delete');
        Route::get('/calamities/export/pdf', [CalamityController::class, 'exportPdf'])->name('calamities.export.pdf');
        Route::get('/calamities/export/excel', [CalamityController::class, 'exportExcel'])->name('calamities.export.excel');
        Route::post('/calamities/seed-samples', [CalamityController::class, 'seedSamples'])->name('calamities.seed-samples');
        Route::resource('calamities', CalamityController::class);
        Route::get('/calamities/{calamity}/add-households', [CalamityController::class, 'showAddHouseholds'])->name('calamities.add-households');
        Route::post('/calamities/{calamity}/add-household', [CalamityController::class, 'addAffectedHousehold'])->name('calamities.add-household');
        Route::get('/calamities/{calamity}/report', [CalamityController::class, 'report'])->name('calamities.report');
        Route::get('/calamities/{calamity}/report-pdf', [CalamityController::class, 'reportPdf'])->name('calamities.report-pdf');

        // Calamity Submodule Views (Web-only)
        Route::name('web.')->group(function () {
            Route::view('/evacuation-centers', 'calamity.evacuation_centers.index')->name('evacuation-centers.index');
            Route::view('/evacuation-centers/create', 'calamity.evacuation_centers.create')->name('evacuation-centers.create');
            Route::get('/relief-items', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'index'])->name('relief-items.index');
            Route::get('/relief-items/create', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'create'])->name('relief-items.create');
            Route::get('/relief-items/{relief_item}', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'show'])->name('relief-items.show');
            Route::get('/relief-items/{relief_item}/edit', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'edit'])->name('relief-items.edit');
            Route::view('/relief-distributions', 'calamity.distributions.index')->name('relief-distributions.index');
            Route::view('/relief-distributions/create', 'calamity.distributions.create')->name('relief-distributions.create');
            Route::get('/damage-assessments', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'indexBlade'])->name('damage-assessments.index');
            Route::get('/damage-assessments/create', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'createBlade'])->name('damage-assessments.create');
            Route::get('/damage-assessments/{damage_assessment}', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'showBlade'])->name('damage-assessments.show');
            Route::get('/damage-assessments/{damage_assessment}/edit', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'editBlade'])->name('damage-assessments.edit');
            Route::get('/notifications', [\App\Http\Controllers\Calamity\NotificationPageController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/create', [\App\Http\Controllers\Calamity\NotificationPageController::class, 'create'])->name('notifications.create');
            Route::get('/notifications/{notification}', [\App\Http\Controllers\Calamity\NotificationPageController::class, 'show'])->name('notifications.show');
            Route::get('/notifications/{notification}/edit', [\App\Http\Controllers\Calamity\NotificationPageController::class, 'edit'])->name('notifications.edit');
            Route::view('/response-team-members', 'calamity.response_team.index')->name('response-team-members.index');
            Route::view('/response-team-members/create', 'calamity.response_team.create')->name('response-team-members.create');
            Route::get('/response-team-members/{response_team_member}', [\App\Http\Controllers\Calamity\ResponseTeamMemberController::class, 'show'])->name('response-team-members.show');

            Route::get('/calamity-reports', [\App\Http\Controllers\Calamity\CalamityReportController::class, 'index'])->name('calamity-reports.index');
            Route::get('/calamity-reports/{calamity}', [\App\Http\Controllers\Calamity\CalamityReportController::class, 'show'])->name('calamity-reports.show');
            Route::get('/calamity-reports/{calamity}/pdf', [\App\Http\Controllers\Calamity\CalamityReportController::class, 'pdf'])->name('calamity-reports.pdf');
            Route::get('/calamity-reports/export/pdf', [\App\Http\Controllers\Calamity\CalamityReportController::class, 'exportIndexPdf'])->name('calamity-reports.export.pdf');
            Route::get('/calamity-reports/export/excel', [\App\Http\Controllers\Calamity\CalamityReportController::class, 'exportIndexExcel'])->name('calamity-reports.export.excel');
        });

        // Calamity Submissions
        Route::post('/damage-assessments', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'storeWeb'])
            ->name('web.damage-assessments.store');
        Route::put('/damage-assessments/{damage_assessment}', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'updateWeb'])
            ->name('web.damage-assessments.update');
        Route::delete('/damage-assessments/{damage_assessment}', [\App\Http\Controllers\Calamity\DamageAssessmentController::class, 'destroyWeb'])
            ->name('web.damage-assessments.destroy');
        Route::post('/notifications', [\App\Http\Controllers\Calamity\NotificationController::class, 'store'])
            ->name('web.notifications.store');
        Route::put('/notifications/{notification}', [\App\Http\Controllers\Calamity\NotificationController::class, 'update'])
            ->name('web.notifications.update');
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\Calamity\NotificationController::class, 'destroy'])
            ->name('web.notifications.destroy');
        Route::post('/response-team-members', [\App\Http\Controllers\Calamity\ResponseTeamMemberController::class, 'store'])
            ->name('web.response-team-members.store');
        Route::post('/relief-items', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'store'])
            ->name('web.relief-items.store');
        Route::put('/relief-items/{relief_item}', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'update'])
            ->name('web.relief-items.update');
        Route::delete('/relief-items/{relief_item}', [\App\Http\Controllers\Calamity\ReliefItemController::class, 'destroy'])
            ->name('web.relief-items.destroy');
    });

    // User Management (Secretary Settings)
    Route::get('/settings/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('settings.users.index');
    Route::post('/settings/users', [\App\Http\Controllers\UserManagementController::class, 'store'])
        ->name('settings.users.store')->middleware('secretary');
    Route::post('/settings/users/{user}/status', [\App\Http\Controllers\UserManagementController::class, 'updateStatus'])
        ->name('settings.users.update-status')->middleware('secretary');
    Route::post('/settings/users/{user}/assignment', [\App\Http\Controllers\UserManagementController::class, 'updateAssignment'])
        ->name('settings.users.update-assignment')->middleware('secretary');
    // MFA settings disabled

    Route::get('/security/audit', [SecurityAuditController::class, 'index'])->name('security.audit');
});
// MFA disabled
