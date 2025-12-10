<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class DamageAssessmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'calamity_id' => $this->calamity_id,
            'household_id' => $this->household_id,
            'damage_level' => $this->damage_level,
            'estimated_cost' => $this->estimated_cost,
            'description' => $this->description,
            'photo_path' => $this->photo_path,
            'assessed_at' => $this->assessed_at,
            'assessed_by' => $this->assessed_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
