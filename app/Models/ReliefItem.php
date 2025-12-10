<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReliefItem extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
    ];
}
