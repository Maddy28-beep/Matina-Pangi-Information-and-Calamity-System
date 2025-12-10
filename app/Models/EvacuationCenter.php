<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvacuationCenter extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'current_occupancy',
        'facilities',
    ];

    protected $casts = [
        'facilities' => 'array',
    ];
}
