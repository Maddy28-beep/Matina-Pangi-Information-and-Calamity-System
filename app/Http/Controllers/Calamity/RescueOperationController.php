<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\RescueOperationResource;
use App\Models\RescueOperation;
use Illuminate\Http\Request;

class RescueOperationController extends Controller
{
    public function index(Request $request)
    {
        $query = RescueOperation::with(['affectedHousehold.household', 'rescuer', 'evacuationCenter'])->latest('rescue_time');
        if ($request->filled('calamity_id')) {
            $query->whereHas('affectedHousehold', function ($q) use ($request) {
                $q->where('calamity_id', $request->calamity_id);
            });
        }
        if ($request->filled('household_id')) {
            $query->whereHas('affectedHousehold', function ($q) use ($request) {
                $q->where('household_id', $request->household_id);
            });
        }

        return RescueOperationResource::collection($query->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_affected_household_id' => 'required|exists:calamity_affected_households,id',
            'rescuer_type' => 'nullable|in:response_team_member,ambulance_team,other',
            'rescuer_id' => 'nullable|exists:response_team_members,id',
            'rescue_time' => 'required|date',
            'evacuation_center_id' => 'nullable|exists:evacuation_centers,id',
            'ambulance_vehicle' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $model = RescueOperation::create($data);
        if ($request->expectsJson()) {
            return new RescueOperationResource($model->load(['affectedHousehold.household', 'rescuer', 'evacuationCenter']));
        }

        return redirect()->back()->with('success', 'Rescue operation recorded successfully.');
    }

    public function show(RescueOperation $rescue_operation)
    {
        return new RescueOperationResource($rescue_operation->load(['affectedHousehold.household', 'rescuer', 'evacuationCenter']));
    }

    public function update(Request $request, RescueOperation $rescue_operation)
    {
        $data = $request->validate([
            'rescuer_type' => 'nullable|in:response_team_member,ambulance_team,other',
            'rescuer_id' => 'nullable|exists:response_team_members,id',
            'rescue_time' => 'nullable|date',
            'evacuation_center_id' => 'nullable|exists:evacuation_centers,id',
            'ambulance_vehicle' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $rescue_operation->update($data);

        return new RescueOperationResource($rescue_operation->load(['affectedHousehold.household', 'rescuer', 'evacuationCenter']));
    }

    public function destroy(RescueOperation $rescue_operation)
    {
        $rescue_operation->delete();

        return response()->json(null, 204);
    }
}
