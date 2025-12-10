<?php

namespace App\Http\Controllers\Calamity;

use App\Http\Controllers\Controller;
use App\Http\Resources\Calamity\CalamityResource;
use App\Models\Calamity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CalamityIncidentController extends Controller
{
    public function index()
    {
        return CalamityResource::collection(Calamity::latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'calamity_name' => 'required|string|max:255',
            'calamity_type' => 'required|string',
            'date_occurred' => 'required|date',
            'occurred_time' => 'nullable',
            'affected_puroks' => 'nullable|array',
            'severity' => 'nullable|string',
            'description' => 'nullable|string',
            'response_actions' => 'nullable|string',
            'status' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpeg,png,jpg|max:5120',
        ]);
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = Str::uuid()->toString().'-'.time().'.'.$file->getClientOriginalExtension();
                $file->storeAs('public/calamity_incident_photos', $filename);
                $photos[] = $filename;
            }
        }
        if (! empty($photos)) {
            $data['photos'] = $photos;
        }
        $calamity = Calamity::create($data);

        return new CalamityResource($calamity);
    }

    public function show(Calamity $calamity)
    {
        return new CalamityResource($calamity);
    }

    public function update(Request $request, Calamity $calamity)
    {
        $data = $request->validate([
            'calamity_name' => 'sometimes|required|string|max:255',
            'calamity_type' => 'sometimes|required|string',
            'date_occurred' => 'sometimes|required|date',
            'occurred_time' => 'nullable',
            'affected_puroks' => 'nullable|array',
            'severity' => 'nullable|string',
            'description' => 'nullable|string',
            'response_actions' => 'nullable|string',
            'status' => 'nullable|string',
            'photos' => 'nullable|array',
            'photos.*' => 'file|mimes:jpeg,png,jpg|max:5120',
        ]);
        $existing = is_array($calamity->photos) ? $calamity->photos : [];
        $newPhotos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = Str::uuid()->toString().'-'.time().'.'.$file->getClientOriginalExtension();
                $file->storeAs('public/calamity_incident_photos', $filename);
                $newPhotos[] = $filename;
            }
        }
        if (! empty($newPhotos)) {
            $data['photos'] = array_values(array_unique(array_merge($existing, $newPhotos)));
        }
        $calamity->update($data);

        return new CalamityResource($calamity);
    }

    public function destroy(Calamity $calamity)
    {
        $calamity->delete();

        return response()->json(null, 204);
    }

    public function uploadPhotos(Request $request, Calamity $calamity)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'file|mimes:jpeg,png,jpg|max:5120',
        ]);

        $existing = is_array($calamity->photos) ? $calamity->photos : [];
        $added = [];
        foreach ($request->file('photos') as $file) {
            $filename = Str::uuid()->toString().'-'.time().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/calamity_incident_photos', $filename);
            $added[] = $filename;
        }
        $calamity->photos = array_values(array_unique(array_merge($existing, $added)));
        $calamity->save();

        return response()->json([
            'photos' => collect($calamity->photos)->map(fn ($n) => asset('storage/calamity_incident_photos/'.$n))->all(),
            'added' => $added,
        ]);
    }

    public function deletePhoto(Request $request, Calamity $calamity, string $photoName)
    {
        $existing = collect(is_array($calamity->photos) ? $calamity->photos : []);
        if (! $existing->contains($photoName)) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $calamity->photos = $existing->reject(fn ($n) => $n === $photoName)->values()->all();
        $calamity->save();

        Storage::delete('public/calamity_incident_photos/'.$photoName);

        return response()->json([
            'photos' => collect($calamity->photos)->map(fn ($n) => asset('storage/calamity_incident_photos/'.$n))->all(),
            'deleted' => $photoName,
        ]);
    }
}
