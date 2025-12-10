<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\ReliefItemResource;
use App\Models\ReliefItem;
use Illuminate\Http\Request;

class ReliefItemController extends Controller
{
    public function index()
    {
        if (request()->expectsJson()) {
            return ReliefItemResource::collection(ReliefItem::latest()->paginate(20));
        }
        $query = ReliefItem::query()->latest();
        if (request('search')) {
            $s = '%'.trim(request('search')).'%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)->orWhere('category', 'like', $s);
            });
        }
        if (request('category')) {
            $query->where('category', request('category'));
        }
        if (request('min_qty')) {
            $query->where('quantity', '>=', (int) request('min_qty'));
        }
        $items = $query->paginate(20)->withQueryString();

        return view('calamity.relief_items.index', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'quantity' => 'required|integer|min:0',
        ]);
        $item = ReliefItem::create($data);
        if ($request->expectsJson()) {
            return new ReliefItemResource($item);
        }

        return redirect()->route('web.relief-items.index')
            ->with('success', 'Relief item added successfully');
    }

    public function show(ReliefItem $relief_item)
    {
        if (request()->expectsJson()) {
            return new ReliefItemResource($relief_item);
        }

        return view('calamity.relief_items.show', compact('relief_item'));
    }

    public function update(Request $request, ReliefItem $relief_item)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
        ]);
        $relief_item->update($data);
        if ($request->expectsJson()) {
            return new ReliefItemResource($relief_item);
        }

        return redirect()->route('web.relief-items.index')
            ->with('success', 'Relief item updated successfully');
    }

    public function destroy(ReliefItem $relief_item)
    {
        // Use the Archivable trait to archive
        $relief_item->archive('Archived by '.auth()->user()->name);
        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('web.relief-items.index')
            ->with('success', 'Relief item archived successfully');
    }

    public function create()
    {
        return view('calamity.relief_items.create');
    }

    public function edit(ReliefItem $relief_item)
    {
        return view('calamity.relief_items.edit', compact('relief_item'));
    }
}
