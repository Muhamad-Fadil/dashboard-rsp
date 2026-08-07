<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianKerja extends Model
{
    protected $table = 'penilaian_kerja';

    protected $fillable = [
        'pegawai_id',
        'dinilai_oleh',
        'periode_bulan',
        'periode_tahun',
        'skor_kedisiplinan',
        'skor_kualitas_kerja',
        'skor_kerjasama',
        'skor_akhir',
        'catatan',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }
}