<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawatInap extends Model
{
    protected $table = 'rawat_inap';

    protected $fillable = [
        'kunjungan_id',
        'bed_id',
        'dokter_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'diagnosa',
        'catatan_keluar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'datetime',
            'tanggal_keluar' => 'datetime',
        ];
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    // helper: lama rawat dalam hari — dipakai langsung buat hitung ALOS
    public function lamaRawatHari(): ?int
    {
        if (! $this->tanggal_keluar) {
            return null; // masih dirawat, belum bisa dihitung
        }

        return $this->tanggal_masuk->diffInDays($this->tanggal_keluar);
    }
}