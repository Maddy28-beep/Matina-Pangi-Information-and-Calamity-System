<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class CalamityReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'calamity_id' => $this->calamity_id,
            'report_date' => $this->report_date,
            'total_casualties' => $this->total_casualties,
            'total_evacuated' => $this->total_evacuated,
            'relief_used_items' => $this->relief_used_items,
            'total_damage_cost' => $this->total_damage_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
