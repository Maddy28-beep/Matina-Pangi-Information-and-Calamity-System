<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class ReliefDistributionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'relief_item_id' => $this->relief_item_id,
            'household_id' => $this->household_id,
            'calamity_id' => $this->calamity_id,
            'quantity' => $this->quantity,
            'distributed_at' => $this->distributed_at,
            'staff_in_charge' => $this->staff_in_charge,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
