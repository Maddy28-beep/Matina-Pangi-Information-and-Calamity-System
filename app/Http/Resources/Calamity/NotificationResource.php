<?php

namespace App\Http\Resources\Calamity;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'calamity_id' => $this->calamity_id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
