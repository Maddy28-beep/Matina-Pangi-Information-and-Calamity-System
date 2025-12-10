<?php

use App\Http\Controllers\Calamity\CalamityAffectedHouseholdController;
use App\Http\Controllers\Calamity\CalamityIncidentController;
use App\Http\Controllers\Calamity\CalamityReportController;
use App\Http\Controllers\Calamity\DamageAssessmentController;
use App\Http\Controllers\Calamity\EvacuationCenterController;
use App\Http\Controllers\Calamity\NotificationController;
use App\Http\Controllers\Calamity\ReliefDistributionController;
use App\Http\Controllers\Calamity\ReliefItemController;
use App\Http\Controllers\Calamity\RescueOperationController;
use App\Http\Controllers\Calamity\ResponseTeamMemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Global search endpoint
    Route::get('/global-search', function (Request $request) {
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
    });

    Route::middleware('role:calamity_head')->group(function () {
        Route::apiResource('calamities', CalamityIncidentController::class);
        Route::post('calamity-incidents', [CalamityIncidentController::class, 'store']);
        Route::put('calamity-incidents/{calamity}', [CalamityIncidentController::class, 'update']);
        Route::post('calamity-incidents/{calamity}/photos', [CalamityIncidentController::class, 'uploadPhotos']);
        Route::delete('calamity-incidents/{calamity}/photos/{photoName}', [CalamityIncidentController::class, 'deletePhoto']);
        Route::apiResource('calamity-affected-households', CalamityAffectedHouseholdController::class);
        Route::apiResource('evacuation-centers', EvacuationCenterController::class);
        Route::apiResource('relief-items', ReliefItemController::class);
        Route::apiResource('relief-distributions', ReliefDistributionController::class);
        Route::apiResource('damage-assessments', DamageAssessmentController::class);
        Route::apiResource('notifications', NotificationController::class);
        Route::apiResource('response-team-members', ResponseTeamMemberController::class);
        Route::apiResource('calamity-reports', CalamityReportController::class);
        Route::apiResource('rescue-operations', RescueOperationController::class);
    });
});

// Web-accessible API endpoints (require web session authentication)
Route::middleware('web')->group(function () {
    // Get sub-families for a household
    Route::get('/households/{household}/sub-families', function ($householdId) {
        $household = \App\Models\Household::findOrFail($householdId);
        $subFamilies = $household->subFamilies()->with('subHead')->get();

        return response()->json($subFamilies->map(function ($family) {
            return [
                'id' => $family->id,
                'sub_family_name' => $family->sub_family_name,
                'is_primary_family' => $family->is_primary_family,
                'head_name' => $family->subHead ? $family->subHead->full_name : 'N/A',
            ];
        }));
    });
});
