<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class CalamityAffectedHouseholdResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'calamity_id' => $this->calamity_id,
            'household_id' => $this->household_id,
            'damage_level' => $this->damage_level,
            'casualties' => $this->casualties,
            'injured' => $this->injured,
            'missing' => $this->missing,
            'house_damage_cost' => $this->house_damage_cost,
            'needs_temporary_shelter' => $this->needs_temporary_shelter,
            'relief_received' => $this->relief_received,
            'relief_items' => $this->relief_items,
            'relief_date' => $this->relief_date,
            'needs' => $this->needs,
            'assessed_by' => $this->assessed_by,
            'evacuation_status' => $this->evacuation_status ?? null,
            'rescue_operations' => RescueOperationResource::collection($this->whenLoaded('rescueOperations')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
