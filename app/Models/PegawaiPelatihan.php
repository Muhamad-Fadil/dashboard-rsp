<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PegawaiPelatihan extends Model
{
    protected $table = 'pegawai_pelatihan';

    protected $fillable = [
        'pegawai_id',
        'pelatihan_id',
        'status',
        'sertifikat',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pelatihan(): BelongsTo
    {
        return $this->belongsTo(Pelatihan::class);
    }
}