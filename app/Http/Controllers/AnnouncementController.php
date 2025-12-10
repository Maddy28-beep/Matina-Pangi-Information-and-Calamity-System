<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRecipient;
use App\Models\Household;
use App\Models\Notification;
use App\Models\Purok;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(20);

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $puroks = Purok::orderBy('purok_name')->get();
        $households = Household::approved()
            ->with('officialHead')
            ->orderBy('household_id')
            ->get();

        return view('announcements.create', compact('puroks', 'households'));
    }

    public function store(Request $request)
    {
        if ($request->filled('filters_json')) {
            $payload = json_decode($request->input('filters_json'), true);
            if (is_array($payload)) {
                $request->merge([
                    'title' => $payload['title'] ?? $request->input('title'),
                    'message' => $payload['message'] ?? $request->input('message'),
                    'urgency' => $payload['urgency'] ?? $request->input('urgency'),
                    'target_purok' => $payload['target_purok'] ?? $request->input('target_purok'),
                    'age_range' => $payload['age_range'] ?? $request->input('age_range'),
                    'households' => $payload['households'] ?? $request->input('households'),
                    'only_affected' => (bool) ($payload['only_affected'] ?? $request->boolean('only_affected')),
                    'only_evacuees' => (bool) ($payload['only_evacuees'] ?? $request->boolean('only_evacuees')),
                    'send_sms' => (bool) ($payload['send_sms'] ?? $request->boolean('send_sms')),
                    'send_email' => (bool) ($payload['send_email'] ?? $request->boolean('send_email')),
                ]);
            }
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'urgency' => 'nullable|string',
            'target_purok' => 'nullable|string',
            'age_range.min' => 'nullable|integer|min:0',
            'age_range.max' => 'nullable|integer|min:0',
            'households' => 'nullable|array',
            'households.*' => 'integer',
            'only_affected' => 'nullable|boolean',
            'only_evacuees' => 'nullable|boolean',
            'send_sms' => 'nullable|boolean',
            'send_email' => 'nullable|boolean',
        ]);

        $filters = [
            'target_purok' => $request->target_purok,
            'age_range' => $request->age_range,
            'households' => $request->households,
            'only_affected' => (bool) $request->only_affected,
            'only_evacuees' => (bool) $request->only_evacuees,
        ];

        $announcement = Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'urgency' => $request->urgency,
            'filters' => $filters,
            'send_sms' => (bool) $request->send_sms,
            'send_email' => (bool) $request->send_email,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        $query = Resident::query()->approved();

        if ($request->target_purok) {
            $query->whereHas('household', function ($q) use ($request) {
                $q->where('purok', $request->target_purok);
            });
        }

        if ($request->age_range && isset($request->age_range['min']) && isset($request->age_range['max'])) {
            $min = is_numeric($request->age_range['min']) ? intval($request->age_range['min']) : null;
            $max = is_numeric($request->age_range['max']) ? intval($request->age_range['max']) : null;
            if (! is_null($min) && ! is_null($max)) {
                $query->whereBetween('age', [$min, $max]);
            } elseif (! is_null($min)) {
                $query->where('age', '>=', $min);
            } elseif (! is_null($max)) {
                $query->where('age', '<=', $max);
            }
        }

        if ($request->households && is_array($request->households)) {
            $query->whereIn('household_id', $request->households);
        }

        if ($request->only_affected) {
            $query->whereHas('household.calamityRecords');
        }

        if ($request->only_evacuees) {
            $query->whereHas('household.calamityRecords', function ($q) {
                $q->where('evacuation_status', 'evacuated');
            });
        }

        $recipients = $query->get();

        DB::transaction(function () use ($announcement, $recipients, $request) {
            foreach ($recipients as $resident) {
                AnnouncementRecipient::create([
                    'announcement_id' => $announcement->id,
                    'resident_id' => $resident->id,
                    'delivery_status' => null,
                ]);

                if ($request->send_sms && $resident->contact_number) {
                    try {
                        Log::info('SMS send', ['to' => $resident->contact_number, 'message' => $request->message]);
                    } catch (\Throwable $e) {
                        Log::error('SMS send failed', ['error' => $e->getMessage()]);
                    }
                }

                if ($request->send_email && $resident->email) {
                    try {
                        Mail::raw($request->message, function ($m) use ($resident, $announcement) {
                            $m->to($resident->email)->subject($announcement->title);
                        });
                    } catch (\Throwable $e) {
                        Log::error('Email send failed', ['error' => $e->getMessage()]);
                    }
                }
            }
        });

        $announcement->status = 'sent';
        $announcement->sent_at = now();
        $announcement->save();

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement sent successfully');
    }

    public function show(Announcement $announcement)
    {
        $announcement->load('recipients.resident');

        return view('announcements.show', compact('announcement'));
    }

    public function bell()
    {
        $anQuery = Announcement::where('status', 'sent')
            ->whereNotNull('sent_at');

        $noQuery = Notification::where('status', 'sent')
            ->whereNotNull('sent_at');

        $count = $anQuery->count() + $noQuery->count();

        $anItems = Announcement::where('status', 'sent')
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->take(5)
            ->get(['id', 'title', 'sent_at'])
            ->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'sent_at' => $a->sent_at,
                    'sent_at_human' => optional($a->sent_at)->diffForHumans(),
                    'url' => route('announcements.show', $a),
                ];
            });

        $noItems = Notification::where('status', 'sent')
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->take(5)
            ->get(['id', 'title', 'sent_at'])
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'sent_at' => $n->sent_at,
                    'sent_at_human' => optional($n->sent_at)->diffForHumans(),
                    'url' => route('web.notifications.show', $n),
                ];
            });

        $combined = $anItems->merge($noItems)->sortByDesc(function ($i) {
            return $i['sent_at'] ?? now()->subYears(10);
        })->values()->take(5);

        return response()->json([
            'count' => $count,
            'items' => $combined->map(function ($i) {
                return [
                    'id' => $i['id'],
                    'title' => $i['title'],
                    'sent_at_human' => $i['sent_at_human'] ?? '',
                    'url' => $i['url'],
                ];
            }),
        ]);
    }
}
