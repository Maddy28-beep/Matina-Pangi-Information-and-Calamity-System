<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Models\Calamity;
use App\Models\Notification;

class NotificationPageController extends Controller
{
    public function index()
    {
        $query = Notification::query()->latest();
        if (request('search')) {
            $s = '%'.trim(request('search')).'%';
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', $s)->orWhere('message', 'like', $s);
            });
        }
        if (request('type')) {
            $query->where('type', request('type'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $notifications = $query->paginate(20)->withQueryString();

        return view('calamity.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $calamities = Calamity::orderBy('date_occurred', 'desc')->get();

        return view('calamity.notifications.create', compact('calamities'));
    }

    public function show(Notification $notification)
    {
        $notification->load('calamity');

        return view('calamity.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        $calamities = Calamity::orderBy('date_occurred', 'desc')->get();

        return view('calamity.notifications.edit', compact('notification', 'calamities'));
    }
}
