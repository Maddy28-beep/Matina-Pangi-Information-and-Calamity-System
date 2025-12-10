<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'assigned_app',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is secretary (full access)
     */
    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    /**
     * Check if user is staff (limited access)
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCalamityHead(): bool
    {
        return $this->role === 'calamity_head';
    }

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function canAccessApp(string $app): bool
    {
        return ($this->assigned_app ?? null) === $app;
    }

    /**
     * Residents created by this user
     */
    public function createdResidents()
    {
        return $this->hasMany(Resident::class, 'created_by');
    }

    /**
     * Residents updated by this user
     */
    public function updatedResidents()
    {
        return $this->hasMany(Resident::class, 'updated_by');
    }

    /**
     * Audit logs for this user
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
