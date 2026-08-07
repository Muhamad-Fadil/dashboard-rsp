<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operasi extends Model
{
    protected $table = 'operasi';

    protected $fillable = [
        'kunjungan_id',
        'dokter_id',
        'jenis_operasi',
        'ruang_operasi',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }
}