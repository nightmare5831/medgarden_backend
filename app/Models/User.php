<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'account_type',
        'seller_status',
        'seller_approved',
        'seller_requested_at',
        'seller_approved_by',
        'seller_approved_at',
        'google_id',
        'avatar_url',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'seller_approved' => 'boolean',
            'seller_requested_at' => 'datetime',
            'seller_approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Check if user is seller
     */
    public function isSeller(): bool
    {
        return $this->role === 'seller' && $this->seller_approved;
    }

    /**
     * Check if user is buyer
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is patient (new role type)
     */
    public function isPatient(): bool
    {
        return $this->account_type === 'patient' || ($this->role === 'buyer' && !$this->account_type);
    }

    /**
     * Check if user is professional (new role type)
     */
    public function isProfessional(): bool
    {
        return $this->account_type === 'professional' || ($this->role === 'seller' && !$this->account_type);
    }

    /**
     * Check if user is association (new role type)
     */
    public function isAssociation(): bool
    {
        return $this->account_type === 'association';
    }

    /**
     * Get user's display role (for frontend)
     */
    public function getDisplayRole(): string
    {
        if ($this->account_type) {
            return match ($this->account_type) {
                'patient' => 'Paciente',
                'professional' => 'Profissional',
                'association' => 'Associação',
                'store' => 'Loja',
                default => ucfirst($this->role),
            };
        }

        return match ($this->role) {
            'buyer' => 'Paciente',
            'seller' => 'Profissional',
            'admin' => 'Admin',
            'super_admin' => 'Super Admin',
            default => ucfirst($this->role),
        };
    }

    /**
     * Seller who approved this seller (if applicable)
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'seller_approved_by');
    }
}
