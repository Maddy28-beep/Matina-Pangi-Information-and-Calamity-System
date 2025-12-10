<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResidentTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'old_household_id',
        'new_household_id',
        'sub_family_id',
        'old_purok',
        'new_purok',
        'new_address',
        'internal_transfer_type',
        'new_household_head_option',
        'new_household_head_id',
        'transfer_type',
        'status',
        'transfer_date',
        'reason',
        'reason_details',
        'reason_for_transfer',
        'origin_address',
        'origin_barangay',
        'origin_municipality',
        'origin_province',
        'destination_address',
        'destination_barangay',
        'destination_municipality',
        'destination_province',
        'approved_by',
        'approved_at',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function oldHousehold()
    {
        return $this->belongsTo(Household::class, 'old_household_id');
    }

    public function newHousehold()
    {
        return $this->belongsTo(Household::class, 'new_household_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function newHouseholdHead()
    {
        return $this->belongsTo(Resident::class, 'new_household_head_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeTransferIn($query)
    {
        return $query->where('transfer_type', 'transfer_in');
    }

    public function scopeTransferOut($query)
    {
        return $query->where('transfer_type', 'transfer_out');
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Check if transfer is approved
     */
    public function isApproved()
    {
        return ! is_null($this->approved_at);
    }

    /**
     * Get full origin location
     */
    public function getFullOriginAttribute()
    {
        $parts = array_filter([
            $this->origin_barangay,
            $this->origin_municipality,
            $this->origin_province,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get full destination location
     */
    public function getFullDestinationAttribute()
    {
        $parts = array_filter([
            $this->destination_barangay,
            $this->destination_municipality,
            $this->destination_province,
        ]);

        return implode(', ', $parts);
    }
}
