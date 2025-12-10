<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\EvacuationCenterResource;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;

class EvacuationCenterController extends Controller
{
    public function index()
    {
        return EvacuationCenterResource::collection(EvacuationCenter::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'capacity' => 'required|integer|min:0',
            'current_occupancy' => 'nullable|integer|min:0',
            'facilities' => 'nullable|array',
        ]);
        $center = EvacuationCenter::create($data);

        return new EvacuationCenterResource($center);
    }

    public function show(EvacuationCenter $evacuation_center)
    {
        return new EvacuationCenterResource($evacuation_center);
    }

    public function update(Request $request, EvacuationCenter $evacuation_center)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer|min:0',
            'current_occupancy' => 'nullable|integer|min:0',
            'facilities' => 'nullable|array',
        ]);
        $evacuation_center->update($data);

        return new EvacuationCenterResource($evacuation_center);
    }

    public function destroy(EvacuationCenter $evacuation_center)
    {
        // Use the Archivable trait to archive
        $evacuation_center->archive('Archived by '.auth()->user()->name);

        return response()->json(null, 204);
    }
}
