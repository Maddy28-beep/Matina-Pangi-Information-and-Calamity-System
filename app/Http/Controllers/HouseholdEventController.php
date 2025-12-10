<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\HouseholdEvent;
use Illuminate\Http\Request;

class HouseholdEventController extends Controller
{
    /**
     * Display a listing of household events
     */
    public function index(Request $request)
    {
        $query = HouseholdEvent::with(['household', 'oldHead', 'newHead', 'processor']);

        // Filter by event type
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        // Filter by household
        if ($request->filled('household_id')) {
            $query->where('household_id', $request->household_id);
        }

        // Search by household ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('household', function ($q) use ($search) {
                $q->where('household_id', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        $events = $query->latest('event_date')->paginate(20);

        // Get all households for filter dropdown
        $households = Household::approved()
            ->orderBy('household_id')
            ->get(['id', 'household_id']);

        return view('household-events.index', compact('events', 'households'));
    }

    /**
     * Display events for a specific household
     */
    public function byHousehold(Household $household)
    {
        $events = HouseholdEvent::where('household_id', $household->id)
            ->with(['oldHead', 'newHead', 'processor'])
            ->latest('event_date')
            ->paginate(20);

        return view('household-events.by-household', compact('events', 'household'));
    }

    /**
     * Display the specified event
     */
    public function show(HouseholdEvent $householdEvent)
    {
        $householdEvent->load(['household', 'oldHead', 'newHead', 'processor']);

        return view('household-events.show', compact('householdEvent'));
    }
}
