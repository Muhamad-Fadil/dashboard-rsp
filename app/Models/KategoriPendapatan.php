<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPendapatan extends Model
{
    protected $table = 'kategori_pendapatan';

    protected $fillable = [
        'nama_kategori',
        'kode',
    ];
}