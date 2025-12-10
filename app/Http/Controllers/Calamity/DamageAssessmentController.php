<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\DamageAssessmentResource;
use App\Models\Calamity;
use App\Models\DamageAssessment;
use App\Models\Household;
use Illuminate\Http\Request;

class DamageAssessmentController extends Controller
{
    public function index()
    {
        return DamageAssessmentResource::collection(DamageAssessment::with(['calamity', 'household', 'assessor'])->latest()->paginate(20));
    }

    public function indexBlade(Request $request)
    {
        $query = DamageAssessment::with(['calamity', 'household', 'assessor'])->latest();

        if ($request->filled('calamity_id')) {
            $query->where('calamity_id', $request->input('calamity_id'));
        }

        if ($request->filled('damage_level')) {
            $query->where('damage_level', $request->input('damage_level'));
        }

        if ($request->filled('assessed_at')) {
            $query->whereDate('assessed_at', $request->input('assessed_at'));
        }

        $assessments = $query->paginate(20)->appends($request->except('page'));
        $calamities = Calamity::orderBy('date_occurred', 'desc')->get(['id', 'calamity_name']);

        return view('calamity.damage.index', compact('assessments', 'calamities'));
    }

    public function showBlade(DamageAssessment $damage_assessment)
    {
        $damage_assessment->load(['calamity', 'household', 'assessor']);

        return view('calamity.damage.show', compact('damage_assessment'));
    }

    public function editBlade(DamageAssessment $damage_assessment)
    {
        $damage_assessment->load(['calamity', 'household', 'assessor']);
        $calamities = Calamity::orderBy('date_occurred', 'desc')->get(['id', 'calamity_name']);
        $households = Household::approved()->orderBy('household_id')->get(['id', 'household_id', 'address', 'purok']);

        return view('calamity.damage.edit', compact('damage_assessment', 'calamities', 'households'));
    }

    public function createBlade()
    {
        $calamities = Calamity::orderBy('date_occurred', 'desc')->get(['id', 'calamity_name']);
        $households = Household::approved()->orderBy('household_id')->get(['id', 'household_id', 'address', 'purok']);

        return view('calamity.damage.create', compact('calamities', 'households'));
    }

    public function storeWeb(Request $request)
    {
        $data = $request->validate([
            'calamity_id' => 'required|exists:calamities,id',
            'household_id' => 'nullable|exists:households,id',
            'damage_level' => 'required|string',
            'estimated_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|file|image|max:5120',
            'assessed_at' => 'nullable|date',
            'assessed_by' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('photo_path')) {
            $data['photo_path'] = $request->file('photo_path')->store('damage_photos', 'public');
        }

        $data['assessed_by'] = $data['assessed_by'] ?? auth()->id();
        $data['assessed_at'] = $data['assessed_at'] ?? now();

        $assessment = DamageAssessment::create($data);

        return redirect()->route('web.damage-assessments.show', $assessment)
            ->with('success', 'Damage assessment created successfully.');
    }

    public function updateWeb(Request $request, DamageAssessment $damage_assessment)
    {
        $data = $request->validate([
            'calamity_id' => 'sometimes|required|exists:calamities,id',
            'household_id' => 'nullable|exists:households,id',
            'damage_level' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|file|image|max:5120',
            'assessed_at' => 'nullable|date',
            'assessed_by' => 'nullable|exists:users,id',
        ]);

        if ($request->hasFile('photo_path')) {
            $data['photo_path'] = $request->file('photo_path')->store('damage_photos', 'public');
        }

        $damage_assessment->update($data);

        return redirect()->route('web.damage-assessments.show', $damage_assessment)
            ->with('success', 'Damage assessment updated successfully.');
    }

    public function destroyWeb(DamageAssessment $damage_assessment)
    {
        $damage_assessment->archive('Archived by '.auth()->user()->name);

        return redirect()->route('web.damage-assessments.index')
            ->with('success', 'Damage assessment archived successfully.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_id' => 'required|exists:calamities,id',
            'household_id' => 'nullable|exists:households,id',
            'damage_level' => 'required|string',
            'estimated_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|string',
            'assessed_at' => 'nullable|date',
            'assessed_by' => 'nullable|exists:users,id',
        ]);
        $assessment = DamageAssessment::create($data);

        return new DamageAssessmentResource($assessment);
    }

    public function show(DamageAssessment $damage_assessment)
    {
        return new DamageAssessmentResource($damage_assessment->load(['calamity', 'household', 'assessor']));
    }

    public function update(Request $request, DamageAssessment $damage_assessment)
    {
        $data = $request->validate([
            'calamity_id' => 'sometimes|required|exists:calamities,id',
            'household_id' => 'nullable|exists:households,id',
            'damage_level' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|string',
            'assessed_at' => 'nullable|date',
            'assessed_by' => 'nullable|exists:users,id',
        ]);
        $damage_assessment->update($data);

        return new DamageAssessmentResource($damage_assessment->load(['calamity', 'household', 'assessor']));
    }

    public function destroy(DamageAssessment $damage_assessment)
    {
        $damage_assessment->archive('Archived by '.auth()->user()->name);

        return response()->json(null, 204);
    }
}
