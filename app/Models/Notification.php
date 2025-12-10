<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Notification extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'calamity_id',
        'type',
        'title',
        'message',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = Crypt::encryptString((string) $value);
    }

    public function getMessageAttribute($value)
    {
        if (! $value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = Crypt::encryptString((string) $value);
    }

    public function getTitleAttribute($value)
    {
        if (! $value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
