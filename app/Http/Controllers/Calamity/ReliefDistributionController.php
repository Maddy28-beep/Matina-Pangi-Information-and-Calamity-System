<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\ReliefDistributionResource;
use App\Models\ReliefDistribution;
use App\Models\ReliefItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $item = ReliefItem::findOrFail($data['relief_item_id']);
        if ($item->quantity < $data['quantity']) {
            return response()->json(['message' => 'Insufficient stock'], 422);
        }
        $dist = DB::transaction(function () use ($data, $item) {
            $item->decrement('quantity', $data['quantity']);
            $created = ReliefDistribution::create($data);
            AuditLog::logAction(
                'stock_out',
                'ReliefItem',
                $item->id,
                'Distributed ' . $data['quantity'] . ' of item #' . $item->id . ' via Distribution #' . $created->id,
                null,
                [
                    'distribution_id' => $created->id,
                    'item_id' => $item->id,
                    'quantity' => $data['quantity'],
                    'household_id' => $data['household_id'],
                    'calamity_id' => $data['calamity_id'] ?? null,
                ]
            );
            return $created;
        });

        return new ReliefDistributionResource($dist);
    }

    /**
     * Store via web form and redirect
     */
    public function storeWeb(Request $request)
    {
        $data = $request->validate([
            'relief_item_id' => 'required|exists:relief_items,id',
            'household_id' => 'required|exists:households,id',
            'calamity_id' => 'nullable|exists:calamities,id',
            'quantity' => 'required|integer|min:1',
            'distributed_at' => 'required|date',
            'staff_in_charge' => 'nullable|exists:users,id',
        ]);

        if (!isset($data['staff_in_charge'])) {
            $data['staff_in_charge'] = auth()->id();
        }

        $item = ReliefItem::findOrFail($data['relief_item_id']);
        if ($item->quantity < $data['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock'])->withInput();
        }
        DB::transaction(function () use ($data, $item) {
            $item->decrement('quantity', $data['quantity']);
            $created = ReliefDistribution::create($data);
            AuditLog::logAction(
                'stock_out',
                'ReliefItem',
                $item->id,
                'Distributed ' . $data['quantity'] . ' of item #' . $item->id . ' via Distribution #' . $created->id,
                null,
                [
                    'distribution_id' => $created->id,
                    'item_id' => $item->id,
                    'quantity' => $data['quantity'],
                    'household_id' => $data['household_id'],
                    'calamity_id' => $data['calamity_id'] ?? null,
                ]
            );
        });

        return redirect()->route('web.relief-distributions.index')
            ->with('success', 'Relief distribution recorded successfully');
    }

    public function show(ReliefDistribution $relief_distribution)
    {
        return new ReliefDistributionResource($relief_distribution->load(['item', 'household', 'calamity', 'staff']));
    }

    public function showBlade(ReliefDistribution $relief_distribution)
    {
        $relief_distribution->load(['item', 'household', 'calamity', 'staff']);
        return view('calamity.distributions.show', compact('relief_distribution'));
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
        $oldItemId = $relief_distribution->relief_item_id;
        $oldQty = $relief_distribution->quantity;
        $newItemId = $data['relief_item_id'] ?? $oldItemId;
        $newQty = $data['quantity'] ?? $oldQty;

        DB::transaction(function () use ($relief_distribution, $data, $oldItemId, $oldQty, $newItemId, $newQty) {
            if ($oldItemId !== $newItemId) {
                $oldItem = ReliefItem::findOrFail($oldItemId);
                $newItem = ReliefItem::findOrFail($newItemId);
                $oldItem->increment('quantity', $oldQty);
                if ($newItem->quantity < $newQty) {
                    abort(response()->json(['message' => 'Insufficient stock'], 422));
                }
                $newItem->decrement('quantity', $newQty);
                AuditLog::logAction(
                    'stock_in',
                    'ReliefItem',
                    $oldItem->id,
                    'Returned ' . $oldQty . ' to item #' . $oldItem->id . ' by editing Distribution #' . $relief_distribution->id,
                    null,
                    [
                        'distribution_id' => $relief_distribution->id,
                        'item_id' => $oldItem->id,
                        'quantity' => $oldQty,
                    ]
                );
                AuditLog::logAction(
                    'stock_out',
                    'ReliefItem',
                    $newItem->id,
                    'Distributed ' . $newQty . ' of item #' . $newItem->id . ' by editing Distribution #' . $relief_distribution->id,
                    null,
                    [
                        'distribution_id' => $relief_distribution->id,
                        'item_id' => $newItem->id,
                        'quantity' => $newQty,
                    ]
                );
            } else {
                $item = ReliefItem::findOrFail($oldItemId);
                $diff = $newQty - $oldQty;
                if ($diff > 0) {
                    if ($item->quantity < $diff) {
                        abort(response()->json(['message' => 'Insufficient stock'], 422));
                    }
                    $item->decrement('quantity', $diff);
                    AuditLog::logAction(
                        'stock_out',
                        'ReliefItem',
                        $item->id,
                        'Additional ' . $diff . ' distributed from item #' . $item->id . ' by editing Distribution #' . $relief_distribution->id,
                        null,
                        [
                            'distribution_id' => $relief_distribution->id,
                            'item_id' => $item->id,
                            'quantity' => $diff,
                        ]
                    );
                } elseif ($diff < 0) {
                    $item->increment('quantity', abs($diff));
                    AuditLog::logAction(
                        'stock_in',
                        'ReliefItem',
                        $item->id,
                        'Returned ' . abs($diff) . ' to item #' . $item->id . ' by editing Distribution #' . $relief_distribution->id,
                        null,
                        [
                            'distribution_id' => $relief_distribution->id,
                            'item_id' => $item->id,
                            'quantity' => abs($diff),
                        ]
                    );
                }
            }

            $relief_distribution->update($data);
        });

        return new ReliefDistributionResource($relief_distribution->load(['item', 'household', 'calamity', 'staff']));
    }

    public function editBlade(ReliefDistribution $relief_distribution)
    {
        $relief_distribution->load(['item', 'household', 'calamity', 'staff']);
        return view('calamity.distributions.edit', compact('relief_distribution'));
    }

    public function destroy(ReliefDistribution $relief_distribution)
    {
        DB::transaction(function () use ($relief_distribution) {
            $item = ReliefItem::find($relief_distribution->relief_item_id);
            if ($item) {
                $item->increment('quantity', $relief_distribution->quantity);
                AuditLog::logAction(
                    'stock_in',
                    'ReliefItem',
                    $item->id,
                    'Restored ' . $relief_distribution->quantity . ' to item #' . $item->id . ' by archiving Distribution #' . $relief_distribution->id,
                    null,
                    [
                        'distribution_id' => $relief_distribution->id,
                        'item_id' => $item->id,
                        'quantity' => $relief_distribution->quantity,
                    ]
                );
            }
            $relief_distribution->archive('Archived by ' . auth()->user()->name);
        });

        return response()->json(null, 204);
    }

    public function updateWeb(Request $request, ReliefDistribution $relief_distribution)
    {
        $data = $request->validate([
            'relief_item_id' => 'sometimes|required|exists:relief_items,id',
            'household_id' => 'sometimes|required|exists:households,id',
            'calamity_id' => 'nullable|exists:calamities,id',
            'quantity' => 'nullable|integer|min:1',
            'distributed_at' => 'nullable|date',
            'staff_in_charge' => 'nullable|exists:users,id',
        ]);

        $oldItemId = $relief_distribution->relief_item_id;
        $oldQty = $relief_distribution->quantity;
        $newItemId = $data['relief_item_id'] ?? $oldItemId;
        $newQty = $data['quantity'] ?? $oldQty;

        try {
            DB::transaction(function () use ($relief_distribution, $data, $oldItemId, $oldQty, $newItemId, $newQty) {
                if ($oldItemId !== $newItemId) {
                    $oldItem = ReliefItem::findOrFail($oldItemId);
                    $newItem = ReliefItem::findOrFail($newItemId);
                    $oldItem->increment('quantity', $oldQty);
                    if ($newItem->quantity < $newQty) {
                        abort(422);
                    }
                    $newItem->decrement('quantity', $newQty);
                } else {
                    $item = ReliefItem::findOrFail($oldItemId);
                    $diff = $newQty - $oldQty;
                    if ($diff > 0) {
                        if ($item->quantity < $diff) {
                            abort(422);
                        }
                        $item->decrement('quantity', $diff);
                    } elseif ($diff < 0) {
                        $item->increment('quantity', abs($diff));
                    }
                }

                $relief_distribution->update($data);
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['quantity' => 'Insufficient stock'])->withInput();
        }

        return redirect()->route('web.relief-distributions.index')
            ->with('success', 'Relief distribution updated successfully');
    }

    public function destroyWeb(ReliefDistribution $relief_distribution)
    {
        DB::transaction(function () use ($relief_distribution) {
            $item = ReliefItem::find($relief_distribution->relief_item_id);
            if ($item) {
                $item->increment('quantity', $relief_distribution->quantity);
                AuditLog::logAction(
                    'stock_in',
                    'ReliefItem',
                    $item->id,
                    'Restored ' . $relief_distribution->quantity . ' to item #' . $item->id . ' by archiving Distribution #' . $relief_distribution->id,
                    null,
                    [
                        'distribution_id' => $relief_distribution->id,
                        'item_id' => $item->id,
                        'quantity' => $relief_distribution->quantity,
                    ]
                );
            }
            $relief_distribution->archive('Archived by ' . auth()->user()->name);
        });

        return redirect()->route('web.relief-distributions.index')
            ->with('success', 'Relief distribution archived');
    }
}
