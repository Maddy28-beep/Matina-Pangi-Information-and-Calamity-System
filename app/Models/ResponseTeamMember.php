<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ResponseTeamMember extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'name',
        'role',
        'skills',
        'calamity_id',
        'evacuation_center_id',
        'assignment_notes',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function calamity()
    {
        return $this->belongsTo(Calamity::class);
    }

    public function evacuationCenter()
    {
        return $this->belongsTo(EvacuationCenter::class);
    }

    public function rescueOperations()
    {
        return $this->hasMany(RescueOperation::class, 'rescuer_id');
    }

    public function setAssignmentNotesAttribute($value)
    {
        $this->attributes['assignment_notes'] = $value !== null ? Crypt::encryptString((string) $value) : null;
    }

    public function getAssignmentNotesAttribute($value)
    {
        if (! $value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
