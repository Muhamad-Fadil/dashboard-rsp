<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laboratorium extends Model
{
    protected $table = 'laboratorium';

    protected $fillable = [
        'kunjungan_id',
        'user_id',
        'jenis_pemeriksaan',
        'waktu_periksa',
        'hasil',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'waktu_periksa' => 'datetime',
        ];
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}