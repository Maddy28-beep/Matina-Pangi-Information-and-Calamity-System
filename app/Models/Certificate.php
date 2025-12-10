<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'certificate_type',
        'purpose',
        'or_number',
        'amount_paid',
        'issued_by',
        'issued_date',
        'valid_until',
        'certificate_number',
        'qr_code',
        'status',
        'remarks',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'valid_until' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Boot method to auto-generate certificate number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = $certificate->generateCertificateNumber();
            }
        });
    }

    /**
     * Relationships
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Scopes
     */
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', 'claimed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('issued_date', now()->year);
    }

    /**
     * Generate unique certificate number
     */
    public function generateCertificateNumber()
    {
        $year = now()->year;
        $map = [
            'barangay_clearance' => 'BCL',
            'certificate_of_indigency' => 'COI',
            'certificate_of_residency' => 'COR',
            'business_clearance' => 'BUS',
            'good_moral' => 'GMR',
            'travel_permit' => 'TPR',
        ];
        $code = $map[$this->certificate_type] ?? strtoupper(str_replace('_', '', $this->certificate_type));
        $count = Certificate::whereYear('created_at', $year)
            ->where('certificate_type', $this->certificate_type)
            ->count() + 1;

        return sprintf('CERT-%s-%s-%04d', $year, $code, $count);
    }

    /**
     * Check if certificate is valid
     */
    public function isValid()
    {
        if (! $this->valid_until) {
            return true; // No expiry
        }

        return $this->valid_until->isFuture() && $this->status !== 'cancelled';
    }

    /**
     * Get certificate type label
     */
    public function getTypeLabelAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->certificate_type));
    }
}
