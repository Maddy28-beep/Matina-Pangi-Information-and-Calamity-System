<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'resident_id',
        'delivery_status',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
