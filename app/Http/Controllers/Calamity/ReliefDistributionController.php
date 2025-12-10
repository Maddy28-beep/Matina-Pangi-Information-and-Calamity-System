<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\ReliefDistributionResource;
use App\Models\ReliefDistribution;
use Illuminate\Http\Request;

class ReliefDistributionController extends Controller
{
    public function index()
    {
        return ReliefDistributionResource::collection(ReliefDistribution::with(['item', 'household', 'calamity', 'staff'])->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'relief_item_id' => 'required|exists:relief_items,id',
            'household_id' => 'required|exists:households,id',
            'calamity_id' => 'nullable|exists:calamities,id',
            'quantity' => 'required|integer|min:1',
            'distributed_at' => 'required|date',
            'staff_in_charge' => 'nullable|exists:users,id',
        ]);
        $dist = ReliefDistribution::create($data);

        return new ReliefDistributionResource($dist);
    }

    public function show(ReliefDistribution $relief_distribution)
    {
        return new ReliefDistributionResource($relief_distribution->load(['item', 'household', 'calamity', 'staff']));
    }

    public function update(Request $request, ReliefDistribution $relief_distribution)
    {
        $data = $request->validate([
            'relief_item_id' => 'sometimes|required|exists:relief_items,id',
            'household_id' => 'sometimes|required|exists:households,id',
            'calamity_id' => 'nullable|exists:calamities,id',
            'quantity' => 'nullable|integer|min:1',
            'distributed_at' => 'nullable|date',
            'staff_in_charge' => 'nullable|exists:users,id',
        ]);
        $relief_distribution->update($data);

        return new ReliefDistributionResource($relief_distribution->load(['item', 'household', 'calamity', 'staff']));
    }

    public function destroy(ReliefDistribution $relief_distribution)
    {
        $relief_distribution->archive('Archived by '.auth()->user()->name);

        return response()->json(null, 204);
    }
}
