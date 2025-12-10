<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class CalamityResource extends JsonResource
{
    public function toArray($request)
    {
        $photoUrls = [];
        if (is_array($this->photos)) {
            $photoUrls = collect($this->photos)
                ->filter()
                ->map(function ($name) {
                    return asset('storage/calamity_incident_photos/'.$name);
                })
                ->values()
                ->all();
        }

        return [
            'id' => $this->id,
            'calamity_type' => $this->calamity_type,
            'calamity_name' => $this->calamity_name,
            'date_occurred' => $this->date_occurred,
            'occurred_time' => $this->occurred_time ?? null,
            'affected_puroks' => $this->affected_puroks,
            'severity' => $this->severity ?? null,
            'severity_level' => $this->severity_level ?? null,
            'description' => $this->description,
            'photos' => $photoUrls,
            'response_actions' => $this->response_actions,
            'status' => $this->status,
            'declared_by' => $this->declared_by,
            'reported_by' => $this->reported_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
