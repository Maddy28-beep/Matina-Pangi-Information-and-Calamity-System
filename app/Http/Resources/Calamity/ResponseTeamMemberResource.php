<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class ResponseTeamMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'skills' => $this->skills,
            'calamity_id' => $this->calamity_id,
            'evacuation_center_id' => $this->evacuation_center_id,
            'assignment_notes' => $this->assignment_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
