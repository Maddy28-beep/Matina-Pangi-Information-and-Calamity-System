<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'urgency',
        'filters',
        'send_sms',
        'send_email',
        'status',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'filters' => 'array',
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function recipients()
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
