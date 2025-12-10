<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PwdSupport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pwd_supports';

    protected $fillable = [
        'resident_id',
        'pwd_id_number',
        'disability_type',
        'medical_condition',
        'assistive_device',
        'aid_status',
        'date_issued',
        'pwd_id_expiry',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pwd_id_expiry' => 'date',
        'date_issued' => 'date',
    ];

    /**
     * Relationships
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Scopes
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('disability_level', $level);
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('pwd_id_expiry')
            ->whereBetween('pwd_id_expiry', [now(), now()->addDays(30)]);
    }

    /**
     * Check if PWD ID is expired
     */
    public function isExpired()
    {
        if (! $this->pwd_id_expiry) {
            return false;
        }

        return $this->pwd_id_expiry->isPast();
    }

    /**
     * Check if PWD ID is expiring soon (within 30 days)
     */
    public function isExpiringSoon()
    {
        if (! $this->pwd_id_expiry) {
            return false;
        }

        return $this->pwd_id_expiry->diffInDays(now()) <= 30 && $this->pwd_id_expiry->isFuture();
    }
}
