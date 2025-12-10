<?php

namespace App\Models;

use App\Traits\Archivable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purok extends Model
{
    use Archivable, HasFactory;

    protected $fillable = [
        'purok_name',
        'purok_code',
        'purok_leader_name',
        'purok_leader_contact',
        'description',
        'boundaries',
        'total_households',
        'total_population',
        'created_by',
        'updated_by',
    ];

    /**
     * Relationships
     */
    public function households()
    {
        return $this->hasMany(Household::class);
    }

    public function residents()
    {
        return $this->hasManyThrough(Resident::class, Household::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Update population counts
     */
    public function updateCounts()
    {
        $this->total_households = $this->households()->approved()->count();
        $this->total_population = $this->residents()->approved()->active()->count();
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeWithCounts($query)
    {
        return $query->withCount([
            'households' => function ($q) {
                $q->where('households.approval_status', 'approved');
            },
            'residents' => function ($q) {
                $q->where('residents.approval_status', 'approved')
                    ->where('residents.status', 'active');
            },
        ]);
    }

    /**
     * Provide a friendly archive title.
     */
    protected function getArchiveTitle(): string
    {
        return $this->purok_name ?? 'Purok #'.$this->getKey();
    }
}
