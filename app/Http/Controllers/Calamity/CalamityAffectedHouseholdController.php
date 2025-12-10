<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\CalamityAffectedHouseholdResource;
use App\Models\CalamityAffectedHousehold;
use Illuminate\Http\Request;

class CalamityAffectedHouseholdController extends Controller
{
    public function index(Request $request)
    {
        $query = CalamityAffectedHousehold::with(['calamity', 'household', 'assessor'])->latest();
        if ($request->filled('calamity_id')) {
            $query->where('calamity_id', $request->calamity_id);
        }

        return CalamityAffectedHouseholdResource::collection($query->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_id' => 'required|exists:calamities,id',
            'household_id' => 'required|exists:households,id',
            'damage_level' => 'required|in:none,minor,moderate,severe,total',
            'casualties' => 'nullable|integer|min:0',
            'injured' => 'nullable|integer|min:0',
            'missing' => 'nullable|integer|min:0',
            'house_damage_cost' => 'nullable|numeric|min:0',
            'needs_temporary_shelter' => 'nullable|boolean',
            'relief_received' => 'nullable|boolean',
            'relief_items' => 'nullable|array',
            'relief_date' => 'nullable|date',
            'needs' => 'nullable|string',
            'assessed_by' => 'nullable|exists:users,id',
            'evacuation_status' => 'nullable|in:in_home,evacuated,returned',
        ]);
        $model = CalamityAffectedHousehold::create($data);

        return new CalamityAffectedHouseholdResource($model);
    }

    public function show(CalamityAffectedHousehold $calamity_affected_household)
    {
        return new CalamityAffectedHouseholdResource($calamity_affected_household->load(['calamity', 'household', 'assessor']));
    }

    public function update(Request $request, CalamityAffectedHousehold $calamity_affected_household)
    {
        $data = $request->validate([
            'damage_level' => 'nullable|in:none,minor,moderate,severe,total',
            'casualties' => 'nullable|integer|min:0',
            'injured' => 'nullable|integer|min:0',
            'missing' => 'nullable|integer|min:0',
            'house_damage_cost' => 'nullable|numeric|min:0',
            'needs_temporary_shelter' => 'nullable|boolean',
            'relief_received' => 'nullable|boolean',
            'relief_items' => 'nullable|array',
            'relief_date' => 'nullable|date',
            'needs' => 'nullable|string',
            'assessed_by' => 'nullable|exists:users,id',
            'evacuation_status' => 'nullable|in:in_home,evacuated,returned',
        ]);
        $calamity_affected_household->update($data);

        return new CalamityAffectedHouseholdResource($calamity_affected_household->fresh()->load(['calamity', 'household', 'assessor']));
    }

    public function destroy(CalamityAffectedHousehold $calamity_affected_household)
    {
        $calamity_affected_household->delete();

        return response()->json(null, 204);
    }
}
