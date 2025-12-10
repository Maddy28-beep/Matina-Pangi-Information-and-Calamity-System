<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalamityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'calamity_id',
        'report_date',
        'total_casualties',
        'total_evacuated',
        'relief_used_items',
        'total_damage_cost',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_damage_cost' => 'decimal:2',
        'relief_used_items' => 'integer',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }
}
