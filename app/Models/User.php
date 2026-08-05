<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',        // admin | direktur | manajer | operator
        'division_id', // null untuk admin & direktur
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    // ---- Helper cek role ----

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDirektur(): bool
    {
        return $this->role === 'direktur';
    }

    public function isManajer(): bool
    {
        return $this->role === 'manajer';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Cek apakah user ini boleh akses dashboard divisi tertentu.
     * - admin & direktur: boleh akses semua divisi
     * - manajer & operator: cuma boleh akses divisinya sendiri
     */
    public function canAccessDivision(string $divisionSlug): bool
    {
        if ($this->isAdmin() || $this->isDirektur()) {
            return true;
        }

        return $this->division && $this->division->slug === $divisionSlug;
    }

    /**
     * URL tujuan setelah login, tergantung role.
     */
    public function redirectUrl(): string
    {
        return match ($this->role) {
            'admin'    => route('admin.dashboard'),
            'direktur' => route('direktur.dashboard'),
            'manajer', 'operator' => route('divisi.dashboard', $this->division?->slug),
            default    => route('login'),
        };
    }
}
