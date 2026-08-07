<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Piutang extends Model
{
    protected $table = 'piutang';

    protected $fillable = [
        'pasien_id',
        'kunjungan_id',
        'jumlah_tagihan',
        'jumlah_terbayar',
        'tanggal_tagihan',
        'jatuh_tempo',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_tagihan' => 'date',
            'jatuh_tempo' => 'date',
        ];
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    // helper: sisa piutang yang belum dibayar — dipakai langsung di indikator "piutang"
    public function sisaPiutang(): float
    {
        return (float) $this->jumlah_tagihan - (float) $this->jumlah_terbayar;
    }
}