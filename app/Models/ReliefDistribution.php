<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReliefDistribution extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'relief_item_id',
        'household_id',
        'calamity_id',
        'quantity',
        'distributed_at',
        'staff_in_charge',
    ];

    protected $casts = [
        'distributed_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(ReliefItem::class, 'relief_item_id');
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_in_charge');
    }
}
