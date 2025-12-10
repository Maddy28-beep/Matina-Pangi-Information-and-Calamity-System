<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    protected $fillable = [
        'resident_id',
        'certificate_type',
        'purpose',
        'status', // pending, approved, rejected
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
