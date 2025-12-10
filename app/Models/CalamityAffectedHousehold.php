<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CalamityAffectedHousehold extends Model
{
    use HasFactory;

    protected $fillable = [
        'calamity_id',
        'household_id',
        'damage_level',
        'casualties',
        'injured',
        'missing',
        'house_damage_cost',
        'needs_temporary_shelter',
        'relief_received',
        'relief_items',
        'relief_date',
        'needs',
        'assessed_by',
        'evacuation_status',
    ];

    protected $casts = [
        'house_damage_cost' => 'decimal:2',
        'needs_temporary_shelter' => 'boolean',
        'relief_received' => 'boolean',
        'relief_items' => 'array',
        'relief_date' => 'date',
    ];

    /**
     * Relationships
     */
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

    public function rescueOperations()
    {
        return $this->hasMany(RescueOperation::class, 'calamity_affected_household_id');
    }

    /**
     * Scopes
     */
    public function scopeNeedingRelief($query)
    {
        return $query->where('relief_received', false);
    }

    public function scopeReceivedRelief($query)
    {
        return $query->where('relief_received', true);
    }

    public function scopeNeedingShelter($query)
    {
        return $query->where('needs_temporary_shelter', true);
    }

    public function scopeByDamageLevel($query, $level)
    {
        return $query->where('damage_level', $level);
    }

    public function scopeSevereDamage($query)
    {
        return $query->whereIn('damage_level', ['severe', 'total']);
    }

    /**
     * Check if household has casualties
     */
    public function hasCasualties()
    {
        return $this->casualties > 0 || $this->injured > 0 || $this->missing > 0;
    }

    /**
     * Get total people affected
     */
    public function getTotalAffectedPeopleAttribute()
    {
        return $this->casualties + $this->injured + $this->missing;
    }

    public function setNeedsAttribute($value)
    {
        $this->attributes['needs'] = $value !== null ? Crypt::encryptString((string) $value) : null;
    }

    public function getNeedsAttribute($value)
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
