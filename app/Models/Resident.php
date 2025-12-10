<?php

namespace App\Models;

use App\Traits\Archivable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use Archivable, HasFactory, SoftDeletes;

    protected $fillable = [
        'resident_id',
        'household_id',
        'sub_family_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthdate',
        'age',
        'sex',
        'civil_status',
        'place_of_birth',
        'nationality',
        'religion',
        'contact_number',
        'email',
        'household_role',
        'is_household_head',
        'is_primary_head',
        'is_co_head',
        'status',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'status_notes',
        'status_changed_at',
        'status_changed_by',
        'is_pwd',
        'pwd_id',
        'disability_type',
        'is_senior_citizen',
        'senior_id',
        'is_teen',
        'is_voter',
        'precinct_number',
        'is_4ps_beneficiary',
        '4ps_id',
        'occupation',
        'employment_status',
        'employer_name',
        'monthly_income',
        'educational_attainment',
        'blood_type',
        'medical_conditions',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'age' => 'integer',
        'is_household_head' => 'boolean',
        'is_primary_head' => 'boolean',
        'is_co_head' => 'boolean',
        'is_pwd' => 'boolean',
        'is_senior_citizen' => 'boolean',
        'is_teen' => 'boolean',
        'is_voter' => 'boolean',
        'is_4ps_beneficiary' => 'boolean',
        'monthly_income' => 'decimal:2',
        'approved_at' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    /**
     * Boot method to generate resident ID and calculate age
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resident) {
            if (empty($resident->resident_id)) {
                $resident->resident_id = self::generateResidentId();
            }

            // Calculate age from birthdate
            if ($resident->birthdate) {
                $resident->age = Carbon::parse($resident->birthdate)->age;

                // Auto-set age categories
                if ($resident->age >= 60) {
                    $resident->is_senior_citizen = true;
                } elseif ($resident->age >= 13 && $resident->age <= 19) {
                    $resident->is_teen = true;
                }
            }

            // Set approval status based on user role
            if (auth()->check() && auth()->user()->isStaff()) {
                $resident->approval_status = 'pending';
            } else {
                $resident->approval_status = 'approved';
                $resident->approved_at = now();
                $resident->approved_by = auth()->id();
            }
        });

        static::updating(function ($resident) {
            // Recalculate age if birthdate changed
            if ($resident->isDirty('birthdate') && $resident->birthdate) {
                $resident->age = Carbon::parse($resident->birthdate)->age;

                // Update age categories
                $resident->is_senior_citizen = $resident->age >= 60;
                $resident->is_teen = $resident->age >= 13 && $resident->age <= 19;
            }
        });
    }

    /**
     * Generate unique resident ID (RES-xxxx)
     */
    public static function generateResidentId(): string
    {
        $lastResident = self::withTrashed()->orderBy('id', 'desc')->first();
        $number = $lastResident ? intval(substr($lastResident->resident_id, 4)) + 1 : 1;

        return 'RES-'.str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the household this resident belongs to
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the sub-family this resident belongs to
     */
    public function subFamily()
    {
        return $this->belongsTo(SubFamily::class, 'sub_family_id');
    }

    /**
     * User who created this resident
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated this resident
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        $name = "{$this->first_name} ";
        if ($this->middle_name) {
            $name .= substr($this->middle_name, 0, 1).'. ';
        }
        $name .= $this->last_name;
        if ($this->suffix) {
            $name .= " {$this->suffix}";
        }

        return $name;
    }

    /**
     * Scope for household heads
     */
    public function scopeHouseholdHeads($query)
    {
        return $query->where('is_household_head', true);
    }

    /**
     * Scope for primary heads (official barangay-recognized heads)
     */
    public function scopePrimaryHeads($query)
    {
        return $query->where('is_primary_head', true);
    }

    /**
     * Scope for sub-family heads (co-heads)
     */
    public function scopeSubFamilyHeads($query)
    {
        return $query->where('is_household_head', true)->where('is_primary_head', false);
    }

    /**
     * Scope for co-heads (extended family heads)
     */
    public function scopeCoHeads($query)
    {
        return $query->where('is_co_head', true);
    }

    /**
     * Scope for PWD
     */
    public function scopePwd($query)
    {
        return $query->where('is_pwd', true);
    }

    /**
     * Scope for senior citizens
     */
    public function scopeSeniorCitizens($query)
    {
        return $query->where('is_senior_citizen', true);
    }

    /**
     * Scope for teens
     */
    public function scopeTeens($query)
    {
        return $query->where('is_teen', true);
    }

    /**
     * Scope for voters
     */
    public function scopeVoters($query)
    {
        return $query->where('is_voter', true);
    }

    /**
     * Scope for 4Ps beneficiaries
     */
    public function scope4PsBeneficiaries($query)
    {
        return $query->where('is_4ps_beneficiary', true);
    }

    /**
     * Scope for active residents only
     */
    public function scopeActive($query)
    {
        return $query->where('residents.status', 'active');
    }

    /**
     * Scope for reallocated residents
     */
    public function scopeReallocated($query)
    {
        return $query->where('residents.status', 'reallocated');
    }

    /**
     * Scope for deceased residents
     */
    public function scopeDeceased($query)
    {
        return $query->where('residents.status', 'deceased');
    }

    /**
     * Scope for approved residents
     */
    public function scopeApproved($query)
    {
        return $query->where('residents.approval_status', 'approved');
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('residents.approval_status', 'pending');
    }

    /**
     * Scope for rejected residents
     */
    public function scopeRejected($query)
    {
        return $query->where('residents.approval_status', 'rejected');
    }

    /**
     * User who approved this resident
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * User who changed the status
     */
    public function statusChanger()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    /**
     * Check if resident is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if resident is reallocated
     */
    public function isReallocated(): bool
    {
        return $this->status === 'reallocated';
    }

    /**
     * Check if resident is deceased
     */
    public function isDeceased(): bool
    {
        return $this->status === 'deceased';
    }

    /**
     * Check if resident is pending approval
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if resident is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'reallocated' => 'warning',
            'deceased' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get approval badge color
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
    public function governmentAssistances()
    {
        return $this->hasMany(GovernmentAssistance::class);
    }

    public function transfers()
    {
        return $this->hasMany(ResidentTransfer::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function pwdSupport()
    {
        return $this->hasOne(PwdSupport::class);
    }

    /**
     * Dynamic Age Calculation
     * Always calculate age from birthdate, never use stored age
     */
    public function getCurrentAgeAttribute()
    {
        if (! $this->birthdate) {
            return null;
        }

        return Carbon::parse($this->birthdate)->age;
    }

    /**
     * Get age category
     */
    public function getAgeCategoryAttribute()
    {
        $age = $this->current_age;

        if ($age === null) {
            return 'Unknown';
        }

        if ($age < 13) {
            return 'Child';
        } elseif ($age >= 13 && $age <= 19) {
            return 'Teen';
        } elseif ($age >= 20 && $age <= 59) {
            return 'Adult';
        } else {
            return 'Senior Citizen';
        }
    }

    /**
     * Check if resident is a child (0-12)
     */
    public function isChild()
    {
        return $this->current_age !== null && $this->current_age < 13;
    }

    /**
     * Check if resident is a teen (13-19)
     */
    public function isTeen()
    {
        return $this->current_age !== null && $this->current_age >= 13 && $this->current_age <= 19;
    }

    /**
     * Check if resident is an adult (20-59)
     */
    public function isAdult()
    {
        return $this->current_age !== null && $this->current_age >= 20 && $this->current_age <= 59;
    }

    /**
     * Check if resident is a senior citizen (60+)
     */
    public function isSeniorCitizen()
    {
        return $this->current_age !== null && $this->current_age >= 60;
    }

    /**
     * Recalculate and update age categories
     * This should be called periodically or when viewing profile
     */
    public function updateAgeCategories()
    {
        if (! $this->birthdate) {
            return;
        }

        $currentAge = Carbon::parse($this->birthdate)->age;

        // Update stored age
        $this->age = $currentAge;

        // Update category flags
        $this->is_teen = ($currentAge >= 13 && $currentAge <= 19);
        $this->is_senior_citizen = ($currentAge >= 60);

        $this->save();
    }
}
