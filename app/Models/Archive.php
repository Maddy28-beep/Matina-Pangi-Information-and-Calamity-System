<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_type',
        'original_id',
        'title',
        'data',
        'reason',
        'archived_by',
        'archived_at',
    ];

    protected $casts = [
        'data' => 'array',
        'archived_at' => 'datetime',
    ];

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Get the badge color for module type
     */
    public function getModuleBadgeColorAttribute()
    {
        return match ($this->module_type) {
            'Resident' => 'primary',
            'Household' => 'success',
            'Calamity' => 'danger',
            'EvacuationCenter' => 'info',
            'ReliefItem' => 'warning',
            'Notification' => 'secondary',
            'ResponseTeamMember' => 'dark',
            default => 'dark',
        };
    }
}
