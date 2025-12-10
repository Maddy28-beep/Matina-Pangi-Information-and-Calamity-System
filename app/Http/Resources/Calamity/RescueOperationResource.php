<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class RescueOperationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'calamity_affected_household_id' => $this->calamity_affected_household_id,
            'rescuer_type' => $this->rescuer_type,
            'rescuer_id' => $this->rescuer_id,
            'rescue_time' => $this->rescue_time,
            'evacuation_center_id' => $this->evacuation_center_id,
            'ambulance_vehicle' => $this->ambulance_vehicle,
            'notes' => $this->notes,
            'affected_household' => $this->whenLoaded('affectedHousehold'),
            'rescuer' => $this->whenLoaded('rescuer'),
            'evacuation_center' => $this->whenLoaded('evacuationCenter'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
