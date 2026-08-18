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

    public function submenuAkses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OperatorSubmenuAkses::class);
    }

    /**
     * Daftar semua sub-menu yang ada di Dashboard Layanan.
     * Kalau nanti SDM/Keuangan juga punya sub-menu bertingkat, tinggal tambah array serupa.
     */
    public static function daftarSubmenuLayanan(): array
    {
        return [
            'pasien' => 'Data Pasien',
            'kunjungan' => 'Kunjungan',
            'rawat-inap' => 'Rawat Inap',
            'operasi' => 'Operasi',
            'laboratorium' => 'Laboratorium',
            'radiologi' => 'Radiologi',
            'resep' => 'Resep',
        ];
    }

    /**
     * Mapping slug submenu -> nama route, dipakai buat redirect & link.
     */
    public static function submenuRouteMap(): array
    {
        return [
            'pasien' => 'divisi.layanan.pasien',
            'kunjungan' => 'divisi.layanan.kunjungan',
            'rawat-inap' => 'divisi.layanan.rawat-inap',
            'operasi' => 'divisi.layanan.operasi',
            'laboratorium' => 'divisi.layanan.laboratorium',
            'radiologi' => 'divisi.layanan.radiologi',
            'resep' => 'divisi.layanan.resep',
        ];
    }

    /**
     * Cek apakah user ini boleh akses sub-menu tertentu.
     * - admin, direktur, manajer: selalu boleh (mereka atasan/pengawas semua sub-menu)
     * - operator: cuma boleh ke sub-menu yang dicentang Manajer-nya
     */
    public function bisaAksesSubmenu(string $submenu): bool
    {
        if (in_array($this->role, ['admin', 'direktur', 'manajer'], true)) {
            return true;
        }

        if ($this->role === 'operator') {
            return $this->submenuAkses()->where('submenu', $submenu)->exists();
        }

        return false;
    }

    /**
     * Daftar slug submenu yang boleh diakses operator ini (buat filter tampilan menu).
     */
    public function daftarSubmenuDiizinkan(): array
    {
        if (in_array($this->role, ['admin', 'direktur', 'manajer'], true)) {
            return array_keys(self::daftarSubmenuLayanan());
        }

        return $this->submenuAkses()->pluck('submenu')->all();
    }

    /**
     * URL tujuan setelah login, tergantung role.
     */
public function redirectUrl(): string
    {
        return match ($this->role) {
            'admin'    => route('admin.dashboard'),
            'direktur' => route('direktur.dashboard'),
            'manajer'  => route('divisi.dashboard', $this->division?->slug),
            'operator' => $this->redirectUrlOperator(),
            default    => route('login'),
        };
    }

    /**
     * Operator diarahkan langsung ke sub-menu pertama yang diizinkan
     * (bukan ke Ringkasan, karena Ringkasan nampilin data gabungan semua sub-menu).
     */
    protected function redirectUrlOperator(): string
    {
        if ($this->division?->slug !== 'layanan') {
            // SDM & Keuangan belum punya sistem sub-menu, tetap ke dashboard biasa
            return route('divisi.dashboard', $this->division?->slug);
        }

        $submenuPertama = $this->submenuAkses()->first();

        if (! $submenuPertama) {
            // belum dikasih akses submenu apapun sama Manajer-nya
            return route('login');
        }

        $routeName = self::submenuRouteMap()[$submenuPertama->submenu] ?? 'login';

        return route($routeName, $this->division->slug);
    }
}
