<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referensi extends Model
{
    protected $table = 'referensi';

    protected $fillable = [
        'kategori',
        'kode',
        'nilai',
        'urutan',
    ];

    // helper: ambil semua referensi dalam 1 kategori, urut, dipakai buat isi dropdown
    public static function byKategori(string $kategori)
    {
        return self::where('kategori', $kategori)->orderBy('urutan')->get();
    }
}