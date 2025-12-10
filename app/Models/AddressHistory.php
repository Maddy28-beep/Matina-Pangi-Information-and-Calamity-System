<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressHistory extends Model
{
    use HasFactory;

    protected $table = 'address_history';

    protected $fillable = [
        'household_id',
        'old_address',
        'old_purok_id',
        'new_address',
        'new_purok_id',
        'move_date',
        'reason',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'move_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function oldPurok()
    {
        return $this->belongsTo(Purok::class, 'old_purok_id');
    }

    public function newPurok()
    {
        return $this->belongsTo(Purok::class, 'new_purok_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scopes
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('move_date', '>=', now()->subDays($days));
    }

    /**
     * Get reason label
     */
    public function getReasonLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->reason));
    }
}
