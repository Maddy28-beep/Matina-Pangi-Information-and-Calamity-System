<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubFamily extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_family_name',
        'household_id',
        'sub_head_resident_id',
        'is_primary_family',
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'is_primary_family' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the household this sub-family belongs to
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the sub-family head (resident)
     */
    public function subHead()
    {
        return $this->belongsTo(Resident::class, 'sub_head_resident_id');
    }

    /**
     * Get all members of this sub-family
     */
    public function members()
    {
        return $this->hasMany(Resident::class, 'sub_family_id');
    }

    /**
     * User who approved this sub-family
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for approved sub-families
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
     * Check if sub-family is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if sub-family is pending
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Get member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
