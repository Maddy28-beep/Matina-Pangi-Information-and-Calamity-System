<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Models\CalamityAffectedHousehold;
use Illuminate\Http\Request;

class AffectedHouseholdPageController extends Controller
{
    public function index(Request $request)
    {
        $query = CalamityAffectedHousehold::with(['household']);
        if ($request->filled('search')) {
            $s = '%'.trim($request->get('search')).'%';
            $query->whereHas('household', function ($q) use ($s) {
                $q->where('household_id', 'like', $s);
            });
        }
        if ($request->filled('damage_level')) {
            $query->where('damage_level', $request->get('damage_level'));
        }
        if ($request->filled('evacuation_status')) {
            $query->where('evacuation_status', $request->get('evacuation_status'));
        }
        $affectedHouseholds = $query->latest()->paginate(20)->withQueryString();

        return view('calamity.affected.index', compact('affectedHouseholds'));
    }

    public function show(CalamityAffectedHousehold $calamity_affected_household)
    {
        $calamity_affected_household->load(['household']);

        return view('calamity.affected.show', compact('calamity_affected_household'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_id' => 'required|exists:calamities,id',
            'household_id' => 'required|exists:households,id',
            'damage_level' => 'required|in:minor,moderate,severe,total',
            'casualties' => 'nullable|integer|min:0',
            'injured' => 'nullable|integer|min:0',
            'missing' => 'nullable|integer|min:0',
            'house_damage_cost' => 'nullable|numeric|min:0',
            'needs_temporary_shelter' => 'nullable|boolean',
            'relief_received' => 'nullable|boolean',
            'relief_items' => 'nullable|array',
            'relief_date' => 'nullable|date',
            'needs' => 'nullable|string',
            'evacuation_status' => 'nullable|in:in_home,evacuated,returned',
        ]);
        $model = CalamityAffectedHousehold::create($data);

        return redirect()->route('web.calamity-affected-households.show', $model)
            ->with('success', 'Affected household recorded successfully');
    }
}
