<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'role',
        'is_confirmed',
        'office',
        'position',
        'employee_number',
        'last_name',
        'given_name',
        'middle_name',
        'suffix',
    ];

    /**
     * Formatted display name: "Last Name, Given Name M., Suffix"
     * Falls back to the legacy `name` column if name parts are not yet filled.
     */
    public function getFullNameAttribute(): string
    {
        if (empty($this->last_name) && empty($this->given_name)) {
            return $this->name ?? '—';
        }

        $mi = $this->middle_name ? strtoupper(substr($this->middle_name, 0, 1)) . '.' : '';
        $parts = array_filter([
            trim($this->last_name . ($this->suffix ? ' ' . $this->suffix : '')),
            trim($this->given_name . ($mi ? ' ' . $mi : '')),
        ]);

        return implode(', ', $parts);
    }

    /**
     * Formatted display name for PDF: "First Name Middle Name Last Name Suffix"
     */
    public function getPdfNameAttribute(): string
    {
        if (empty($this->last_name) && empty($this->given_name)) {
            return $this->name ?? '—';
        }

        $parts = array_filter([
            trim($this->given_name),
            trim($this->middle_name),
            trim($this->last_name . ($this->suffix ? ' ' . $this->suffix : '')),
        ]);

        return implode(' ', $parts);
    }

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
        ];
    }

    // ─── Role Helpers ──────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /** Account is waiting for admin confirmation (null = never acted on). */
    public function isPending(): bool
    {
        return $this->is_confirmed === null;
    }

    /** Admin has confirmed this account — employee may log in. */
    public function isConfirmedAccount(): bool
    {
        return $this->is_confirmed === true;
    }

    /** Admin has rejected this account — employee is blocked. */
    public function isRejected(): bool
    {
        return $this->is_confirmed === false;
    }
}
