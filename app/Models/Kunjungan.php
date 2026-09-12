<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = [
        'no_kunjungan',
        'pasien_id',
        'poli_id',
        'dokter_id',
        'jenis_kunjungan',
        'keluhan',
        'status',
        'waktu_daftar',
        'waktu_dilayani',
        'waktu_selesai',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'waktu_daftar' => 'datetime',
            'waktu_dilayani' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rawatInap(): HasOne
    {
        return $this->hasOne(RawatInap::class);
    }

    // helper: hitung lama tunggu dalam menit, dipakai buat indikator "waktu tunggu pelayanan"
    public function waktuTungguMenit(): ?int
    {
        if (! $this->waktu_dilayani) {
            return null;
        }

        return $this->waktu_daftar->diffInMinutes($this->waktu_dilayani);
    }
}