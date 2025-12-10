<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DamageAssessment extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'calamity_id',
        'household_id',
        'damage_level',
        'estimated_cost',
        'description',
        'photo_path',
        'assessed_at',
        'assessed_by',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'assessed_at' => 'datetime',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = $value !== null ? Crypt::encryptString((string) $value) : null;
    }

    public function getDescriptionAttribute($value)
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
