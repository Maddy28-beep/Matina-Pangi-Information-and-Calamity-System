<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseholdEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id',
        'event_type',
        'description',
        'old_head_id',
        'new_head_id',
        'reason',
        'event_date',
        'notes',
        'processed_by',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function oldHead()
    {
        return $this->belongsTo(Resident::class, 'old_head_id');
    }

    public function newHead()
    {
        return $this->belongsTo(Resident::class, 'new_head_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scopes
     */
    public function scopeHeadChanges($query)
    {
        return $query->where('event_type', 'head_change');
    }

    public function scopeHouseholdSplits($query)
    {
        return $query->where('event_type', 'household_split');
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    /**
     * Get event description
     */
    public function getDescriptionAttribute()
    {
        switch ($this->event_type) {
            case 'head_change':
                return "Household head changed from {$this->oldHead->full_name} to {$this->newHead->full_name}";
            case 'member_added':
                return 'New member added to household';
            case 'member_removed':
                return 'Member removed from household';
            case 'household_split':
                return 'Household split into new family unit';
            case 'household_merged':
                return 'Household merged with another';
            default:
                return 'Household event';
        }
    }
}
