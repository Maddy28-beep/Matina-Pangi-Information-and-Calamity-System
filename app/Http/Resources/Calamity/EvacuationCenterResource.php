<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class EvacuationCenterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'current_occupancy' => $this->current_occupancy,
            'facilities' => $this->facilities,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
