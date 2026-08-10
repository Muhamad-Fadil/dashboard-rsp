<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = [
        'no_rm',
        'no_registrasi',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'nik',
        'jenis_pembayaran_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function kunjungan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function jenisPembayaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Referensi::class, 'jenis_pembayaran_id');
    }

    /**
     * Status pasien SAAT INI: Rawat Inap (kalau ada rawat_inap yang masih aktif) atau Rawat Jalan.
     */
    public function statusSaatIni(): string
    {
        $sedangDirawat = $this->kunjungan()
            ->whereHas('rawatInap', fn ($q) => $q->whereNull('tanggal_keluar'))
            ->exists();

        return $sedangDirawat ? 'Rawat Inap' : 'Rawat Jalan';
    }
}