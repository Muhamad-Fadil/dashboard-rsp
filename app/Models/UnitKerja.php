<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitKerja extends Model
{
    protected $table = 'unit_kerja';

    protected $fillable = [
        'nama_unit',
        'kode_unit',
        'division_id',
        'keterangan',
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function poli(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Poli::class);
    }
}