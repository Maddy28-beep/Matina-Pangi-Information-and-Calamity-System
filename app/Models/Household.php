<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Household extends Model
{
    use Archivable, HasFactory, SoftDeletes;

    protected $fillable = [
        'household_id',
        'official_head_id',
        'address',
        'purok',
        'purok_id',
        'housing_type',
        'has_electricity',
        'electric_account_number',
        'total_members',
        'household_type',
        'parent_household_id',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'has_electricity' => 'boolean',
        'total_members' => 'integer',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot method to generate household ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($household) {
            if (empty($household->household_id)) {
                $household->household_id = self::generateHouseholdId();
            }

            // Set approval status based on user role
            if (auth()->check() && auth()->user()->isStaff()) {
                $household->approval_status = 'pending';
            } else {
                $household->approval_status = 'approved';
                $household->approved_at = now();
                $household->approved_by = auth()->id();
            }
        });
    }

    /**
     * Generate unique household ID (HH-xxxx)
     */
    public static function generateHouseholdId(): string
    {
        $lastHousehold = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $lastHousehold ? intval(substr($lastHousehold->household_id, 3)) + 1 : 1;

        return 'HH-'.str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get all residents in this household
     */
    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Get the official household head (barangay-recognized)
     */
    public function officialHead()
    {
        return $this->belongsTo(Resident::class, 'official_head_id');
    }

    /**
     * Get the household head (legacy support)
     */
    public function head()
    {
        return $this->hasOne(Resident::class)->where('is_household_head', true);
    }

    /**
     * Get household members (excluding head)
     */
    public function members()
    {
        return $this->hasMany(Resident::class)->where('is_household_head', false);
    }

    /**
     * Parent household (for new family heads)
     */
    public function parentHousehold()
    {
        return $this->belongsTo(Household::class, 'parent_household_id');
    }

    /**
     * Child households (new family heads from this household)
     */
    public function childHouseholds()
    {
        return $this->hasMany(Household::class, 'parent_household_id');
    }

    /**
     * Get total household income
     */
    public function getTotalIncomeAttribute()
    {
        return $this->residents()->sum('monthly_income');
    }

    /**
     * Get household full address
     */
    public function getFullAddressAttribute()
    {
        $address = $this->address;

        // Only add Purok prefix if address doesn't already contain it
        if ($this->purok && ! stripos($address, 'purok')) {
            $address = "Purok {$this->purok}, {$address}";
        }

        return $address.', Barangay Matina Pangi';
    }

    /**
     * User who approved this household
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for approved households
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope for rejected households
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Check if household is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if household is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Get approval status badge color
     */
    public function getApprovalBadgeColorAttribute(): string
    {
        return match ($this->approval_status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Phase 2 Relationships
     */
    public function purok()
    {
        return $this->belongsTo(Purok::class);
    }

    public function governmentAssistance()
    {
        return $this->hasMany(GovernmentAssistance::class);
    }

    public function events()
    {
        return $this->hasMany(HouseholdEvent::class);
    }

    public function addressHistory()
    {
        return $this->hasMany(AddressHistory::class);
    }

    public function calamityRecords()
    {
        return $this->hasMany(CalamityAffectedHousehold::class);
    }

    /**
     * Get all sub-families in this household
     */
    public function subFamilies()
    {
        return $this->hasMany(SubFamily::class);
    }

    /**
     * Get the primary sub-family (official head's family)
     */
    public function primaryFamily()
    {
        return $this->hasOne(SubFamily::class)->where('is_primary_family', true);
    }

    /**
     * Get all extended sub-families (excluding primary)
     */
    public function extendedFamilies()
    {
        return $this->hasMany(SubFamily::class)->where('is_primary_family', false);
    }

    /**
     * Get household statistics
     */
    public function getStatisticsAttribute()
    {
        $residents = $this->residents;

        return [
            'total_residents' => $residents->count(),
            'total_families' => $this->subFamilies()->count(),
            'seniors' => $residents->where('is_senior_citizen', true)->count(),
            'pwd' => $residents->where('is_pwd', true)->count(),
            'teens' => $residents->where('is_teen', true)->count(),
            'voters' => $residents->where('is_voter', true)->count(),
            'four_ps' => $residents->where('is_4ps_beneficiary', true)->count(),
        ];
    }
}
