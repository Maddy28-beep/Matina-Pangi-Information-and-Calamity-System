<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Calamity extends Model
{
    use Archivable, HasFactory, SoftDeletes;

    protected $fillable = [
        'calamity_type',
        'calamity_name',
        'date_occurred',
        'affected_puroks',
        'affected_areas',
        'severity',
        'severity_level',
        'description',
        'photos',
        'response_actions',
        'status',
        'declared_by',
        'reported_by',
    ];

    protected $casts = [
        'date_occurred' => 'date',
        'affected_puroks' => 'array',
        'photos' => 'array',
    ];

    /**
     * Relationships
     */
    public function affectedHouseholds()
    {
        return $this->hasMany(CalamityAffectedHousehold::class);
    }

    public function declarer()
    {
        return $this->belongsTo(User::class, 'declared_by');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Scopes
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('calamity_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Get total affected households
     */
    public function getTotalAffectedAttribute()
    {
        return $this->affectedHouseholds()->count();
    }

    /**
     * Get total casualties
     */
    public function getTotalCasualtiesAttribute()
    {
        return $this->affectedHouseholds()->sum('casualties');
    }

    /**
     * Get total injured
     */
    public function getTotalInjuredAttribute()
    {
        return $this->affectedHouseholds()->sum('injured');
    }

    /**
     * Get households needing relief
     */
    public function getHouseholdsNeedingReliefAttribute()
    {
        return $this->affectedHouseholds()->where('relief_received', false)->count();
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

    public function setResponseActionsAttribute($value)
    {
        $this->attributes['response_actions'] = $value !== null ? Crypt::encryptString((string) $value) : null;
    }

    public function getResponseActionsAttribute($value)
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
