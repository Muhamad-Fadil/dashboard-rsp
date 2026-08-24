<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WilayahBogor extends Model
{
    protected $table = 'wilayah_bogor';

    protected $fillable = [
        'kode_wilayah',
        'nama_kecamatan',
        'kabupaten_kota',
    ];

    public function pasien(): HasMany
    {
        return $this->hasMany(Pasien::class);
    }
}