<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Purok;
use Illuminate\Http\Request;

class PurokController extends Controller
{
    /**
     * Display a listing of puroks
     */
    public function index()
    {
        $puroks = Purok::withCounts()->orderBy('purok_code')->get();

        return view('puroks.index', compact('puroks'));
    }

    /**
     * Show the form for creating a new purok
     */
    public function create()
    {
        return view('puroks.create');
    }

    /**
     * Store a newly created purok
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purok_name' => 'required|string|max:255',
            'purok_code' => 'required|string|max:50|unique:puroks,purok_code',
            'purok_leader_name' => 'nullable|string|max:255',
            'purok_leader_contact' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'boundaries' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $purok = Purok::create($validated);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model' => 'Purok',
            'model_id' => $purok->id,
            'changes' => json_encode($validated),
        ]);

        return redirect()->route('puroks.index')
            ->with('success', 'Purok created successfully!');
    }

    /**
     * Display the specified purok
     */
    public function show(Purok $purok)
    {
        $purok->load(['households.head', 'households.members']);

        return view('puroks.show', compact('purok'));
    }

    /**
     * Show the form for editing the specified purok
     */
    public function edit(Purok $purok)
    {
        return view('puroks.edit', compact('purok'));
    }

    /**
     * Update the specified purok
     */
    public function update(Request $request, Purok $purok)
    {
        $validated = $request->validate([
            'purok_name' => 'required|string|max:255',
            'purok_code' => 'required|string|max:50|unique:puroks,purok_code,'.$purok->id,
            'purok_leader_name' => 'nullable|string|max:255',
            'purok_leader_contact' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'boundaries' => 'nullable|string',
        ]);

        $old = $purok->toArray();
        $validated['updated_by'] = auth()->id();

        $purok->update($validated);

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model' => 'Purok',
            'model_id' => $purok->id,
            'old_values' => json_encode($old),
            'new_values' => json_encode($validated),
        ]);

        return redirect()->route('puroks.index')
            ->with('success', 'Purok updated successfully!');
    }

    /**
     * Remove the specified purok
     */
    public function destroy(Purok $purok)
    {
        // Check if purok has households
        if ($purok->households()->count() > 0) {
            return redirect()->route('puroks.index')
                ->with('error', 'Cannot archive purok with existing households!');
        }

        // Log the action
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'archive',
            'model' => 'Purok',
            'model_id' => $purok->id,
            'old_values' => json_encode($purok->toArray()),
        ]);

        $purok->archive('Archived by '.auth()->user()->name);

        return redirect()->route('puroks.index')
            ->with('success', 'Purok archived successfully!');
    }

    /**
     * Update purok population counts
     */
    public function updateCounts(Purok $purok)
    {
        // Store old counts
        $oldHouseholds = $purok->total_households;
        $oldPopulation = $purok->total_population;

        // Update counts from database
        $purok->updateCounts();

        // Get new counts
        $newHouseholds = $purok->total_households;
        $newPopulation = $purok->total_population;

        // Check if counts changed
        if ($oldHouseholds == $newHouseholds && $oldPopulation == $newPopulation) {
            $message = "Counts are already up to date! Households: {$newHouseholds}, Population: {$newPopulation}";
        } else {
            $message = 'Purok counts updated successfully! ';
            $message .= "Households: {$oldHouseholds} → {$newHouseholds}, ";
            $message .= "Population: {$oldPopulation} → {$newPopulation}";
        }

        return redirect()->back()
            ->with('success', $message);
    }
}
