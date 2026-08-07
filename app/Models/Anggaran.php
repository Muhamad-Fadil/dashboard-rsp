<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggaran extends Model
{
    protected $table = 'anggaran';

    protected $fillable = [
        'kategori_pengeluaran_id',
        'unit_kerja_id',
        'tahun',
        'bulan',
        'jumlah_anggaran',
        'keterangan',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(RealisasiAnggaran::class);
    }
}