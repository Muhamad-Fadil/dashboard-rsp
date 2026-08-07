<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'no_resep',
        'kunjungan_id',
        'dokter_id',
        'tanggal_resep',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_resep' => 'datetime',
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

    public function detail(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }
}