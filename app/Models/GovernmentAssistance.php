<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentAssistance extends Model
{
    use HasFactory;

    protected $table = 'government_assistance';

    protected $fillable = [
        'resident_id',
        'household_id',
        'assistance_type',
        'program_name',
        'program_type',
        'id_number',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'date_received',
        'status',
        'description',
        'notes',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'date_received' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('assistance_type', $type);
    }

    public function scopeFourPs($query)
    {
        return $query->where('assistance_type', '4ps');
    }

    public function scopeSeniorPension($query)
    {
        return $query->where('assistance_type', 'senior_pension');
    }

    public function scopePwdAllowance($query)
    {
        return $query->where('assistance_type', 'pwd_allowance');
    }

    /**
     * Check if assistance is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if assistance is about to expire (within 30 days)
     */
    public function isExpiringSoon()
    {
        if (! $this->end_date) {
            return false;
        }

        return $this->end_date->diffInDays(now()) <= 30 && $this->end_date->isFuture();
    }
}
