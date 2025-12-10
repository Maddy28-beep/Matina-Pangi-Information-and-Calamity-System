<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return NotificationResource::collection(Notification::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_id' => 'nullable|exists:calamities,id',
            'type' => 'required|in:sms,email,system',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'nullable|in:draft,sent,failed',
            'sent_at' => 'nullable|date',
        ]);
        if (! isset($data['status']) || $data['status'] === null) {
            $data['status'] = 'sent';
        }
        if (! isset($data['sent_at']) || $data['sent_at'] === null) {
            $data['sent_at'] = now();
        }
        $notification = Notification::create($data);
        if ($request->expectsJson()) {
            return new NotificationResource($notification);
        }

        return redirect()->route('web.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    public function show(Notification $notification)
    {
        return new NotificationResource($notification);
    }

    public function update(Request $request, Notification $notification)
    {
        $data = $request->validate([
            'calamity_id' => 'nullable|exists:calamities,id',
            'type' => 'nullable|in:sms,email,system',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'status' => 'nullable|in:draft,sent,failed',
            'sent_at' => 'nullable|date',
        ]);
        $notification->update($data);
        if ($request->expectsJson()) {
            return new NotificationResource($notification);
        }

        return redirect()->route('web.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    public function destroy(Notification $notification)
    {
        // Use the Archivable trait to archive
        $notification->archive('Archived by '.auth()->user()->name);
        if (request()->expectsJson()) {
            return response()->json(null, 204);
        }

        return redirect()->route('web.notifications.index')
            ->with('success', 'Notification archived successfully.');
    }
}
